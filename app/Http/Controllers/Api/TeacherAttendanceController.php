<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\Schedule;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TeacherAttendanceController extends Controller
{
    // ═══════════════════════════════════════════════════════════
    // SESSIONS
    // ═══════════════════════════════════════════════════════════

    /**
     * List upcoming sessions for the teacher
     * GET /api/teacher/attendance/sessions
     */
    public function sessions(Request $request): JsonResponse
    {
        $teacher = $request->user()->teacher;

        if (!$teacher) {
            return response()->json(['message' => 'Profil enseignant non trouvé'], 404);
        }

        // Determine date range
        $startDate = now();
        $endDate = now()->addDays(14); // Default to 2 weeks

        if ($request->date) {
            $startDate = Carbon::parse($request->date);
            $endDate = Carbon::parse($request->date);
        } elseif ($request->from && $request->to) {
            $startDate = Carbon::parse($request->from);
            $endDate = Carbon::parse($request->to);
        }

        // Get recurring schedules for teacher's assigned modules
        $query = Schedule::whereHas('module', function ($q) use ($teacher) {
                // Ensure module is assigned to teacher
                $q->whereHas('teachers', function ($t) use ($teacher) {
                    $t->where('teachers.id', $teacher->id);
                });
            })
            ->with(['module', 'lesson']); // Assuming lesson relationship exists on Schedule

        if ($request->module_id) {
            $query->where('module_id', $request->module_id);
        }

        $schedules = $query->get();

        // Expand recurring schedules into sessions
        $sessions = collect();
        $dayMap = [
            'monday' => Carbon::MONDAY,
            'tuesday' => Carbon::TUESDAY,
            'wednesday' => Carbon::WEDNESDAY,
            'thursday' => Carbon::THURSDAY,
            'friday' => Carbon::FRIDAY,
            'saturday' => Carbon::SATURDAY,
            'sunday' => Carbon::SUNDAY,
        ];

        // Iterate through each day in the range
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dayName = strtolower($date->locale('en')->isoFormat('dddd'));

            // Find schedules for this day
            $daySchedules = $schedules->filter(function ($schedule) use ($dayName) {
                return strtolower($schedule->day) === $dayName;
            });

            foreach ($daySchedules as $schedule) {
                // Check if attendance is already taken for this session
                $attendanceTaken = Attendance::where('schedule_id', $schedule->id)
                    ->whereDate('attendance_date', $date->format('Y-m-d'))
                    ->exists();

                $sessions->push([
                    'id' => $schedule->id,
                    'date' => $date->format('Y-m-d'),
                    'day_name' => $date->locale('en')->isoFormat('dddd'),
                    'start_time' => Carbon::parse($schedule->start_time)->format('H:i'),
                    'end_time' => Carbon::parse($schedule->start_time)->addHours(1)->format('H:i'), // Assuming 1h
                    'room' => $schedule->classroom,
                    'module' => [
                        'id' => $schedule->module->id,
                        'code' => $schedule->module->code,
                        'name' => $schedule->module->name,
                    ],
                    'lesson' => $schedule->lesson ? [
                        'id' => $schedule->lesson->id,
                        'title' => $schedule->lesson->title,
                    ] : null,
                    'attendance_taken' => $attendanceTaken,
                ]);
            }
        }

        // Sort by date and time
        $sortedSessions = $sessions->sort(function ($a, $b) {
            if ($a['date'] === $b['date']) {
                return strcmp($a['start_time'], $b['start_time']);
            }
            return strcmp($a['date'], $b['date']);
        })->values();

        return response()->json([
            'data' => $sortedSessions
        ]);
    }

    /**
     * Get students of a session
     * GET /api/teacher/attendance/sessions/{schedule}
     */
    public function sessionStudents(Request $request, Schedule $schedule): JsonResponse
    {
        $teacher = $request->user()->teacher;

        if (!$teacher) {
            return response()->json(['message' => 'Profil enseignant non trouvé'], 404);
        }

        // Validate date param
        $request->validate([
            'date' => 'required|date',
        ]);
        $date = $request->date;

        // Authorize: Check if teacher is assigned to the module of this schedule
        $isAssigned = $teacher->modules()->where('modules.id', $schedule->module_id)->exists();
        if (!$isAssigned) {
            return response()->json(['message' => 'Non autorisé. Vous n\'êtes pas assigné à ce module.'], 403);
        }

        $module = $schedule->module;

        // Get students enrolled in the module (via specialty and semester)
        $students = Student::where('specialty_id', $module->specialty_id)
            ->where('current_semester', $module->semester)
            ->with('user') // Load user for email/phone if needed
            ->get()
            ->map(function ($student) use ($schedule, $date) {
                // Get existing attendance status
                $attendance = Attendance::where('student_id', $student->id)
                    ->where('schedule_id', $schedule->id)
                    ->whereDate('attendance_date', $date)
                    ->first();

                return [
                    'id' => $student->id,
                    'registration_number' => $student->registration_number,
                    'full_name' => $student->full_name,
                    'email' => $student->user->email ?? null,
                    'current_status' => $attendance ? $attendance->status : null,
                    'note' => $attendance ? $attendance->notes : null,
                ];
            });

        return response()->json([
            'schedule' => [
                'id' => $schedule->id,
                'date' => $date,
                'start_time' => Carbon::parse($schedule->start_time)->format('H:i'),
                'end_time' => Carbon::parse($schedule->start_time)->addHours(1)->format('H:i'),
                'room' => $schedule->classroom,
                'module' => [
                    'id' => $module->id,
                    'code' => $module->code,
                    'name' => $module->name,
                ]
            ],
            'students' => $students
        ]);
    }

    /**
     * Save/update attendance for a session
     * POST /api/teacher/attendance/sessions/{schedule}
     */
    public function store(Request $request, Schedule $schedule): JsonResponse
    {
        $teacher = $request->user()->teacher;

        if (!$teacher) {
            return response()->json(['message' => 'Profil enseignant non trouvé'], 404);
        }

        // Authorize
        $isAssigned = $teacher->modules()->where('modules.id', $schedule->module_id)->exists();
        if (!$isAssigned) {
            return response()->json(['message' => 'Non autorisé. Vous n\'êtes pas assigné à ce module.'], 403);
        }

        // Validate
        $validated = $request->validate([
            'date' => 'required|date',
            'attendances' => 'required|array|min:1',
            'attendances.*.student_id' => 'required|exists:students,id',
            'attendances.*.status' => 'required|in:present,absent,late,excused',
            'attendances.*.note' => 'nullable|string|max:255',
        ]);

        $date = $validated['date'];
        $module = $schedule->module;

        // Verify students belong to the module (optional but good practice)
        // We can skip strict verification for performance or do a quick check

        DB::transaction(function () use ($validated, $schedule, $teacher, $date) {
            foreach ($validated['attendances'] as $data) {
                Attendance::updateOrCreate(
                    [
                        'student_id' => $data['student_id'],
                        'schedule_id' => $schedule->id,
                        'attendance_date' => $date,
                    ],
                    [
                        'teacher_id' => $teacher->id,
                        'status' => $data['status'],
                        'notes' => $data['note'] ?? null,
                    ]
                );
            }
        });

        // Return updated list
        return $this->sessionStudents($request, $schedule);
    }

    // ═══════════════════════════════════════════════════════════
    // HISTORY
    // ═══════════════════════════════════════════════════════════

    /**
     * Teacher attendance history
     * GET /api/teacher/attendance/history
     */
    public function history(Request $request): JsonResponse
    {
        $teacher = $request->user()->teacher;

        if (!$teacher) {
            return response()->json(['message' => 'Profil enseignant non trouvé'], 404);
        }

        $query = Attendance::where('teacher_id', $teacher->id)
            ->join('schedules', 'attendances.schedule_id', '=', 'schedules.id')
            ->join('modules', 'schedules.module_id', '=', 'modules.id')
            ->select(
                'attendances.schedule_id',
                'attendances.attendance_date',
                'modules.id as module_id',
                'modules.code as module_code',
                'modules.name as module_name',
                'schedules.start_time',
                'schedules.classroom'
            )
            ->groupBy(
                'attendances.schedule_id',
                'attendances.attendance_date',
                'modules.id',
                'modules.code',
                'modules.name',
                'schedules.start_time',
                'schedules.classroom'
            )
            ->orderBy('attendances.attendance_date', 'desc');

        // Filters
        if ($request->from) {
            $query->whereDate('attendances.attendance_date', '>=', $request->from);
        }
        if ($request->to) {
            $query->whereDate('attendances.attendance_date', '<=', $request->to);
        }
        if ($request->module_id) {
            $query->where('modules.id', $request->module_id);
        }

        // Pagination
        $perPage = $request->input('per_page', 10);
        $history = $query->paginate($perPage);

        // Transform to include counts
        $data = $history->getCollection()->map(function ($item) {
            // Count statuses for this session
            $counts = Attendance::where('schedule_id', $item->schedule_id)
                ->where('attendance_date', $item->attendance_date)
                ->select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->pluck('total', 'status')
                ->toArray();

            return [
                'schedule_id' => $item->schedule_id,
                'date' => $item->attendance_date, // It's already a string from DB usually, or Carbon if cast
                'module' => [
                    'id' => $item->module_id,
                    'code' => $item->module_code,
                    'name' => $item->module_name,
                ],
                'present_count' => $counts['present'] ?? 0,
                'absent_count' => $counts['absent'] ?? 0,
                'late_count' => $counts['late'] ?? 0,
                'excused_count' => $counts['excused'] ?? 0,
            ];
        });

        return response()->json([
            'data' => $data,
            'meta' => [
                'current_page' => $history->currentPage(),
                'per_page' => $history->perPage(),
                'total' => $history->total(),
                'last_page' => $history->lastPage(),
            ],
        ]);
    }
}
