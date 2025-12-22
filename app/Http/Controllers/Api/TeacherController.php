<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\User;
use App\Models\Module;
use App\Models\Student;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TeacherController extends Controller
{
    // ═══════════════════════════════════════════════════════════
    // DASHBOARD
    // ═══════════════════════════════════════════════════════════

    /**
     * Get teacher dashboard
     * GET /api/teacher/dashboard
     */
    public function dashboard(Request $request): JsonResponse
    {
        $user = $request->user();
        $teacher = $user->teacher()->with('specialty')->first();

        if (!$teacher) {
            return response()->json([
                'message' => 'Profil enseignant non trouvé',
            ], 404);
        }

        // Count assigned modules
        $modulesCount = $teacher->modules()->count();

        // Count competences (can teach modules)
        // Assuming the relationship 'canTeachModules' exists as per requirements
        $canTeachModulesCount = 0;
        if (method_exists($teacher, 'canTeachModules')) {
            $canTeachModulesCount = $teacher->canTeachModules()->count();
        }

        // Calculate upcoming lessons count (remaining classes in the current week)
        // Since Schedule is recurring (day of week), we count slots for remaining days of this week
        $today = now();
        $endOfWeek = now()->endOfWeek();

        // Map Carbon days to English days for query
        $dayMap = [
            Carbon::MONDAY => 'monday',
            Carbon::TUESDAY => 'tuesday',
            Carbon::WEDNESDAY => 'wednesday',
            Carbon::THURSDAY => 'thursday',
            Carbon::FRIDAY => 'friday',
            Carbon::SATURDAY => 'saturday',
            Carbon::SUNDAY => 'sunday',
        ];

        $remainingDays = [];
        for ($date = $today->copy(); $date->lte($endOfWeek); $date->addDay()) {
            $remainingDays[] = $dayMap[$date->dayOfWeek] ?? null;
        }
        $remainingDays = array_filter($remainingDays);

        $upcomingLessonsCount = 0;
        if (!empty($remainingDays)) {
            $upcomingLessonsCount = Schedule::where('teacher_id', $teacher->id)
                ->whereIn('day', $remainingDays)
                ->count();

            // Refine for today: only count classes that haven't started yet?
            // For simplicity, we count all classes on remaining days including today.
        }

        return response()->json([
            'teacher' => [
                'id' => $teacher->id,
                'full_name' => $teacher->full_name,
                'email' => $user->email,
                'specialty' => $teacher->specialty ? [
                    'id' => $teacher->specialty->id,
                    'name' => $teacher->specialty->name,
                ] : null,
            ],
            'statistics' => [
                'modules_count' => $modulesCount,
                'can_teach_modules_count' => $canTeachModulesCount,
                'upcoming_lessons_count' => $upcomingLessonsCount,
            ],
        ]);
    }

    // ═══════════════════════════════════════════════════════════
    // PROFILE
    // ═══════════════════════════════════════════════════════════

    /**
     * Get teacher profile
     * GET /api/teacher/profile
     */
    public function profile(Request $request): JsonResponse
    {
        $user = $request->user();
        // Eager load relationships
        $teacher = $user->teacher()
            ->with(['specialty', 'modules'])
            ->first();

        if (!$teacher) {
            return response()->json([
                'message' => 'Profil enseignant non trouvé',
            ], 404);
        }

        // Load canTeachModules if relationship exists
        $canTeachModules = [];
        if (method_exists($teacher, 'canTeachModules')) {
            $canTeachModules = $teacher->canTeachModules->map(function ($module) {
                return [
                    'id' => $module->id,
                    'code' => $module->code,
                    'name' => $module->name,
                ];
            });
        }

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $teacher->full_name, // Using teacher name as user name might be different or same
                'email' => $user->email,
                'phone' => $user->phone,
            ],
            'teacher' => [
                'id' => $teacher->id,
                'specialty' => $teacher->specialty ? [
                    'id' => $teacher->specialty->id,
                    'name' => $teacher->specialty->name,
                ] : null,
                'can_teach_modules' => $canTeachModules,
                'assigned_modules' => $teacher->modules->map(function ($module) {
                    return [
                        'id' => $module->id,
                        'code' => $module->code,
                        'name' => $module->name,
                    ];
                }),
            ],
        ]);
    }

    /**
     * Update teacher profile
     * PUT /api/teacher/profile
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();
        $teacher = $user->teacher;

        if (!$teacher) {
            return response()->json([
                'message' => 'Profil enseignant non trouvé',
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|min:3|max:100',
            'email' => ['sometimes', 'email', Rule::unique('users')->ignore($user->id)],
            'phone' => 'sometimes|nullable|string|max:30',
            'password' => 'sometimes|nullable|string|min:8|confirmed',
            'specialty_id' => 'sometimes|nullable|exists:specialties,id',
            'can_teach_module_ids' => 'sometimes|array',
            'can_teach_module_ids.*' => 'exists:modules,id',
        ]);

        DB::transaction(function () use ($user, $teacher, $validated) {
            // Update User
            $userUpdate = [];
            if (isset($validated['email'])) $userUpdate['email'] = $validated['email'];
            if (isset($validated['phone'])) $userUpdate['phone'] = $validated['phone'];
            if (isset($validated['password'])) $userUpdate['password'] = Hash::make($validated['password']);

            if (!empty($userUpdate)) {
                $user->update($userUpdate);
            }

            // Update Teacher
            $teacherUpdate = [];
            if (isset($validated['name'])) {
                // Assuming name is split into first_name and last_name
                // Simple split for demo purposes
                $parts = explode(' ', $validated['name'], 2);
                $teacherUpdate['first_name'] = $parts[0];
                $teacherUpdate['last_name'] = $parts[1] ?? '';
            }
            if (isset($validated['specialty_id'])) {
                $teacherUpdate['specialty_id'] = $validated['specialty_id'];
            }

            if (!empty($teacherUpdate)) {
                $teacher->update($teacherUpdate);
            }

            // Sync canTeachModules
            if (isset($validated['can_teach_module_ids']) && method_exists($teacher, 'canTeachModules')) {
                $teacher->canTeachModules()->sync($validated['can_teach_module_ids']);
            }
        });

        // Return updated profile
        return $this->profile($request);
    }

    // ═══════════════════════════════════════════════════════════
    // MODULES
    // ═══════════════════════════════════════════════════════════

    /**
     * Get assigned modules
     * GET /api/teacher/modules
     */
    public function modules(Request $request): JsonResponse
    {
        $teacher = $request->user()->teacher;

        if (!$teacher) {
            return response()->json(['message' => 'Profil enseignant non trouvé'], 404);
        }

        $query = $teacher->modules();

        // Filters
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('code', 'like', "%{$request->search}%");
            });
        }

        if ($request->semester) {
            $query->where('semester', $request->semester);
        }

        // Pagination
        $perPage = $request->input('per_page', 10);
        $modules = $query->paginate($perPage);

        // Transform data
        $data = $modules->getCollection()->map(function ($module) {
            // Get student count for this module
            // Students in the same specialty and semester
            $studentsCount = Student::where('specialty_id', $module->specialty_id)
                ->where('current_semester', $module->semester)
                ->count();

            return [
                'id' => $module->id,
                'code' => $module->code,
                'name' => $module->name,
                'semester' => $module->semester,
                'students_count' => $studentsCount,
            ];
        });

        return response()->json([
            'data' => $data,
            'meta' => [
                'current_page' => $modules->currentPage(),
                'per_page' => $modules->perPage(),
                'total' => $modules->total(),
                'last_page' => $modules->lastPage(),
            ],
        ]);
    }

    /**
     * Get students for a module
     * GET /api/teacher/modules/{module}/students
     */
    public function moduleStudents(Request $request, $moduleId): JsonResponse
    {
        $teacher = $request->user()->teacher;

        if (!$teacher) {
            return response()->json(['message' => 'Profil enseignant non trouvé'], 404);
        }

        // Check if teacher is assigned to this module
        $isAssigned = $teacher->modules()->where('modules.id', $moduleId)->exists();

        if (!$isAssigned) {
            return response()->json(['message' => 'Non autorisé. Vous n\'êtes pas assigné à ce module.'], 403);
        }

        $module = Module::findOrFail($moduleId);

        // Get students
        $students = Student::where('specialty_id', $module->specialty_id)
            ->where('current_semester', $module->semester)
            ->select('id', 'registration_number', 'first_name', 'last_name', 'user_id')
            ->with('user:id,email,phone')
            ->get()
            ->map(function ($student) {
                return [
                    'id' => $student->id,
                    'full_name' => $student->full_name,
                    'registration_number' => $student->registration_number,
                    'email' => $student->user->email ?? null,
                    'phone' => $student->user->phone ?? null,
                ];
            });

        return response()->json([
            'module' => [
                'id' => $module->id,
                'name' => $module->name,
                'code' => $module->code,
            ],
            'students' => $students,
            'count' => $students->count(),
        ]);
    }

    // ═══════════════════════════════════════════════════════════
    // SCHEDULE
    // ═══════════════════════════════════════════════════════════

    /**
     * Get teacher schedule
     * GET /api/teacher/schedule
     */
    public function schedule(Request $request): JsonResponse
    {
        $teacher = $request->user()->teacher;

        if (!$teacher) {
            return response()->json(['message' => 'Profil enseignant non trouvé'], 404);
        }

        // Determine start and end of the requested week
        $startOfWeek = now()->startOfWeek();
        if ($request->week === 'next') {
            $startOfWeek = now()->addWeek()->startOfWeek();
        }
        $endOfWeek = $startOfWeek->copy()->endOfWeek();

        // Get recurring schedules for teacher
        $schedules = Schedule::where('teacher_id', $teacher->id)
            ->with(['module'])
            ->get();

        // Map recurring schedules to actual dates in the week
        $weekLessons = collect();

        $dayMap = [
            'monday' => Carbon::MONDAY,
            'tuesday' => Carbon::TUESDAY,
            'wednesday' => Carbon::WEDNESDAY,
            'thursday' => Carbon::THURSDAY,
            'friday' => Carbon::FRIDAY,
            'saturday' => Carbon::SATURDAY,
            'sunday' => Carbon::SUNDAY,
        ];

        foreach ($schedules as $schedule) {
            $dayOfWeek = $dayMap[strtolower($schedule->day)] ?? null;

            if ($dayOfWeek) {
                // Find the date for this day in the requested week
                $date = $startOfWeek->copy()->setISODate($startOfWeek->year, $startOfWeek->weekOfYear, $dayOfWeek);

                $weekLessons->push([
                    'date' => $date->format('Y-m-d'),
                    'day_name' => $date->locale('en')->isoFormat('dddd'), // Prompt requested English day names in example
                    'start_time' => Carbon::parse($schedule->start_time)->format('H:i'),
                    'end_time' => Carbon::parse($schedule->start_time)->addHours(1)->format('H:i'), // Assuming 1h duration
                    'room' => $schedule->classroom,
                    'module' => [
                        'id' => $schedule->module->id,
                        'code' => $schedule->module->code,
                        'name' => $schedule->module->name,
                    ],
                ]);
            }
        }

        // Sort by date then start_time
        $sortedLessons = $weekLessons->sort(function ($a, $b) {
            if ($a['date'] === $b['date']) {
                return strcmp($a['start_time'], $b['start_time']);
            }
            return strcmp($a['date'], $b['date']);
        })->values();

        return response()->json([
            'week' => [
                'start_date' => $startOfWeek->format('Y-m-d'),
                'end_date' => $endOfWeek->format('Y-m-d'),
            ],
            'lessons' => $sortedLessons,
        ]);
    }
}
