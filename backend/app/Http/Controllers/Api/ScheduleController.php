<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\Specialty;
use App\Models\Module;
use App\Models\Teacher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    /**
     * Get schedules with filters
     */
    public function index(Request $request): JsonResponse
    {
        $query = Schedule::with(['module', 'teacher', 'specialty']);

        if ($request->specialty_id) {
            $query->where('specialty_id', $request->specialty_id);
        }

        if ($request->group) {
            $query->where('group', $request->group);
        }

        if ($request->teacher_id) {
            $query->where('teacher_id', $request->teacher_id);
        }

        if ($request->day) {
            $query->where('day', $request->day);
        }

        if ($request->academic_year) {
            $query->where('academic_year', $request->academic_year);
        }

        $schedules = $query->orderBy('day')->orderBy('start_time')->get()->map(function($schedule) {
            return [
                'id' => $schedule->id,
                'module' => [
                    'id' => $schedule->module->id,
                    'name' => $schedule->module->name,
                    'code' => $schedule->module->code,
                ],
                'teacher' => [
                    'id' => $schedule->teacher->id,
                    'full_name' => $schedule->teacher->full_name,
                ],
                'specialty' => [
                    'id' => $schedule->specialty->id,
                    'name' => $schedule->specialty->name,
                    'code' => $schedule->specialty->code,
                ],
                'group' => $schedule->group,
                'day' => $schedule->day,
                'start_time' => $schedule->start_time,
                'end_time' => $schedule->end_time,
                'classroom' => $schedule->classroom,
                'semester' => $schedule->semester,
                'academic_year' => $schedule->academic_year,
            ];
        });

        return response()->json([
            'schedules' => $schedules,
            'count' => $schedules->count(),
        ]);
    }

    /**
     * Get single schedule
     */
    public function show($id): JsonResponse
    {
        $schedule = Schedule::with(['module', 'teacher', 'specialty'])->findOrFail($id);

        return response()->json([
            'schedule' => [
                'id' => $schedule->id,
                'module' => [
                    'id' => $schedule->module->id,
                    'name' => $schedule->module->name,
                    'code' => $schedule->module->code,
                ],
                'teacher' => [
                    'id' => $schedule->teacher->id,
                    'full_name' => $schedule->teacher->full_name,
                ],
                'specialty' => [
                    'id' => $schedule->specialty->id,
                    'name' => $schedule->specialty->name,
                    'code' => $schedule->specialty->code,
                ],
                'group' => $schedule->group,
                'study_mode' => $schedule->study_mode,
                'day' => $schedule->day,
                'start_time' => $schedule->start_time,
                'end_time' => $schedule->end_time,
                'classroom' => $schedule->classroom,
                'semester' => $schedule->module->semester,
                'academic_year' => $schedule->academic_year,
            ],
        ]);
    }

    /**
     * Create schedule
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'module_id' => 'required|exists:modules,id',
            'teacher_id' => 'required|exists:teachers,id',
            'specialty_id' => 'required|exists:specialties,id',
            'study_mode' => 'required|in:presencial,apprentissage,cours_de_soir',
            'group' => 'nullable|string|max:10',
            'day' => 'required|in:saturday,sunday,monday,tuesday,wednesday,thursday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'classroom' => 'nullable|string|max:50',
            'academic_year' => 'required|string|max:9',
        ]);

        // Check for conflicts
        $conflict = Schedule::where('day', $validated['day'])
            ->where('specialty_id', $validated['specialty_id'])
            ->where('academic_year', $validated['academic_year'])
            ->where(function($query) use ($validated) {
                $query->whereBetween('start_time', [$validated['start_time'], $validated['end_time']])
                      ->orWhereBetween('end_time', [$validated['start_time'], $validated['end_time']])
                      ->orWhere(function($q) use ($validated) {
                          $q->where('start_time', '<=', $validated['start_time'])
                            ->where('end_time', '>=', $validated['end_time']);
                      });
            });

        if (isset($validated['group'])) {
            $conflict->where(function($query) use ($validated) {
                $query->where('group', $validated['group'])
                      ->orWhereNull('group');
            });
        } else {
            // If no group specified, check all groups
            $conflict->whereNull('group');
        }

        if ($conflict->exists()) {
            return response()->json([
                'message' => 'Schedule conflict detected for this time slot',
            ], 422);
        }

        $schedule = Schedule::create($validated);

        return response()->json([
            'message' => 'Schedule created successfully',
            'schedule' => [
                'id' => $schedule->id,
                'module' => $schedule->module->name,
                'teacher' => $schedule->teacher->full_name,
                'day' => $schedule->day,
                'time' => $schedule->start_time . ' - ' . $schedule->end_time,
            ],
        ], 201);
    }

    /**
     * Update schedule
     */
    public function update(Request $request, $id): JsonResponse
    {
        $schedule = Schedule::findOrFail($id);

        $validated = $request->validate([
            'module_id' => 'sometimes|exists:modules,id',
            'teacher_id' => 'sometimes|exists:teachers,id',
            'specialty_id' => 'sometimes|exists:specialties,id',
            'study_mode' => 'sometimes|in:presencial,apprentissage,cours_de_soir',
            'group' => 'sometimes|nullable|string|max:10',
            'day' => 'sometimes|in:saturday,sunday,monday,tuesday,wednesday,thursday',
            'start_time' => 'sometimes|date_format:H:i',
            'end_time' => 'sometimes|date_format:H:i|after:start_time',
            'classroom' => 'sometimes|nullable|string|max:50',
            'academic_year' => 'sometimes|string|max:9',
        ]);

        // Check for conflicts (excluding current schedule)
        if (isset($validated['day']) || isset($validated['start_time']) || isset($validated['end_time'])) {
            $day = $validated['day'] ?? $schedule->day;
            $start = $validated['start_time'] ?? $schedule->start_time;
            $end = $validated['end_time'] ?? $schedule->end_time;
            $specialty_id = $validated['specialty_id'] ?? $schedule->specialty_id;
            $year = $validated['academic_year'] ?? $schedule->academic_year;
            $group = $validated['group'] ?? $schedule->group;

            $conflict = Schedule::where('id', '!=', $id)
                ->where('day', $day)
                ->where('specialty_id', $specialty_id)
                ->where('academic_year', $year)
                ->where(function($query) use ($start, $end) {
                    $query->whereBetween('start_time', [$start, $end])
                          ->orWhereBetween('end_time', [$start, $end])
                          ->orWhere(function($q) use ($start, $end) {
                              $q->where('start_time', '<=', $start)
                                ->where('end_time', '>=', $end);
                          });
                });

            if ($group) {
                $conflict->where(function($query) use ($group) {
                    $query->where('group', $group)
                          ->orWhereNull('group');
                });
            } else {
                $conflict->whereNull('group');
            }

            if ($conflict->exists()) {
                return response()->json([
                    'message' => 'Schedule conflict detected for this time slot',
                ], 422);
            }
        }

        $schedule->update($validated);

        return response()->json([
            'message' => 'Schedule updated successfully',
            'schedule' => [
                'id' => $schedule->id,
                'module' => $schedule->module->name,
                'teacher' => $schedule->teacher->full_name,
            ],
        ]);
    }

    /**
     * Delete schedule
     */
    public function destroy($id): JsonResponse
    {
        $schedule = Schedule::findOrFail($id);
        $schedule->delete();

        return response()->json([
            'message' => 'Schedule deleted successfully',
        ]);
    }

    /**
     * Get available groups for a specialty
     */
    public function getGroups(Request $request): JsonResponse
    {
        $specialty_id = $request->specialty_id;

        if (!$specialty_id) {
            return response()->json(['message' => 'specialty_id is required'], 400);
        }

        $groups = \App\Models\Student::where('specialty_id', $specialty_id)
            ->whereNotNull('group')
            ->distinct()
            ->pluck('group')
            ->filter()
            ->values();

        return response()->json([
            'groups' => $groups,
        ]);
    }
}
