<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Module;
use App\Models\Grade;
use App\Models\Attendance;
use App\Models\Schedule;
use App\Models\Exam;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class StudentController extends Controller
{
    // ═══════════════════════════════════════════════════════════
    // DASHBOARD
    // ═══════════════════════════════════════════════════════════

    /**
     * Get student dashboard
     * GET /api/student/dashboard
     */
    public function dashboard(Request $request): JsonResponse
    {
        $user = $request->user();
        $student = $user->student()->with('specialty')->first();

        if (!$student) {
            return response()->json([
                'message' => 'Profil étudiant non trouvé',
            ], 404);
        }

        // Get modules for current semester
        $modules = Module::where('specialty_id', $student->specialty_id)
            ->where('semester', $student->current_semester)
            ->with('teachers')
            ->get();

        // Get recent grades (last 5)
        $recentGrades = Grade::where('student_id', $student->id)
            ->with(['module', 'exam'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Calculate attendance rate
        $totalAttendance = Attendance::where('student_id', $student->id)->count();
        $presentCount = Attendance::where('student_id', $student->id)
            ->where('status', 'present')
            ->count();
        $lateCount = Attendance::where('student_id', $student->id)
            ->where('status', 'late')
            ->count();
        $absentCount = Attendance::where('student_id', $student->id)
            ->where('status', 'absent')
            ->count();
        $excusedCount = Attendance::where('student_id', $student->id)
            ->where('status', 'excused')
            ->count();
        $attendanceRate = $totalAttendance > 0
            ? round(($presentCount / $totalAttendance) * 100, 2)
            : 0;

        // Get upcoming exams (next 5)
        $upcomingExams = Exam::whereHas('module', function($q) use ($student) {
                $q->where('specialty_id', $student->specialty_id);
            })
            ->where('exam_date', '>=', now())
            ->orderBy('exam_date')
            ->limit(5)
            ->with('module')
            ->get();

        return response()->json([
            'student' => [
                'id' => $student->id,
                'registration_number' => $student->registration_number,
                'full_name' => $student->full_name,
                'email' => $user->email,
                'phone' => $user->phone,
                'specialty' => [
                    'id' => $student->specialty->id,
                    'name' => $student->specialty->name,
                    'code' => $student->specialty->code,
                ],
                'current_semester' => $student->current_semester,
                'study_mode' => $student->study_mode,
                'years_enrolled' => $student->years_enrolled,
            ],
            'statistics' => [
                'modules_count' => $modules->count(),
                'attendance' => [
                    'total' => $totalAttendance,
                    'present' => $presentCount,
                    'late' => $lateCount,
                    'absent' => $absentCount,
                    'excused' => $excusedCount,
                    'rate' => $attendanceRate,
                ],
                'grades_count' => $recentGrades->count(),
                'upcoming_exams' => $upcomingExams->count(),
            ],
            'modules' => $modules->map(function($module) {
                return [
                    'id' => $module->id,
                    'name' => $module->name,
                    'code' => $module->code,
                    'coefficient' => $module->coefficient ?? 1,
                    'hours_per_week' => $module->hours_per_week ?? 0,
                ];
            }),
            'recent_grades' => $recentGrades->map(function($grade) {
                $maxGrade = 20;
                return [
                    'id' => $grade->id,
                    'module' => [
                        'id' => $grade->module->id,
                        'name' => $grade->module->name,
                        'code' => $grade->module->code,
                    ],
                    'exam_type' => $grade->exam ? $grade->exam->exam_type : 'N/A',
                    'grade' => $grade->grade,
                    'max_grade' => $maxGrade,
                    'grade_20' => round(($grade->grade / $maxGrade) * 20, 2),
                    'percentage' => round(($grade->grade / $maxGrade) * 100, 2),
                    'date' => $grade->created_at->format('Y-m-d'),
                ];
            }),
            'upcoming_exams' => $upcomingExams->map(function($exam) {
                return [
                    'id' => $exam->id,
                    'module' => [
                        'id' => $exam->module->id,
                        'name' => $exam->module->name,
                    ],
                    'type' => $exam->exam_type,
                    'date' => $exam->exam_date->format('Y-m-d'),
                    'start_time' => $exam->exam_date->format('H:i'),
                    'duration_minutes' => $exam->duration_minutes,
                    'room' => $exam->classroom,
                ];
            }),
        ]);
    }

    // ═══════════════════════════════════════════════════════════
    // PROFILE
    // ═══════════════════════════════════════════════════════════

    /**
     * Get student profile
     * GET /api/student/profile
     */
    public function profile(Request $request): JsonResponse
    {
        $user = $request->user();
        $student = $user->student()->with('specialty')->first();

        if (!$student) {
            return response()->json([
                'message' => 'Profil étudiant non trouvé',
            ], 404);
        }

        return response()->json([
            'id' => $student->id,
            'registration_number' => $student->registration_number,
            'first_name' => $student->first_name,
            'last_name' => $student->last_name,
            'date_of_birth' => $student->date_of_birth->format('Y-m-d'),
            'address' => $student->address,
            'email' => $user->email,
            'phone' => $user->phone,
            'specialty' => [
                'id' => $student->specialty->id,
                'name' => $student->specialty->name,
                'code' => $student->specialty->code,
            ],
            'current_semester' => $student->current_semester,
            'study_mode' => $student->study_mode,
            'years_enrolled' => $student->years_enrolled,
            'is_graduated' => $student->is_graduated,
            'created_at' => $student->created_at->format('Y-m-d'),
        ]);
    }

    /**
     * Update student profile
     * PUT /api/student/profile
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();
        $student = $user->student;

        if (!$student) {
            return response()->json(['message' => 'Student profile not found'], 404);
        }

        // Validate only editable fields
        $validated = $request->validate([
            'phone' => ['sometimes', 'nullable', 'string', 'regex:/^0[5-7][0-9]{8}$/', Rule::unique('users')->ignore($user->id)],
            'date_of_birth' => ['sometimes', 'nullable', 'date', 'before:today'],
            'address' => ['sometimes', 'nullable', 'string', 'max:500'],
        ]);

        // Update User model (only phone)
        if (isset($validated['phone'])) {
            $user->update(['phone' => $validated['phone']]);
        }

        // Update Student model (date_of_birth, address)
        $studentUpdate = [];
        if (isset($validated['date_of_birth'])) {
            $studentUpdate['date_of_birth'] = $validated['date_of_birth'];
        }
        if (isset($validated['address'])) {
            $studentUpdate['address'] = $validated['address'];
        }

        if (!empty($studentUpdate)) {
            $student->update($studentUpdate);
        }

        // Reload relationships
        $student->load('specialty');
        $student->refresh();
        $user->refresh();

        return response()->json([
            'message' => 'Profile updated successfully',
            'id' => $student->id,
            'registration_number' => $student->registration_number,
            'first_name' => $student->first_name,
            'last_name' => $student->last_name,
            'date_of_birth' => $student->date_of_birth,
            'address' => $student->address,
            'email' => $user->email,
            'phone' => $user->phone,
            'specialty' => [
                'id' => $student->specialty->id,
                'name' => $student->specialty->name,
                'code' => $student->specialty->code,
            ],
            'current_semester' => $student->current_semester,
            'study_mode' => $student->study_mode,
            'years_enrolled' => $student->years_enrolled,
            'is_graduated' => $student->is_graduated,
            'created_at' => $student->created_at->format('Y-m-d'),
        ]);
    }

    /**
     * Update password
     * PUT /api/student/profile/password
     */
    public function updatePassword(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8',
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            return response()->json(['message' => 'Current password is incorrect'], 422);
        }

        $user->update([
            'password' => Hash::make($validated['new_password'])
        ]);

        return response()->json(['message' => 'Password updated successfully']);
    }

    /**
     * Complete student profile (first time)
     * POST /api/student/complete-profile
     */
    public function completeProfile(Request $request): JsonResponse
    {
        $user = $request->user();
        $student = $user->student()->with('specialty')->first();

        if (!$student) {
            return response()->json(['message' => 'Student profile not found'], 404);
        }

        // Log incoming data
        Log::info('Complete Profile Request', [
            'user_id' => $user->id,
            'student_id' => $student->id,
            'data' => $request->all()
        ]);

        // Validate required fields
        $validated = $request->validate([
            'date_of_birth' => ['required', 'date', 'before:today', 'after:1950-01-01'],
            'address' => ['required', 'string', 'min:10', 'max:500'],
            'phone' => ['nullable', 'string', 'regex:/^0[5-7][0-9]{8}$/', Rule::unique('users')->ignore($user->id)],
        ]);

        // Update student (saves to database)
        $student->update([
            'date_of_birth' => $validated['date_of_birth'],
            'address' => $validated['address'],
        ]);

        Log::info('Student updated', [
            'student_id' => $student->id,
            'date_of_birth' => $student->date_of_birth,
            'address' => $student->address
        ]);

        // Update phone if provided
        if (isset($validated['phone']) && !empty($validated['phone'])) {
            $user->update(['phone' => $validated['phone']]);
            Log::info('User phone updated', ['user_id' => $user->id, 'phone' => $user->phone]);
        }

        // Reload from database
        $student->refresh();
        $user->refresh();

        Log::info('Profile completion successful', [
            'student_id' => $student->id,
            'date_of_birth' => $student->date_of_birth,
            'address' => $student->address,
            'phone' => $user->phone
        ]);

        return response()->json([
            'message' => 'Profile completed successfully',
            'profile_complete' => true,
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'role' => $user->role,
            'registration_number' => $student->registration_number,
            'first_name' => $student->first_name,
            'last_name' => $student->last_name,
            'date_of_birth' => $student->date_of_birth,
            'address' => $student->address,
            'specialty_id' => $student->specialty_id,
            'current_semester' => $student->current_semester,
            'study_mode' => $student->study_mode,
        ]);
    }

    // ═══════════════════════════════════════════════════════════
    // MODULES
    // ═══════════════════════════════════════════════════════════

    /**
     * Get student modules (with filters)
     * GET /api/student/modules?semester=1
     */
    public function modules(Request $request): JsonResponse
    {
        $student = $request->user()->student;

        if (!$student) {
            return response()->json([
                'message' => 'Profil étudiant non trouvé',
            ], 404);
        }

        $semester = $request->semester ?? $student->current_semester;

        $modules = Module::where('specialty_id', $student->specialty_id)
            ->where('semester', $semester)
            ->with(['teachers'])
            ->get()
            ->map(function($module) use ($student) {
                // Count attendance for this module
                $totalClasses = Attendance::whereHas('schedule', function($q) use ($module) {
                        $q->where('module_id', $module->id);
                    })
                    ->where('student_id', $student->id)
                    ->count();

                $presentClasses = Attendance::whereHas('schedule', function($q) use ($module) {
                        $q->where('module_id', $module->id);
                    })
                    ->where('student_id', $student->id)
                    ->where('status', 'present')
                    ->count();

                $absentClasses = $totalClasses - $presentClasses;

                $attendanceRate = $totalClasses > 0
                    ? round(($presentClasses / $totalClasses) * 100, 2)
                    : 0;

                return [
                    'id' => $module->id,
                    'name' => $module->name,
                    'code' => $module->code,
                    'description' => $module->description,
                    'semester' => $module->semester,
                    'coefficient' => $module->coefficient,
                    'hours_per_week' => $module->hours_per_week,
                    'teachers' => $module->teachers->map(function($teacher) {
                        return [
                            'id' => $teacher->id,
                            'full_name' => $teacher->full_name,
                            'specialization' => $teacher->specialization,
                        ];
                    }),
                    'attendance' => [
                        'total_classes' => $totalClasses,
                        'present' => $presentClasses,
                        'absent' => $absentClasses,
                        'rate' => $attendanceRate,
                    ],
                ];
            });

        return response()->json([
            'semester' => $semester,
            'modules' => $modules,
            'count' => $modules->count(),
        ]);
    }

    // ═══════════════════════════════════════════════════════════
    // GRADES
    // ═══════════════════════════════════════════════════════════

    /**
     * Get student grades (with filters)
     * GET /api/student/grades?semester=1&module_id=5&from_date=2025-01-01&to_date=2025-12-31
     */
    public function grades(Request $request): JsonResponse
    {
        $student = $request->user()->student;

        if (!$student) {
            return response()->json([
                'message' => 'Profil étudiant non trouvé',
            ], 404);
        }

        $query = Grade::where('student_id', $student->id)
            ->with(['module', 'exam']);

        // Filter by semester
        if ($request->semester) {
            $query->whereHas('module', function($q) use ($request) {
                $q->where('semester', $request->semester);
            });
        }

        // Filter by module
        if ($request->module_id) {
            $query->where('module_id', $request->module_id);
        }

        // Filter by date range
        if ($request->from_date) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->to_date) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $grades = $query->orderBy('created_at', 'desc')->get();

        // Calculate statistics
        $totalGrades = $grades->count();
        $maxGrade = 20;
        $averageGrade = $totalGrades > 0
            ? round($grades->avg(function($grade) use ($maxGrade) {
                return ($grade->grade / $maxGrade) * 20;
              }), 2)
            : 0;

        // Group by module
        $gradesByModule = $grades->groupBy('module_id')->map(function($moduleGrades) use ($maxGrade) {
            $module = $moduleGrades->first()->module;
            $avgGrade = round($moduleGrades->avg(function($grade) use ($maxGrade) {
                return ($grade->grade / $maxGrade) * 20;
            }), 2);

            return [
                'module' => [
                    'id' => $module->id,
                    'name' => $module->name,
                    'code' => $module->code,
                    'coefficient' => $module->coefficient,
                ],
                'average' => $avgGrade,
                'grades_count' => $moduleGrades->count(),
                'grades' => $moduleGrades->map(function($grade) use ($maxGrade) {
                    return [
                        'id' => $grade->id,
                        'exam_type' => $grade->exam ? $grade->exam->exam_type : 'N/A',
                        'grade' => $grade->grade,
                        'max_grade' => $maxGrade,
                        'grade_20' => round(($grade->grade / $maxGrade) * 20, 2),
                        'percentage' => round(($grade->grade / $maxGrade) * 100, 2),
                        'date' => $grade->created_at->format('Y-m-d'),
                    ];
                }),
            ];
        })->values();

        return response()->json([
            'statistics' => [
                'total_grades' => $totalGrades,
                'average_grade' => $averageGrade,
            ],
            'grades_by_module' => $gradesByModule,
        ]);
    }

    // ═══════════════════════════════════════════════════════════
    // ATTENDANCE
    // ═══════════════════════════════════════════════════════════

    /**
     * Get student attendance
     * GET /api/student/attendance
     */
    public function attendance(Request $request): JsonResponse
    {
        $student = $request->user()->student;

        if (!$student) {
            return response()->json(['message' => 'Student profile not found'], 404);
        }

        $query = Attendance::where('student_id', $student->id)
            ->with(['schedule.module', 'schedule.teacher']);

        // Filter by date range
        if ($request->from) {
            $query->whereDate('attendance_date', '>=', $request->from);
        }
        if ($request->to) {
            $query->whereDate('attendance_date', '<=', $request->to);
        }

        // Filter by module
        if ($request->module_id) {
            $query->whereHas('schedule', function($q) use ($request) {
                $q->where('module_id', $request->module_id);
            });
        }

        $attendances = $query->orderBy('attendance_date', 'desc')->paginate(15);

        $attendances->getCollection()->transform(function($attendance) {
            $schedule = $attendance->schedule;
            $module = $schedule ? $schedule->module : null;
            $teacher = $schedule ? $schedule->teacher : null;

            return [
                'date' => $attendance->attendance_date->format('Y-m-d'),
                'day_name' => $attendance->attendance_date->locale('en')->isoFormat('dddd'),
                'start_time' => $schedule ? Carbon::parse($schedule->start_time)->format('H:i') : null,
                'end_time' => $schedule ? Carbon::parse($schedule->start_time)->addHours(1)->format('H:i') : null,
                'room' => $schedule ? $schedule->classroom : null,
                'module' => $module ? [
                    'id' => $module->id,
                    'code' => $module->code,
                    'name' => $module->name,
                ] : null,
                'instructor' => $teacher ? $teacher->user->full_name : 'N/A',
                'status' => $attendance->status,
                'note' => $attendance->notes,
            ];
        });

        return response()->json($attendances);
    }

    // ═══════════════════════════════════════════════════════════
    // SCHEDULE
    // ═══════════════════════════════════════════════════════════

    /**
     * Get student schedule
     * GET /api/student/schedule?week=current
     */
    public function schedule(Request $request): JsonResponse
    {
        $student = $request->user()->student;

        if (!$student) {
            return response()->json([
                'message' => 'Profil étudiant non trouvé',
            ], 404);
        }

        // Determine start and end of the requested week
        $startOfWeek = now()->startOfWeek();
        if ($request->week === 'next') {
            $startOfWeek = now()->addWeek()->startOfWeek();
        }
        $endOfWeek = $startOfWeek->copy()->endOfWeek();

        // Get recurring schedules for student's specialty and semester
        $schedules = Schedule::where('specialty_id', $student->specialty_id)
            ->where('semester', $student->current_semester)
            ->with(['module', 'teacher'])
            ->get();

        // Map recurring schedules to actual dates in the week
        $weekSchedules = collect();

        // Map English days to Carbon days
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

                $weekSchedules->push([
                    'id' => $schedule->id,
                    'module' => [
                        'id' => $schedule->module->id,
                        'name' => $schedule->module->name,
                        'code' => $schedule->module->code,
                    ],
                    'teacher' => $schedule->teacher ? [
                        'id' => $schedule->teacher->id,
                        'full_name' => $schedule->teacher->full_name,
                    ] : null,
                    'start_time' => is_string($schedule->start_time) ? $schedule->start_time : Carbon::parse($schedule->start_time)->format('H:i:s'),
                    'end_time' => Carbon::parse($schedule->start_time)->addHours(1)->format('H:i:s'),
                    'room' => $schedule->classroom,
                    'type' => 'course',
                    'date' => $date->format('Y-m-d'),
                    'day_name' => $date->locale('fr')->isoFormat('dddd'),
                ]);
            }
        }

        // Group by day
        $schedulesByDay = $weekSchedules->sortBy('start_time')->groupBy('date')->map(function($daySchedules, $date) {
            return [
                'date' => $date,
                'day_name' => $daySchedules->first()['day_name'],
                'classes' => $daySchedules->values()->toArray(),
            ];
        })->values()->toArray();

        Log::info('Schedule Response', [
            'student_id' => $student->id,
            'specialty_id' => $student->specialty_id,
            'semester' => $student->current_semester,
            'schedules_count' => $schedules->count(),
            'week_schedules_count' => $weekSchedules->count(),
            'response_count' => count($schedulesByDay)
        ]);

        return response()->json($schedulesByDay);
    }

    // ═══════════════════════════════════════════════════════════
    // EXAMS
    // ═══════════════════════════════════════════════════════════

    /**
     * Get student exam results
     * GET /api/student/exams/results
     */
    public function examResults(Request $request): JsonResponse
    {
        $student = $request->user()->student;

        if (!$student) {
            return response()->json(['message' => 'Student profile not found'], 404);
        }

        $query = Grade::where('student_id', $student->id)
            ->with(['exam.module']);

        // Sort by exam date descending
        $query->join('exams', 'grades.exam_id', '=', 'exams.id')
              ->orderBy('exams.exam_date', 'desc')
              ->select('grades.*');

        $grades = $query->get();

        $results = $grades->map(function($grade) {
            $exam = $grade->exam;
            $module = $exam ? $exam->module : ($grade->module ?? null);

            return [
                'id' => $grade->id,
                'subject' => $module ? $module->name : 'Unknown Subject',
                'type' => $exam ? ucfirst($exam->exam_type) : 'Exam',
                'date' => $exam ? $exam->exam_date->format('Y-m-d') : null,
                'grade' => (float)$grade->grade,
            ];
        });

        return response()->json(['results' => $results]);
    }

    /**
     * Get upcoming exams
     * GET /api/student/exams/upcoming
     */
    public function upcomingExams(Request $request): JsonResponse
    {
        $student = $request->user()->student;

        if (!$student) {
            return response()->json(['message' => 'Student profile not found'], 404);
        }

        // Get exams for the student's specialty and semester that are in the future
        $exams = Exam::whereHas('module', function($q) use ($student) {
                $q->where('specialty_id', $student->specialty_id)
                  ->where('semester', $student->current_semester);
            })
            ->where('exam_date', '>=', now())
            ->orderBy('exam_date', 'asc')
            ->with('module')
            ->get();

        $upcoming = $exams->map(function($exam) {
            return [
                'id' => $exam->id,
                'subject' => $exam->module->name,
                'type' => ucfirst($exam->exam_type),
                'date' => $exam->exam_date->format('Y-m-d'),
                'time' => $exam->exam_date->format('H:i'), // exam_date is dateTime
                'room' => $exam->classroom ?? 'TBA',
                'duration' => $exam->duration_minutes ? $exam->duration_minutes . ' mins' : 'N/A',
            ];
        });

        return response()->json(['exams' => $upcoming]);
    }

    /**
     * Calculate grade letter based on mark
     */
    private function calculateGradeLetter($mark)
    {
        if ($mark >= 16) return 'A';
        if ($mark >= 14) return 'B';
        if ($mark >= 12) return 'C';
        if ($mark >= 10) return 'D';
        return 'F';
    }
}
