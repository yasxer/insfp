<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Models\Student;
use App\Models\RegistrationNumber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Login with registration_number OR email + password
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $identifier = $request->registration_number;
        $user = null;

        // 1. Try to find user by email (for Admin/Teacher/Student)
        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            $user = User::where('email', $identifier)->first();
        }
        // 2. If not email, try to find student by registration number
        else {
            $student = Student::where('registration_number', $identifier)->with('user')->first();
            if ($student) {
                $user = $student->user;
            }
        }

        // 3. Validate User and Password
        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'registration_number' => ['Identifiants incorrects.'],
            ]);
        }

        // 4. Check Approval
        if (!$user->is_approved) {
            return response()->json([
                'message' => 'Votre inscription est en attente d\'approbation.',
            ], 403);
        }

        // 5. Check if profile is complete (for students only)
        $profileComplete = true;
        if ($user->role === 'student') {
            $student = $user->student;
            $profileComplete = !is_null($student->date_of_birth) && !is_null($student->address);
        }

        $token = $user->createToken('auth-token')->plainTextToken;
        $userData = $this->getUserData($user);
        $userData['profile_complete'] = $profileComplete;

        return response()->json([
            'message' => 'Connexion réussie',
            'token' => $token,
            'user' => $userData,
            'profile_complete' => $profileComplete,
        ]);
    }

    /**
     * Register - Student fills all data
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $registrationNumber = RegistrationNumber::where('number', $request->registration_number)
                ->where('is_used', false)
                ->first();

            if (!$registrationNumber) {
                throw ValidationException::withMessages([
                    'registration_number' => ['Numéro invalide ou déjà utilisé.'],
                ]);
            }

            $user = User::create([
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'role' => 'student',
                'is_approved' => false,
            ]);

            $student = Student::create([
                'user_id' => $user->id,
                'specialty_id' => $request->specialty_id,
                'registration_number' => $request->registration_number,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'study_mode' => 'initial',
                'current_semester' => 1,
                'years_enrolled' => 1,
                'is_graduated' => false,
            ]);

            $registrationNumber->update([
                'is_used' => true,
                'used_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Inscription réussie. En attente d\'approbation.',
                'student' => [
                    'registration_number' => $student->registration_number,
                    'full_name' => $student->full_name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'specialty' => $student->specialty->name,
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Change password
     */
    public function changePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Mot de passe actuel incorrect.'],
            ]);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return response()->json([
            'message' => 'Mot de passe modifié avec succès',
        ]);
    }

    /**
     * Logout - Delete current token (FIXED)
     */
    public function logout(Request $request): JsonResponse
    {
        // Solution: Use tokens() relationship
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Déconnexion réussie',
        ]);
    }
    /**
     * Get current user
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        $userData = $this->getUserData($user);

        return response()->json($userData);
    }

    /**
     * Helper: Get user data with role info
     */
    private function getUserData(User $user): array
    {
        $data = [
            'id' => $user->id,
            'email' => $user->email,
            'phone' => $user->phone,
            'role' => $user->role,
            'is_approved' => $user->is_approved,
        ];

        if ($user->role === 'student') {
            $student = $user->student()->with('specialty')->first();
            if ($student) {
                $data['student'] = [
                    'id' => $student->id,
                    'registration_number' => $student->registration_number,
                    'full_name' => $student->full_name,
                    'first_name' => $student->first_name,
                    'last_name' => $student->last_name,
                    'study_mode' => $student->study_mode,
                    'current_semester' => $student->current_semester,
                    'specialty' => $student->specialty ? [
                        'id' => $student->specialty->id,
                        'name' => $student->specialty->name,
                        'code' => $student->specialty->code,
                    ] : null,
                ];
            }
        } elseif ($user->role === 'teacher') {
            $teacher = $user->teacher;
            if ($teacher) {
                $data['teacher'] = [
                    'id' => $teacher->id,
                    'full_name' => $teacher->full_name,
                    'specialization' => $teacher->specialization,
                ];
            }
        } elseif ($user->role === 'administration') {
            $admin = $user->administration;
            if ($admin) {
                $data['administration'] = [
                    'id' => $admin->id,
                    'full_name' => $admin->full_name,
                    'position' => $admin->position,
                ];
            }
        }

        return $data;
    }
}
