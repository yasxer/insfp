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
        $absentCount = $totalAttendance - $presentCount;
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
                    'absent' => $absentCount,
                    'rate' => $attendanceRate,
                ],
                'grades_count' => $recentGrades->count(),
                'upcoming_exams' => $upcomingExams->count(),
            ],
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
            'student' => [
                'id' => $student->id,
                'registration_number' => $student->registration_number,
                'first_name' => $student->first_name,
                'last_name' => $student->last_name,
                'full_name' => $student->full_name,
                'email' => $user->email,
                'phone' => $user->phone,
                'specialty' => [
                    'id' => $student->specialty->id,
                    'name' => $student->specialty->name,
                    'code' => $student->specialty->code,
                    'duration_semesters' => $student->specialty->duration_semesters,
                ],
                'current_semester' => $student->current_semester,
                'study_mode' => $student->study_mode,
                'years_enrolled' => $student->years_enrolled,
                'is_graduated' => $student->is_graduated,
                'created_at' => $student->created_at->format('Y-m-d'),
            ],
        ]);
    }

    /**
     * Update student profile (Email + Phone + Password)
     * PUT /api/student/profile
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();
        $student = $user->student;

        if (!$student) {
            return response()->json([
                'message' => 'Profil étudiant non trouvé',
            ], 404);
        }

        $validated = $request->validate([
            'email' => ['sometimes', 'email', Rule::unique('users')->ignore($user->id)],
            'phone' => ['sometimes', 'nullable', 'string', 'regex:/^0[5-7][0-9]{8}$/', Rule::unique('users')->ignore($user->id)],
            'current_password' => 'required_with:new_password|string',
            'new_password' => 'sometimes|string|min:6|confirmed',
        ]);

        // Update email/phone
        $userUpdate = [];
        if (isset($validated['email'])) {
            $userUpdate['email'] = $validated['email'];
        }
        if (isset($validated['phone'])) {
            $userUpdate['phone'] = $validated['phone'];
        }

        // Update password if provided
        if (isset($validated['new_password'])) {
            // Verify current password
            if (!Hash::check($validated['current_password'], $user->password)) {
                throw ValidationException::withMessages([
                    'current_password' => ['Mot de passe actuel incorrect.'],
                ]);
            }
            $userUpdate['password'] = Hash::make($validated['new_password']);
        }

        if (!empty($userUpdate)) {
            $user->update($userUpdate);
        }

        return response()->json([
            'message' => 'Profil mis à jour avec succès',
            'student' => [
                'email' => $user->email,
                'phone' => $user->phone,
            ],
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
            ->with(['schedule.module']);

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
            ->with(['module.teachers'])
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
                    'start_time' => $schedule->start_time,
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
                'classes' => $daySchedules->values(),
            ];
        })->values();

        return response()->json([
            'schedule' => $schedulesByDay,
        ]);
    }

    // ═══════════════════════════════════════════════════════════
    // EXAMS
    // ═══════════════════════════════════════════════════════════

    /**
     * Get student exams / results
     * GET /api/student/exams
     */
    public function exams(Request $request): JsonResponse
    {
        $student = $request->user()->student;

        if (!$student) {
            return response()->json(['message' => 'Student profile not found'], 404);
        }

        $query = Grade::where('student_id', $student->id)
            ->with(['exam.module']);

        // Filter by module
        if ($request->module_id) {
            $query->where('module_id', $request->module_id);
        }

        // Filter by date range
        if ($request->from) {
            $query->whereHas('exam', function($q) use ($request) {
                $q->whereDate('exam_date', '>=', $request->from);
            });
        }
        if ($request->to) {
            $query->whereHas('exam', function($q) use ($request) {
                $q->whereDate('exam_date', '<=', $request->to);
            });
        }

        // Filter by passed status
        if ($request->has('passed')) {
            if ($request->boolean('passed')) {
                $query->where('grade', '>=', 10);
            } else {
                $query->where('grade', '<', 10);
            }
        }

        // Sort by exam date descending
        // We need to join exams table to sort by exam_date
        $query->join('exams', 'grades.exam_id', '=', 'exams.id')
              ->orderBy('exams.exam_date', 'desc')
              ->select('grades.*');

        $grades = $query->paginate(15);

        $grades->getCollection()->transform(function($grade) {
            $exam = $grade->exam;
            $module = $exam ? $exam->module : ($grade->module ?? null);

            return [
                'exam' => $exam ? [
                    'id' => $exam->id,
                    'title' => ucfirst($exam->exam_type) . ' Exam',
                    'type' => $exam->exam_type,
                    'date' => $exam->exam_date->format('Y-m-d'),
                    'max_mark' => 20,
                    'module' => $module ? [
                        'id' => $module->id,
                        'code' => $module->code,
                        'name' => $module->name,
                    ] : null,
                ] : null,
                'mark' => (float)$grade->grade,
                'grade_letter' => $this->calculateGradeLetter($grade->grade),
                'note' => null,
                'passed' => $grade->grade >= 10,
            ];
        });

        return response()->json($grades);
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
