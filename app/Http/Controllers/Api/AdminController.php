<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Administration;
use App\Models\Specialty;
use App\Models\Module;
use App\Models\RegistrationNumber;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    // ═══════════════════════════════════════════════════════════
    // DASHBOARD & STATISTICS
    // ═══════════════════════════════════════════════════════════

    /**
     * Get dashboard statistics
     */
    public function dashboard(): JsonResponse
    {
        $stats = [
            'students' => [
                'total' => Student::count(),
                'approved' => Student::whereHas('user', function($q) {
                    $q->where('is_approved', true);
                })->count(),
                'pending' => Student::whereHas('user', function($q) {
                    $q->where('is_approved', false);
                })->count(),
                'graduated' => Student::where('is_graduated', true)->count(),
            ],
            'teachers' => [
                'total' => Teacher::count(),
                'active' => Teacher::whereHas('user', function($q) {
                    $q->where('is_approved', true);
                })->count(),
            ],
            'specialties' => [
                'total' => Specialty::count(),
                'active' => Specialty::where('is_active', true)->count(),
            ],
            'modules' => [
                'total' => Module::count(),
            ],
            'registration_numbers' => [
                'available' => RegistrationNumber::where('is_used', false)->count(),
                'used' => RegistrationNumber::where('is_used', true)->count(),
            ],
        ];

        return response()->json([
            'statistics' => $stats,
        ]);
    }

    // ═══════════════════════════════════════════════════════════
    // REGISTRATION NUMBER MANAGEMENT
    // ═══════════════════════════════════════════════════════════

    /**
     * Generate ONE registration number
     */
    public function generateRegistrationNumber(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'specialty_id' => 'required|exists:specialties,id',
        ]);

        $specialty = Specialty::findOrFail($request->specialty_id);
        $year = date('Y');
        $academicYear = $year . '-' . ($year + 1);

        $lastNumber = RegistrationNumber::where('specialty_id', $request->specialty_id)
            ->where('number', 'like', $specialty->code . $year . '%')
            ->orderBy('number', 'desc')
            ->first();

        $sequence = $lastNumber
            ? intval(substr($lastNumber->number, -3)) + 1
            : 1;

        $number = $specialty->code . $year . str_pad($sequence, 3, '0', STR_PAD_LEFT);

        $registrationNumber = RegistrationNumber::create([
            'number' => $number,
            'specialty_id' => $request->specialty_id,
            'academic_year' => $academicYear,
            'is_used' => false,
        ]);

        return response()->json([
            'message' => 'Numéro généré avec succès',
            'registration_number' => $number,
            'specialty' => $specialty->name,
            'academic_year' => $academicYear,
        ], 201);
    }

    /**
     * Get available registration numbers
     */
    public function availableRegistrationNumbers(Request $request): JsonResponse
    {
        $query = RegistrationNumber::with('specialty')->where('is_used', false);

        if ($request->specialty_id) {
            $query->where('specialty_id', $request->specialty_id);
        }

        $numbers = $query->orderBy('number')->get()->map(function ($regNum) {
            return [
                'id' => $regNum->id,
                'number' => $regNum->number,
                'specialty' => $regNum->specialty->name,
                'academic_year' => $regNum->academic_year,
                'created_at' => $regNum->created_at->format('Y-m-d H:i'),
            ];
        });

        return response()->json([
            'available_numbers' => $numbers,
            'count' => $numbers->count(),
        ]);
    }

    /**
     * Get used registration numbers
     */
    public function usedRegistrationNumbers(Request $request): JsonResponse
    {
        $query = RegistrationNumber::with(['specialty'])
            ->where('is_used', true);

        if ($request->specialty_id) {
            $query->where('specialty_id', $request->specialty_id);
        }

        $numbers = $query->orderBy('used_at', 'desc')->get()->map(function ($regNum) {
            $student = Student::where('registration_number', $regNum->number)->with('user')->first();

            return [
                'id' => $regNum->id,
                'number' => $regNum->number,
                'specialty' => $regNum->specialty->name,
                'academic_year' => $regNum->academic_year,
                'used_at' => $regNum->used_at->format('Y-m-d H:i'),
                'student' => $student ? [
                    'full_name' => $student->full_name,
                    'email' => $student->user->email ?? null,
                    'is_approved' => $student->user ? $student->user->is_approved : false,
                ] : null,
            ];
        });

        return response()->json([
            'used_numbers' => $numbers,
            'count' => $numbers->count(),
        ]);
    }

    /**
     * Delete unused registration number
     */
    public function deleteRegistrationNumber(Request $request, $id): JsonResponse
    {
        $regNum = RegistrationNumber::findOrFail($id);

        if ($regNum->is_used) {
            return response()->json([
                'message' => 'Impossible de supprimer un numéro déjà utilisé',
            ], 400);
        }

        $number = $regNum->number;
        $regNum->delete();

        return response()->json([
            'message' => 'Numéro supprimé avec succès',
            'number' => $number,
        ]);
    }

    // ═══════════════════════════════════════════════════════════
    // STUDENT MANAGEMENT (FULL CRUD)
    // ═══════════════════════════════════════════════════════════

    /**
     * Get all students with filters
     */
    public function getStudents(Request $request): JsonResponse
    {
        $query = Student::with(['user', 'specialty']);

        // Filter by approval status
        if ($request->has('approved')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('is_approved', $request->boolean('approved'));
            });
        }

        // Filter by specialty
        if ($request->specialty_id) {
            $query->where('specialty_id', $request->specialty_id);
        }

        // Filter by semester
        if ($request->semester) {
            $query->where('current_semester', $request->semester);
        }

        // Filter by study mode
        if ($request->study_mode) {
            $query->where('study_mode', $request->study_mode);
        }

        // Filter by graduated status
        if ($request->has('is_graduated')) {
            $query->where('is_graduated', $request->boolean('is_graduated'));
        }

        // Search by name or registration number
        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('registration_number', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
            });
        }

        $students = $query->orderBy('registration_number')->get()->map(function ($student) {
            return [
                'id' => $student->id,
                'registration_number' => $student->registration_number,
                'full_name' => $student->full_name,
                'first_name' => $student->first_name,
                'last_name' => $student->last_name,
                'email' => $student->user->email ?? null,
                'phone' => $student->user->phone ?? null,
                'is_approved' => $student->user ? $student->user->is_approved : false,
                'specialty' => $student->specialty ? [
                    'id' => $student->specialty->id,
                    'name' => $student->specialty->name,
                    'code' => $student->specialty->code,
                ] : null,
                'study_mode' => $student->study_mode,
                'current_semester' => $student->current_semester,
                'years_enrolled' => $student->years_enrolled,
                'is_graduated' => $student->is_graduated,
                'created_at' => $student->created_at->format('Y-m-d'),
            ];
        });

        return response()->json([
            'students' => $students,
            'count' => $students->count(),
        ]);
    }

    /**
     * Get single student details
     */
    public function getStudent($id): JsonResponse
    {
        $student = Student::with(['user', 'specialty'])->findOrFail($id);

        return response()->json([
            'student' => [
                'id' => $student->id,
                'registration_number' => $student->registration_number,
                'first_name' => $student->first_name,
                'last_name' => $student->last_name,
                'full_name' => $student->full_name,
                'email' => $student->user->email ?? null,
                'phone' => $student->user->phone ?? null,
                'is_approved' => $student->user ? $student->user->is_approved : false,
                'specialty' => $student->specialty ? [
                    'id' => $student->specialty->id,
                    'name' => $student->specialty->name,
                    'code' => $student->specialty->code,
                ] : null,
                'study_mode' => $student->study_mode,
                'current_semester' => $student->current_semester,
                'years_enrolled' => $student->years_enrolled,
                'is_graduated' => $student->is_graduated,
                'created_at' => $student->created_at->format('Y-m-d H:i'),
            ],
        ]);
    }

    /**
     * Update student
     */
    public function updateStudent(Request $request, $id): JsonResponse
    {
        $student = Student::with('user')->findOrFail($id);

        $validated = $request->validate([
            'first_name' => 'sometimes|string|max:100',
            'last_name' => 'sometimes|string|max:100',
            'email' => ['sometimes', 'email', Rule::unique('users')->ignore($student->user_id)],
            'phone' => ['sometimes', 'nullable', 'string', 'regex:/^0[5-7][0-9]{8}$/', Rule::unique('users')->ignore($student->user_id)],
            'specialty_id' => 'sometimes|exists:specialties,id',
            'study_mode' => 'sometimes|in:initial,alternance,continue',
            'current_semester' => 'sometimes|integer|min:1|max:6',
            'years_enrolled' => 'sometimes|integer|min:1',
            'is_graduated' => 'sometimes|boolean',
        ]);

        DB::beginTransaction();

        try {
            // Update user info
            if ($student->user) {
                $userUpdate = [];
                if (isset($validated['email'])) $userUpdate['email'] = $validated['email'];
                if (isset($validated['phone'])) $userUpdate['phone'] = $validated['phone'];

                if (!empty($userUpdate)) {
                    $student->user->update($userUpdate);
                }
            }

            // Update student info
            $studentUpdate = [];
            $allowedFields = ['first_name', 'last_name', 'specialty_id', 'study_mode', 'current_semester', 'years_enrolled', 'is_graduated'];
            foreach ($allowedFields as $field) {
                if (isset($validated[$field])) {
                    $studentUpdate[$field] = $validated[$field];
                }
            }

            if (!empty($studentUpdate)) {
                $student->update($studentUpdate);
            }

            DB::commit();

            return response()->json([
                'message' => 'Étudiant mis à jour avec succès',
                'student' => [
                    'id' => $student->id,
                    'registration_number' => $student->registration_number,
                    'full_name' => $student->full_name,
                    'email' => $student->user->email ?? null,
                    'specialty' => $student->specialty->name,
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete student
     */
    public function deleteStudent($id): JsonResponse
    {
        $student = Student::with('user')->findOrFail($id);

        DB::beginTransaction();

        try {
            $registrationNumber = $student->registration_number;
            $user = $student->user;

            // Delete student
            $student->delete();

            // Delete user if exists
            if ($user) {
                $user->delete();
            }

            // Make registration number available again
            RegistrationNumber::where('number', $registrationNumber)
                ->update(['is_used' => false, 'used_at' => null]);

            DB::commit();

            return response()->json([
                'message' => 'Étudiant supprimé avec succès',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get pending student registrations
     */
    public function pendingRegistrations(): JsonResponse
    {
        $pendingStudents = Student::whereNotNull('user_id')
            ->whereHas('user', function ($query) {
                $query->where('is_approved', false);
            })
            ->with(['user', 'specialty'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($student) {
                return [
                    'id' => $student->id,
                    'user_id' => $student->user_id,
                    'registration_number' => $student->registration_number,
                    'full_name' => $student->full_name,
                    'email' => $student->user->email,
                    'phone' => $student->user->phone,
                    'specialty' => $student->specialty->name,
                    'study_mode' => $student->study_mode,
                    'registered_at' => $student->user->created_at->format('Y-m-d H:i'),
                ];
            });

        return response()->json([
            'pending_registrations' => $pendingStudents,
            'count' => $pendingStudents->count(),
        ]);
    }

    /**
     * Approve student registration
     */
    public function approveRegistration(Request $request, $studentId): JsonResponse
    {
        $student = Student::with('user')->findOrFail($studentId);

        if (!$student->user) {
            return response()->json([
                'message' => 'Étudiant sans compte',
            ], 400);
        }

        if ($student->user->is_approved) {
            return response()->json([
                'message' => 'Déjà approuvé',
            ], 400);
        }

        $student->user->update(['is_approved' => true]);

        return response()->json([
            'message' => 'Inscription approuvée',
            'student' => [
                'registration_number' => $student->registration_number,
                'full_name' => $student->full_name,
                'email' => $student->user->email,
            ],
        ]);
    }

    /**
     * Reject student registration
     */
    public function rejectRegistration(Request $request, $studentId): JsonResponse
    {
        $validated = $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $student = Student::with('user')->findOrFail($studentId);

        if (!$student->user) {
            return response()->json([
                'message' => 'Étudiant sans compte',
            ], 400);
        }

        DB::beginTransaction();

        try {
            $registrationNumber = $student->registration_number;
            $user = $student->user;

            $student->delete();
            $user->delete();

            RegistrationNumber::where('number', $registrationNumber)
                ->update(['is_used' => false, 'used_at' => null]);

            DB::commit();

            return response()->json([
                'message' => 'Inscription rejetée. Le numéro est à nouveau disponible.',
                'reason' => $request->reason,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Reset student password
     */
    public function resetStudentPassword(Request $request, $studentId): JsonResponse
    {
        $validated = $request->validate([
            'new_password' => 'required|string|min:6',
        ]);

        $student = Student::with('user')->findOrFail($studentId);

        if (!$student->user) {
            return response()->json([
                'message' => 'Étudiant sans compte utilisateur',
            ], 400);
        }

        $student->user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return response()->json([
            'message' => 'Mot de passe réinitialisé avec succès',
            'registration_number' => $student->registration_number,
            'new_password' => $request->new_password,
        ]);
    }

    // ═══════════════════════════════════════════════════════════
    // TEACHER MANAGEMENT (FULL CRUD)
    // ═══════════════════════════════════════════════════════════

    /**
     * Get all teachers
     */
    public function getTeachers(Request $request): JsonResponse
    {
        $query = Teacher::with(['user', 'modules']);

        // Filter by approval status
        if ($request->has('approved')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('is_approved', $request->boolean('approved'));
            });
        }

        // Search by name
        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('specialization', 'like', "%{$search}%")
                  ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
            });
        }

        $teachers = $query->orderBy('last_name')->get()->map(function ($teacher) {
            return [
                'id' => $teacher->id,
                'full_name' => $teacher->full_name,
                'first_name' => $teacher->first_name,
                'last_name' => $teacher->last_name,
                'email' => $teacher->user->email ?? null,
                'phone' => $teacher->user->phone ?? null,
                'specialization' => $teacher->specialization,
                'is_approved' => $teacher->user ? $teacher->user->is_approved : false,
                'modules_count' => $teacher->modules->count(),
                'created_at' => $teacher->created_at->format('Y-m-d'),
            ];
        });

        return response()->json([
            'teachers' => $teachers,
            'count' => $teachers->count(),
        ]);
    }

    /**
     * Get single teacher details
     */
    public function getTeacher($id): JsonResponse
    {
        $teacher = Teacher::with(['user', 'modules.specialty'])->findOrFail($id);

        return response()->json([
            'teacher' => [
                'id' => $teacher->id,
                'first_name' => $teacher->first_name,
                'last_name' => $teacher->last_name,
                'full_name' => $teacher->full_name,
                'email' => $teacher->user->email ?? null,
                'phone' => $teacher->user->phone ?? null,
                'specialization' => $teacher->specialization,
                'is_approved' => $teacher->user ? $teacher->user->is_approved : false,
                'modules' => $teacher->modules->map(function($module) {
                    return [
                        'id' => $module->id,
                        'name' => $module->name,
                        'code' => $module->code,
                        'specialty' => $module->specialty->name,
                        'hours_per_week' => $module->hours_per_week,
                    ];
                }),
                'created_at' => $teacher->created_at->format('Y-m-d H:i'),
            ],
        ]);
    }

    /**
     * Create teacher
     */
    public function createTeacher(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|regex:/^0[5-7][0-9]{8}$/|unique:users,phone',
            'password' => 'required|string|min:6',
            'specialization' => 'required|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            $user = User::create([
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'role' => 'teacher',
                'is_approved' => true,
            ]);

            $teacher = Teacher::create([
                'user_id' => $user->id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'specialization' => $request->specialization,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Enseignant créé avec succès',
                'teacher' => [
                    'id' => $teacher->id,
                    'full_name' => $teacher->full_name,
                    'email' => $user->email,
                    'specialization' => $teacher->specialization,
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update teacher
     */
    public function updateTeacher(Request $request, $id): JsonResponse
    {
        $teacher = Teacher::with('user')->findOrFail($id);

        $validated = $request->validate([
            'first_name' => 'sometimes|string|max:100',
            'last_name' => 'sometimes|string|max:100',
            'email' => ['sometimes', 'email', Rule::unique('users')->ignore($teacher->user_id)],
            'phone' => ['sometimes', 'nullable', 'string', 'regex:/^0[5-7][0-9]{8}$/', Rule::unique('users')->ignore($teacher->user_id)],
            'specialization' => 'sometimes|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            if ($teacher->user) {
                $userUpdate = [];
                if (isset($validated['email'])) $userUpdate['email'] = $validated['email'];
                if (isset($validated['phone'])) $userUpdate['phone'] = $validated['phone'];

                if (!empty($userUpdate)) {
                    $teacher->user->update($userUpdate);
                }
            }

            $teacherUpdate = [];
            $allowedFields = ['first_name', 'last_name', 'specialization'];
            foreach ($allowedFields as $field) {
                if (isset($validated[$field])) {
                    $teacherUpdate[$field] = $validated[$field];
                }
            }

            if (!empty($teacherUpdate)) {
                $teacher->update($teacherUpdate);
            }

            DB::commit();

            return response()->json([
                'message' => 'Enseignant mis à jour avec succès',
                'teacher' => [
                    'id' => $teacher->id,
                    'full_name' => $teacher->full_name,
                    'email' => $teacher->user->email ?? null,
                    'specialization' => $teacher->specialization,
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete teacher
     */
    public function deleteTeacher($id): JsonResponse
    {
        $teacher = Teacher::with('user')->findOrFail($id);

        DB::beginTransaction();

        try {
            $user = $teacher->user;

            $teacher->delete();

            if ($user) {
                $user->delete();
            }

            DB::commit();

            return response()->json([
                'message' => 'Enseignant supprimé avec succès',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Reset teacher password
     */
    public function resetTeacherPassword(Request $request, $teacherId): JsonResponse
    {
        $validated = $request->validate([
            'new_password' => 'required|string|min:6',
        ]);

        $teacher = Teacher::with('user')->findOrFail($teacherId);

        if (!$teacher->user) {
            return response()->json([
                'message' => 'Enseignant sans compte utilisateur',
            ], 400);
        }

        $teacher->user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return response()->json([
            'message' => 'Mot de passe réinitialisé avec succès',
            'teacher' => $teacher->full_name,
            'new_password' => $request->new_password,
        ]);
    }

    // ═══════════════════════════════════════════════════════════
    // SPECIALTY MANAGEMENT (FULL CRUD)
    // ═══════════════════════════════════════════════════════════

    /**
     * Get all specialties
     */
    public function getSpecialties(Request $request): JsonResponse
    {
        $query = Specialty::withCount('students', 'modules');

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $specialties = $query->orderBy('name')->get()->map(function($specialty) {
            return [
                'id' => $specialty->id,
                'name' => $specialty->name,
                'code' => $specialty->code,
                'description' => $specialty->description,
                'duration_semesters' => $specialty->duration_semesters,
                'is_active' => $specialty->is_active,
                'students_count' => $specialty->students_count,
                'modules_count' => $specialty->modules_count,
                'created_at' => $specialty->created_at->format('Y-m-d'),
            ];
        });

        return response()->json([
            'specialties' => $specialties,
            'count' => $specialties->count(),
        ]);
    }

    /**
     * Get single specialty
     */
    public function getSpecialty($id): JsonResponse
    {
        $specialty = Specialty::with(['modules', 'students'])
            ->withCount('students', 'modules')
            ->findOrFail($id);

        return response()->json([
            'specialty' => [
                'id' => $specialty->id,
                'name' => $specialty->name,
                'code' => $specialty->code,
                'description' => $specialty->description,
                'duration_semesters' => $specialty->duration_semesters,
                'is_active' => $specialty->is_active,
                'cover_image' => $specialty->cover_image,
                'brochure_path' => $specialty->brochure_path,
                'brochure_name' => $specialty->brochure_name,
                'students_count' => $specialty->students_count,
                'modules_count' => $specialty->modules_count,
                'modules' => $specialty->modules->map(function($module) {
                    return [
                        'id' => $module->id,
                        'name' => $module->name,
                        'code' => $module->code,
                        'semester' => $module->semester,
                        'coefficient' => $module->coefficient,
                        'hours_per_week' => $module->hours_per_week,
                    ];
                }),
            ],
        ]);
    }

    /**
     * Create specialty
     */
    public function createSpecialty(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:specialties,name',
            'code' => 'required|string|max:10|unique:specialties,code',
            'description' => 'nullable|string',
            'duration_semesters' => 'required|integer|min:1|max:10',
            'is_active' => 'sometimes|boolean',
        ]);

        $specialty = Specialty::create($validated);

        return response()->json([
            'message' => 'Spécialité créée avec succès',
            'specialty' => [
                'id' => $specialty->id,
                'name' => $specialty->name,
                'code' => $specialty->code,
                'duration_semesters' => $specialty->duration_semesters,
            ],
        ], 201);
    }

    /**
     * Update specialty
     */
    public function updateSpecialty(Request $request, $id): JsonResponse
    {
        $specialty = Specialty::findOrFail($id);

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255', Rule::unique('specialties')->ignore($id)],
            'code' => ['sometimes', 'string', 'max:10', Rule::unique('specialties')->ignore($id)],
            'description' => 'sometimes|nullable|string',
            'duration_semesters' => 'sometimes|integer|min:1|max:10',
            'is_active' => 'sometimes|boolean',
        ]);

        $specialty->update($validated);

        return response()->json([
            'message' => 'Spécialité mise à jour avec succès',
            'specialty' => [
                'id' => $specialty->id,
                'name' => $specialty->name,
                'code' => $specialty->code,
                'duration_semesters' => $specialty->duration_semesters,
                'is_active' => $specialty->is_active,
            ],
        ]);
    }

    /**
     * Delete specialty
     */
    public function deleteSpecialty($id): JsonResponse
    {
        $specialty = Specialty::withCount('students', 'modules')->findOrFail($id);

        if ($specialty->students_count > 0) {
            return response()->json([
                'message' => 'Impossible de supprimer une spécialité avec des étudiants',
            ], 400);
        }

        if ($specialty->modules_count > 0) {
            return response()->json([
                'message' => 'Impossible de supprimer une spécialité avec des modules',
            ], 400);
        }

        $specialty->delete();

        return response()->json([
            'message' => 'Spécialité supprimée avec succès',
        ]);
    }

    /**
     * Toggle specialty active status
     */
    public function toggleSpecialtyStatus($id): JsonResponse
    {
        $specialty = Specialty::findOrFail($id);

        $specialty->update([
            'is_active' => !$specialty->is_active,
        ]);

        return response()->json([
            'message' => 'Statut modifié avec succès',
            'specialty' => [
                'id' => $specialty->id,
                'name' => $specialty->name,
                'is_active' => $specialty->is_active,
            ],
        ]);
    }

    // ═══════════════════════════════════════════════════════════
    // MODULE MANAGEMENT (FULL CRUD)
    // ═══════════════════════════════════════════════════════════

    /**
     * Get all modules
     */
    public function getModules(Request $request): JsonResponse
    {
        $query = Module::with(['specialty', 'teachers']);

        if ($request->specialty_id) {
            $query->where('specialty_id', $request->specialty_id);
        }

        if ($request->semester) {
            $query->where('semester', $request->semester);
        }

        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $modules = $query->orderBy('semester')->orderBy('name')->get()->map(function($module) {
            return [
                'id' => $module->id,
                'name' => $module->name,
                'code' => $module->code,
                'specialty' => [
                    'id' => $module->specialty->id,
                    'name' => $module->specialty->name,
                ],
                'semester' => $module->semester,
                'coefficient' => $module->coefficient,
                'hours_per_week' => $module->hours_per_week,
                'teachers_count' => $module->teachers->count(),
                'created_at' => $module->created_at->format('Y-m-d'),
            ];
        });

        return response()->json([
            'modules' => $modules,
            'count' => $modules->count(),
        ]);
    }

    /**
     * Get single module
     */
    public function getModule($id): JsonResponse
    {
        $module = Module::with(['specialty', 'teachers'])->findOrFail($id);

        return response()->json([
            'module' => [
                'id' => $module->id,
                'name' => $module->name,
                'code' => $module->code,
                'description' => $module->description,
                'specialty' => [
                    'id' => $module->specialty->id,
                    'name' => $module->specialty->name,
                    'code' => $module->specialty->code,
                ],
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
            ],
        ]);
    }

    /**
     * Create module
     */
    public function createModule(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:modules,code',
            'description' => 'nullable|string',
            'specialty_id' => 'required|exists:specialties,id',
            'semester' => 'required|integer|min:1|max:6',
            'coefficient' => 'required|numeric|min:0.5|max:10',
            'hours_per_week' => 'required|integer|min:1|max:40',
        ]);

        $module = Module::create($validated);

        return response()->json([
            'message' => 'Module créé avec succès',
            'module' => [
                'id' => $module->id,
                'name' => $module->name,
                'code' => $module->code,
                'specialty' => $module->specialty->name,
                'semester' => $module->semester,
            ],
        ], 201);
    }

    /**
     * Update module
     */
    public function updateModule(Request $request, $id): JsonResponse
    {
        $module = Module::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'code' => ['sometimes', 'string', 'max:50', Rule::unique('modules')->ignore($id)],
            'description' => 'sometimes|nullable|string',
            'specialty_id' => 'sometimes|exists:specialties,id',
            'semester' => 'sometimes|integer|min:1|max:6',
            'coefficient' => 'sometimes|numeric|min:0.5|max:10',
            'hours_per_week' => 'sometimes|integer|min:1|max:40',
        ]);

        $module->update($validated);

        return response()->json([
            'message' => 'Module mis à jour avec succès',
            'module' => [
                'id' => $module->id,
                'name' => $module->name,
                'code' => $module->code,
                'specialty' => $module->specialty->name,
            ],
        ]);
    }

    /**
     * Delete module
     */
    public function deleteModule($id): JsonResponse
    {
        $module = Module::findOrFail($id);

        // Check if module has grades or attendance
        $hasGrades = \App\Models\Grade::where('module_id', $id)->exists();
        $hasAttendance = \App\Models\Attendance::where('module_id', $id)->exists();

        if ($hasGrades || $hasAttendance) {
            return response()->json([
                'message' => 'Impossible de supprimer un module avec des notes ou des présences',
            ], 400);
        }

        $module->delete();

        return response()->json([
            'message' => 'Module supprimé avec succès',
        ]);
    }

    /**
     * Assign teacher to module
     */
    public function assignTeacherToModule(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'module_id' => 'required|exists:modules,id',
            'teacher_id' => 'required|exists:teachers,id',
        ]);

        $module = Module::findOrFail($request->module_id);
        $teacher = Teacher::findOrFail($request->teacher_id);

        // Check if already assigned
        if ($module->teachers()->where('teacher_id', $teacher->id)->exists()) {
            return response()->json([
                'message' => 'Enseignant déjà assigné à ce module',
            ], 400);
        }

        $module->teachers()->attach($teacher->id);

        return response()->json([
            'message' => 'Enseignant assigné avec succès',
            'module' => $module->name,
            'teacher' => $teacher->full_name,
        ]);
    }

    /**
     * Remove teacher from module
     */
    public function removeTeacherFromModule(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'module_id' => 'required|exists:modules,id',
            'teacher_id' => 'required|exists:teachers,id',
        ]);

        $module = Module::findOrFail($request->module_id);
        $teacher = Teacher::findOrFail($request->teacher_id);

        $module->teachers()->detach($teacher->id);

        return response()->json([
            'message' => 'Enseignant retiré du module',
            'module' => $module->name,
            'teacher' => $teacher->full_name,
        ]);
    }
}
