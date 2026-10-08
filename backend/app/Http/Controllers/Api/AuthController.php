<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Models\Student;
use App\Models\RegistrationNumber;
use App\Models\SessionSpecialty;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $user = $this->resolveCredentials($request);

        if (!$user->is_approved) {
            return response()->json([
                'message' => 'Votre inscription est en attente d\'approbation.',
            ], 403);
        }

        $profileComplete = $this->isProfileComplete($user);

        // First-party SPA authentication: log the user into the (web) session so the
        // credential lives in an httpOnly cookie the browser JS can never read —
        // instead of a bearer token in localStorage that any XSS could steal.
        Auth::guard('web')->login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        $userData = $this->getUserData($user);
        $userData['profile_complete'] = $profileComplete;

        return response()->json([
            'message' => 'Connexion réussie',
            'user' => $userData,
            'profile_complete' => $profileComplete,
        ]);
    }

    /**
     * Login for the mobile app. The app cannot use the SPA's httpOnly session
     * cookie, so it receives a Sanctum personal access token instead.
     * POST /api/mobile/login
     */
    public function mobileLogin(LoginRequest $request): JsonResponse
    {
        $user = $this->resolveCredentials($request);

        if (!$user->is_approved) {
            return response()->json([
                'message' => 'Votre inscription est en attente d\'approbation.',
            ], 403);
        }

        $profileComplete = $this->isProfileComplete($user);
        $userData = $this->getUserData($user);
        $userData['profile_complete'] = $profileComplete;

        return response()->json([
            'message' => 'Connexion réussie',
            'token' => $user->createToken('mobile')->plainTextToken,
            'user' => $userData,
            'profile_complete' => $profileComplete,
        ]);
    }

    /**
     * Revoke the token the mobile app is using.
     * POST /api/mobile/logout
     */
    public function mobileLogout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json([
            'message' => 'Déconnexion réussie',
        ]);
    }

    /**
     * Find the user by email or registration number and check the password.
     */
    private function resolveCredentials(LoginRequest $request): User
    {
        $identifier = $request->registration_number;
        $user = null;

        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            $user = User::where('email', $identifier)->first();
        } else {
            $student = Student::where('registration_number', $identifier)->with('user')->first();
            if ($student) {
                $user = $student->user;
            }
        }

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'registration_number' => ['Identifiants incorrects.'],
            ]);
        }

        return $user;
    }

    /**
     * Only students have to fill in their birth date and address.
     */
    private function isProfileComplete(User $user): bool
    {
        if ($user->role !== 'student') {
            return true;
        }

        $student = $user->student;

        return !is_null($student->date_of_birth) && !is_null($student->address);
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

            // The registration number is issued for a specific session + specialty.
            // The student must register under exactly those — this prevents both
            // mismatched enrollments and orphan students (a combo that isn't offered).
            if ((int) $request->session_id !== (int) $registrationNumber->session_id
                || (int) $request->specialty_id !== (int) $registrationNumber->specialty_id) {
                throw ValidationException::withMessages([
                    'registration_number' => ['Ce numéro ne correspond pas à la session ou à la spécialité choisie.'],
                ]);
            }

            $studyTypeMap = [
                'initial' => 'presential',
                'alternance' => 'apprentissage',
                'continue' => 'cours_soir',
            ];
            $studyType = $studyTypeMap[$request->study_mode] ?? 'presential';

            // The chosen study mode must actually be offered for this specialty in
            // this session. Without this, session_specialty_id could be null and the
            // student would never be linked to any cohort.
            $sessionSpecialty = SessionSpecialty::where('session_id', $registrationNumber->session_id)
                ->where('specialty_id', $registrationNumber->specialty_id)
                ->where('study_type', $studyType)
                ->first();

            if (!$sessionSpecialty) {
                throw ValidationException::withMessages([
                    'study_mode' => ['Ce mode d\'étude n\'est pas proposé pour cette spécialité dans cette session.'],
                ]);
            }

            $user = User::create([
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'role' => 'student',
                'is_approved' => false,
            ]);

            $student = Student::create([
                'user_id' => $user->id,
                'specialty_id' => $registrationNumber->specialty_id,
                'session_specialty_id' => $sessionSpecialty->id,
                'registration_number' => $request->registration_number,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'study_mode' => $request->study_mode,
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
        // Tear down the session so the httpOnly auth cookie is no longer valid.
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

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
     * Lookup registration number - returns session, specialty, study mode
     */
    public function lookupRegistrationNumber(Request $request): JsonResponse
    {
        $request->validate([
            'registration_number' => 'required|string',
        ]);

        $regNum = RegistrationNumber::where('number', $request->registration_number)
            ->with(['specialty', 'session'])
            ->first();

        if (!$regNum) {
            return response()->json([
                'message' => 'Numéro d\'inscription introuvable.',
            ], 404);
        }

        if ($regNum->is_used) {
            return response()->json([
                'message' => 'Ce numéro d\'inscription est déjà utilisé.',
            ], 422);
        }

        // Determine study mode from session_specialty if available
        $studyModes = [];
        $sessionSpecialties = SessionSpecialty::where('session_id', $regNum->session_id)
            ->where('specialty_id', $regNum->specialty_id)
            ->get();

        $studyTypeToMode = [
            'presential' => 'initial',
            'apprentissage' => 'alternance',
            'cours_soir' => 'continue',
        ];

        foreach ($sessionSpecialties as $ss) {
            $mode = $studyTypeToMode[$ss->study_type] ?? $ss->study_type;
            $studyModes[] = [
                'value' => $mode,
                'label' => SessionSpecialty::STUDY_TYPES[$ss->study_type] ?? $ss->study_type,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'registration_number' => $regNum->number,
                'session' => $regNum->session ? [
                    'id' => $regNum->session->id,
                    'name' => $regNum->session->name,
                ] : null,
                'specialty' => $regNum->specialty ? [
                    'id' => $regNum->specialty->id,
                    'name' => $regNum->specialty->name,
                    'code' => $regNum->specialty->code,
                ] : null,
                'study_modes' => $studyModes,
            ],
        ]);
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
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'full_name' => $user->full_name,
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
