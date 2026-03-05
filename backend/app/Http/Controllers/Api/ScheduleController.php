<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\Specialty;
use App\Models\Module;
use App\Models\Teacher;
use App\Models\TrainingSession;
use App\Models\SessionSpecialty;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScheduleController extends Controller
{
    // ─── Helpers ─────────────────────────────────────────────────────────────

    private function formatSchedule($schedule): array
    {
        return [
            'id'            => $schedule->id,
            'session_id'    => $schedule->session_id,
            'module'        => ['id' => $schedule->module->id, 'name' => $schedule->module->name, 'code' => $schedule->module->code],
            'teacher'       => ['id' => $schedule->teacher->id, 'full_name' => $schedule->teacher->full_name],
            'specialty'     => ['id' => $schedule->specialty->id, 'name' => $schedule->specialty->name, 'code' => $schedule->specialty->code],
            'group'         => $schedule->group,
            'day'           => $schedule->day,
            'start_time'    => substr($schedule->start_time, 0, 5),
            'end_time'      => substr($schedule->end_time, 0, 5),
            'classroom'     => $schedule->classroom,
            'semester'      => $schedule->semester,
            'study_mode'    => $schedule->study_mode,
            'academic_year' => $schedule->academic_year,
        ];
    }

    private function academicYearFromSession(TrainingSession $session): string
    {
        if ($session->month >= 9) {
            return $session->year . '-' . ($session->year + 1);
        }
        return ($session->year - 1) . '-' . $session->year;
    }

    private function checkTeacherConflict($teacherId, $day, $startTime, $endTime, $academicYear, $excludeId = null): ?array
    {
        $query = Schedule::where('teacher_id', $teacherId)
            ->where('day', $day)
            ->where('academic_year', $academicYear)
            ->where(function ($q) use ($startTime, $endTime) {
                $q->where('start_time', '<', $endTime)
                  ->where('end_time', '>', $startTime);
            });

        if ($excludeId) $query->where('id', '!=', $excludeId);

        $conflict = $query->with(['specialty', 'module'])->first();
        if ($conflict) {
            return [
                'specialty' => $conflict->specialty->name,
                'module'    => $conflict->module->name,
                'time'      => substr($conflict->start_time, 0, 5) . ' - ' . substr($conflict->end_time, 0, 5),
            ];
        }
        return null;
    }

    private function checkSpecialtyConflict($specialtyId, $semester, $studyMode, $group, $day, $startTime, $endTime, $academicYear, $excludeId = null): ?array
    {
        $query = Schedule::where('specialty_id', $specialtyId)
            ->where('semester', $semester)
            ->where('study_mode', $studyMode)
            ->where('day', $day)
            ->where('academic_year', $academicYear)
            ->where(function ($q) use ($startTime, $endTime) {
                $q->where('start_time', '<', $endTime)
                  ->where('end_time', '>', $startTime);
            });

        if ($group) {
            $query->where(function ($q) use ($group) {
                $q->where('group', $group)->orWhereNull('group');
            });
        } else {
            $query->whereNull('group');
        }

        if ($excludeId) $query->where('id', '!=', $excludeId);

        $conflict = $query->with(['module'])->first();
        if ($conflict) {
            return [
                'module' => $conflict->module->name,
                'group'  => $conflict->group,
                'time'   => substr($conflict->start_time, 0, 5) . ' - ' . substr($conflict->end_time, 0, 5),
            ];
        }
        return null;
    }

    // ─── CRUD ────────────────────────────────────────────────────────────────

    /**
     * GET /admin/schedules
     */
    public function index(Request $request): JsonResponse
    {
        $query = Schedule::with(['module', 'teacher', 'specialty']);

        if ($request->session_id) {
            $query->where('session_id', $request->session_id);
        } elseif ($request->academic_year) {
            $query->where('academic_year', $request->academic_year);
        }

        if ($request->specialty_id)  $query->where('specialty_id', $request->specialty_id);
        if ($request->semester)       $query->where('semester', $request->semester);
        if ($request->study_mode)     $query->where('study_mode', $request->study_mode);
        if ($request->teacher_id)     $query->where('teacher_id', $request->teacher_id);
        if ($request->day)            $query->where('day', $request->day);

        // group filter: empty string means no-group rows
        if ($request->has('group') && $request->group !== '') {
            $query->where('group', $request->group);
        }

        $schedules = $query->orderBy('day')->orderBy('start_time')->get()
            ->map(fn($s) => $this->formatSchedule($s));

        return response()->json(['schedules' => $schedules, 'count' => $schedules->count()]);
    }

    /**
     * GET /admin/schedules/{id}
     */
    public function show($id): JsonResponse
    {
        $schedule = Schedule::with(['module', 'teacher', 'specialty'])->findOrFail($id);
        return response()->json(['schedule' => $this->formatSchedule($schedule)]);
    }

    /**
     * POST /admin/schedules
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'module_id'     => 'required|exists:modules,id',
            'teacher_id'    => 'required|exists:teachers,id',
            'specialty_id'  => 'required|exists:specialties,id',
            'study_mode'    => 'required|in:initial,alternance,continue',
            'semester'      => 'required|integer|min:1|max:6',
            'group'         => 'nullable|string|max:10',
            'day'           => 'required|in:saturday,sunday,monday,tuesday,wednesday,thursday',
            'start_time'    => 'required|date_format:H:i',
            'end_time'      => 'required|date_format:H:i|after:start_time',
            'classroom'     => 'nullable|string|max:50',
            'academic_year' => 'required|string|max:9',
            'session_id'    => 'nullable|exists:sessions,id',
        ]);

        // ── Teacher conflict ──────────────────────────────────────────────────
        $teacherConflict = $this->checkTeacherConflict(
            $validated['teacher_id'], $validated['day'],
            $validated['start_time'], $validated['end_time'], $validated['academic_year']
        );
        if ($teacherConflict) {
            return response()->json([
                'message'  => 'Conflit enseignant : cet enseignant a déjà un cours à ce créneau.',
                'conflict' => $teacherConflict,
            ], 422);
        }

        // ── Specialty/group conflict ──────────────────────────────────────────
        $specialtyConflict = $this->checkSpecialtyConflict(
            $validated['specialty_id'], $validated['semester'],
            $validated['study_mode'],
            $validated['group'] ?? null, $validated['day'],
            $validated['start_time'], $validated['end_time'], $validated['academic_year']
        );
        if ($specialtyConflict) {
            return response()->json([
                'message'  => 'Conflit emploi du temps : ce groupe a déjà un cours à ce créneau.',
                'conflict' => $specialtyConflict,
            ], 422);
        }

        $schedule = Schedule::create($validated);
        $schedule->load(['module', 'teacher', 'specialty']);

        return response()->json([
            'message'  => 'Séance ajoutée avec succès.',
            'schedule' => $this->formatSchedule($schedule),
        ], 201);
    }

    /**
     * PUT /admin/schedules/{id}
     */
    public function update(Request $request, $id): JsonResponse
    {
        $schedule = Schedule::findOrFail($id);

        $validated = $request->validate([
            'module_id'     => 'sometimes|exists:modules,id',
            'teacher_id'    => 'sometimes|exists:teachers,id',
            'specialty_id'  => 'sometimes|exists:specialties,id',
            'study_mode'    => 'sometimes|in:initial,alternance,continue',
            'semester'      => 'sometimes|integer|min:1|max:6',
            'group'         => 'sometimes|nullable|string|max:10',
            'day'           => 'sometimes|in:saturday,sunday,monday,tuesday,wednesday,thursday',
            'start_time'    => 'sometimes|date_format:H:i',
            'end_time'      => 'sometimes|date_format:H:i|after:start_time',
            'classroom'     => 'sometimes|nullable|string|max:50',
            'academic_year' => 'sometimes|string|max:9',
            'session_id'    => 'sometimes|nullable|exists:sessions,id',
        ]);

        $day        = $validated['day']          ?? $schedule->day;
        $start      = $validated['start_time']   ?? substr($schedule->start_time, 0, 5);
        $end        = $validated['end_time']      ?? substr($schedule->end_time, 0, 5);
        $teacherId  = $validated['teacher_id']   ?? $schedule->teacher_id;
        $specialtyId= $validated['specialty_id'] ?? $schedule->specialty_id;
        $semester   = $validated['semester']     ?? $schedule->semester;
        $group      = array_key_exists('group', $validated) ? $validated['group'] : $schedule->group;
        $year       = $validated['academic_year']?? $schedule->academic_year;

        $teacherConflict = $this->checkTeacherConflict($teacherId, $day, $start, $end, $year, $id);
        if ($teacherConflict) {
            return response()->json([
                'message'  => 'Conflit enseignant : cet enseignant a déjà un cours à ce créneau.',
                'conflict' => $teacherConflict,
            ], 422);
        }

        $studyMode  = $validated['study_mode']     ?? $schedule->study_mode;
        $specialtyConflict = $this->checkSpecialtyConflict($specialtyId, $semester, $studyMode, $group, $day, $start, $end, $year, $id);
        if ($specialtyConflict) {
            return response()->json([
                'message'  => 'Conflit emploi du temps : ce groupe a déjà un cours à ce créneau.',
                'conflict' => $specialtyConflict,
            ], 422);
        }

        $schedule->update($validated);
        $schedule->load(['module', 'teacher', 'specialty']);

        return response()->json([
            'message'  => 'Séance mise à jour avec succès.',
            'schedule' => $this->formatSchedule($schedule),
        ]);
    }

    /**
     * DELETE /admin/schedules/{id}
     */
    public function destroy($id): JsonResponse
    {
        Schedule::findOrFail($id)->delete();
        return response()->json(['message' => 'Séance supprimée.']);
    }

    // ─── Session-based management ────────────────────────────────────────────

    /**
     * GET /admin/schedules/sessions
     */
    public function getSessions(): JsonResponse
    {
        $sessions = TrainingSession::orderBy('year', 'desc')->orderBy('month', 'desc')->get()
            ->map(fn($s) => [
                'id'            => $s->id,
                'name'          => $s->name,
                'is_active'     => $s->is_active,
                'status'        => $s->status,
                'start_date'    => $s->start_date,
                'academic_year' => $this->academicYearFromSession($s),
            ]);

        return response()->json([
            'sessions' => $sessions,
            'current'  => $sessions->firstWhere('is_active', true),
            'archives' => $sessions->filter(fn($s) => !$s['is_active'])->values(),
        ]);
    }

    /**
     * GET /admin/schedules/sessions/{sessionId}/specialties
     */
    public function getSessionSpecialties(Request $request, $sessionId): JsonResponse
    {
        $session = TrainingSession::findOrFail($sessionId);

        // Get specialty IDs belonging to this session
        $sessionSpecialtyIds = SessionSpecialty::where('session_id', $sessionId)->pluck('specialty_id');

        $combinations = Student::select(
                'specialty_id', 'study_mode', 'current_semester',
                DB::raw('COUNT(*) as students_count'),
                DB::raw('GROUP_CONCAT(DISTINCT COALESCE(`group`, "") ORDER BY `group` SEPARATOR ",") as groups_list')
            )
            ->where('is_graduated', false)
            ->whereIn('specialty_id', $sessionSpecialtyIds)
            ->whereNotNull('specialty_id')
            ->whereNotNull('current_semester')
            ->groupBy('specialty_id', 'study_mode', 'current_semester')
            ->get();

        $specialtyIds = $combinations->pluck('specialty_id')->unique();
        $specialties  = Specialty::whereIn('id', $specialtyIds)->get()->keyBy('id');

        $statuses = DB::table('schedule_statuses')
            ->where('session_id', $sessionId)
            ->get()
            ->groupBy('specialty_id');

        $result = $combinations->map(function ($item) use ($specialties, $statuses, $sessionId) {
            $specialty    = $specialties->get($item->specialty_id);
            $groups       = $item->groups_list
                ? array_values(array_filter(explode(',', $item->groups_list), fn($g) => $g !== ''))
                : [];

            $specStatuses = $statuses->get($item->specialty_id, collect())
                ->where('semester', $item->current_semester)
                ->where('study_mode', $item->study_mode);

            if (count($groups) > 0) {
                $publishedGroups = $specStatuses->pluck('group')->toArray();
                $allPublished    = count(array_diff($groups, $publishedGroups)) === 0;
                $somePublished   = $specStatuses->isNotEmpty();
            } else {
                $nullStatus    = $specStatuses->firstWhere('group', null);
                $allPublished  = $nullStatus && $nullStatus->is_published;
                $somePublished = $allPublished;
            }

            $schedulesCount = Schedule::where('session_id', $sessionId)
                ->where('specialty_id', $item->specialty_id)
                ->where('semester', $item->current_semester)
                ->where('study_mode', $item->study_mode)
                ->count();

            return [
                'id'             => $item->specialty_id . '_' . $item->study_mode . '_' . $item->current_semester,
                'specialty_id'   => $item->specialty_id,
                'specialty_name' => $specialty?->name ?? 'Unknown',
                'specialty_code' => $specialty?->code ?? '',
                'study_mode'     => $item->study_mode,
                'semester'       => $item->current_semester,
                'students_count' => $item->students_count,
                'groups'         => $groups,
                'groups_count'   => count($groups),
                'schedules_count'=> $schedulesCount,
                'is_published'   => $allPublished,
                'is_partial'     => $somePublished && !$allPublished,
            ];
        })
        ->sortBy([['study_mode', 'asc'], ['specialty_name', 'asc'], ['semester', 'asc']])
        ->values();

        return response()->json([
            'session'      => [
                'id'            => $session->id,
                'name'          => $session->name,
                'is_active'     => $session->is_active,
                'status'        => $session->status,
                'academic_year' => $this->academicYearFromSession($session),
            ],
            'specialties'  => $result,
            'all_published' => $result->every(fn($s) => $s['is_published']),
            'count'        => $result->count(),
        ]);
    }

    /**
     * POST /admin/schedules/sessions/{sessionId}/publish
     */
    public function publishSpecialty(Request $request, $sessionId): JsonResponse
    {
        TrainingSession::findOrFail($sessionId);
        $request->validate([
            'specialty_id' => 'required|exists:specialties,id',
            'semester'     => 'required|integer|min:1|max:6',
            'study_mode'   => 'required|string|in:initial,alternance,continue',
            'group'        => 'nullable|string|max:10',
        ]);

        DB::table('schedule_statuses')->updateOrInsert(
            [
                'session_id'   => $sessionId,
                'specialty_id' => $request->specialty_id,
                'semester'     => $request->semester,
                'study_mode'   => $request->study_mode,
                'group'        => $request->group ?? null,
            ],
            ['is_published' => true, 'updated_at' => now(), 'created_at' => now()]
        );

        return response()->json(['message' => 'Emploi du temps finalisé.']);
    }

    /**
     * POST /admin/schedules/sessions/{sessionId}/unpublish
     */
    public function unpublishSpecialty(Request $request, $sessionId): JsonResponse
    {
        $request->validate([
            'specialty_id' => 'required|exists:specialties,id',
            'semester'     => 'required|integer|min:1|max:6',
            'study_mode'   => 'required|string|in:initial,alternance,continue',
            'group'        => 'nullable|string|max:10',
        ]);

        DB::table('schedule_statuses')
            ->where('session_id', $sessionId)
            ->where('specialty_id', $request->specialty_id)
            ->where('semester', $request->semester)
            ->where('study_mode', $request->study_mode)
            ->where('group', $request->group ?? null)
            ->update(['is_published' => false]);

        return response()->json(['message' => 'Emploi du temps rouvert pour modification.']);
    }

    // ─── Legacy ──────────────────────────────────────────────────────────────

    public function getGroups(Request $request): JsonResponse
    {
        if (!$request->specialty_id) {
            return response()->json(['message' => 'specialty_id is required'], 400);
        }
        $groups = Student::where('specialty_id', $request->specialty_id)
            ->whereNotNull('group')->distinct()->pluck('group')->filter()->values();
        return response()->json(['groups' => $groups]);
    }

    public function getSpecialtySemesters(Request $request): JsonResponse
    {
        $combinations = Student::select(
                'specialty_id', 'study_mode', 'current_semester',
                DB::raw('COUNT(*) as students_count'),
                DB::raw('GROUP_CONCAT(DISTINCT COALESCE(`group`, "") SEPARATOR ",") as groups_list')
            )
            ->where('is_graduated', false)
            ->whereNotNull('specialty_id')->whereNotNull('current_semester')
            ->groupBy('specialty_id', 'study_mode', 'current_semester')
            ->get();

        $specialtyIds = $combinations->pluck('specialty_id')->unique();
        $specialties  = Specialty::whereIn('id', $specialtyIds)->get()->keyBy('id');

        $result = $combinations->map(function ($item) use ($specialties) {
            $specialty = $specialties->get($item->specialty_id);
            $groups    = $item->groups_list
                ? array_values(array_filter(explode(',', $item->groups_list), fn($g) => $g !== ''))
                : [];
            return [
                'id'             => $item->specialty_id . '_' . $item->study_mode . '_' . $item->current_semester,
                'specialty_id'   => $item->specialty_id,
                'specialty_name' => $specialty?->name ?? 'Unknown',
                'specialty_code' => $specialty?->code ?? '',
                'study_mode'     => $item->study_mode,
                'semester'       => $item->current_semester,
                'students_count' => $item->students_count,
                'groups'         => $groups,
                'groups_count'   => count($groups),
            ];
        })->sortBy([['study_mode', 'asc'], ['specialty_name', 'asc'], ['semester', 'asc']])->values();

        return response()->json(['specialty_semesters' => $result, 'count' => $result->count()]);
    }
}
