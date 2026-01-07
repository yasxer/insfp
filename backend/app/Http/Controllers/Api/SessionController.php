<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TrainingSession;
use App\Models\SessionSpecialty;
use App\Models\Specialty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SessionController extends Controller
{
    /**
     * Get all sessions (current and upcoming)
     */
    public function index(Request $request)
    {
        $query = TrainingSession::with(['sessionSpecialties.specialty'])
            ->withCount('students');

        // Filter by status
        if ($request->has('status')) {
            if ($request->status === 'current') {
                $query->current();
            } elseif ($request->status === 'archived') {
                $query->archived();
            }
        } else {
            // Default: show current and upcoming only
            $query->current();
        }

        $sessions = $query->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get()
            ->map(function ($session) {
                return [
                    'id' => $session->id,
                    'name' => $session->name,
                    'month' => $session->month,
                    'year' => $session->year,
                    'start_date' => $session->start_date->format('Y-m-d'),
                    'end_date' => $session->end_date->format('Y-m-d'),
                    'status' => $session->status,
                    'is_active' => $session->is_active,
                    'students_count' => $session->students_count,
                    'specialties_by_type' => $this->groupSpecialtiesByType($session->sessionSpecialties)
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $sessions
        ]);
    }

    /**
     * Get archived sessions
     */
    public function archived()
    {
        $sessions = TrainingSession::with(['sessionSpecialties.specialty'])
            ->withCount('students')
            ->archived()
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get()
            ->map(function ($session) {
                return [
                    'id' => $session->id,
                    'name' => $session->name,
                    'month' => $session->month,
                    'year' => $session->year,
                    'start_date' => $session->start_date->format('Y-m-d'),
                    'end_date' => $session->end_date->format('Y-m-d'),
                    'status' => $session->status,
                    'is_active' => $session->is_active,
                    'students_count' => $session->students_count,
                    'specialties_by_type' => $this->groupSpecialtiesByType($session->sessionSpecialties)
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $sessions
        ]);
    }

    /**
     * Get single session with details
     */
    public function show($id)
    {
        $session = TrainingSession::with(['sessionSpecialties.specialty', 'sessionSpecialties.students'])
            ->withCount('students')
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $session->id,
                'name' => $session->name,
                'month' => $session->month,
                'year' => $session->year,
                'start_date' => $session->start_date->format('Y-m-d'),
                'end_date' => $session->end_date->format('Y-m-d'),
                'status' => $session->status,
                'is_active' => $session->is_active,
                'students_count' => $session->students_count,
                'specialties_by_type' => $this->groupSpecialtiesByType($session->sessionSpecialties)
            ]
        ]);
    }

    /**
     * Create a new session
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:2020|max:2100',
        ]);

        // Check if session already exists
        $exists = TrainingSession::where('month', $validated['month'])
            ->where('year', $validated['year'])
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Une session existe déjà pour ce mois et cette année.'
            ], 422);
        }

        $session = TrainingSession::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Session créée avec succès.',
            'data' => $session
        ], 201);
    }

    /**
     * Update a session
     */
    public function update(Request $request, $id)
    {
        $session = TrainingSession::findOrFail($id);

        $validated = $request->validate([
            'month' => 'sometimes|integer|between:1,12',
            'year' => 'sometimes|integer|min:2020|max:2100',
            'is_active' => 'sometimes|boolean'
        ]);

        // Check for duplicate if month/year changed
        if (isset($validated['month']) || isset($validated['year'])) {
            $month = $validated['month'] ?? $session->month;
            $year = $validated['year'] ?? $session->year;

            $exists = TrainingSession::where('month', $month)
                ->where('year', $year)
                ->where('id', '!=', $id)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Une session existe déjà pour ce mois et cette année.'
                ], 422);
            }
        }

        $session->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Session mise à jour avec succès.',
            'data' => $session
        ]);
    }

    /**
     * Delete a session
     */
    public function destroy($id)
    {
        $session = TrainingSession::withCount('students')->findOrFail($id);

        if ($session->students_count > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Impossible de supprimer cette session car elle contient des étudiants.'
            ], 422);
        }

        $session->delete();

        return response()->json([
            'success' => true,
            'message' => 'Session supprimée avec succès.'
        ]);
    }

    /**
     * Add specialty to a session with study type
     */
    public function addSpecialty(Request $request, $sessionId)
    {
        $session = TrainingSession::findOrFail($sessionId);

        $validated = $request->validate([
            'specialty_id' => 'required|exists:specialties,id',
            'study_type' => ['required', Rule::in(['presential', 'apprentissage', 'cours_soir'])]
        ]);

        // Check if already exists
        $exists = SessionSpecialty::where('session_id', $sessionId)
            ->where('specialty_id', $validated['specialty_id'])
            ->where('study_type', $validated['study_type'])
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Cette spécialité existe déjà dans ce type d\'étude pour cette session.'
            ], 422);
        }

        $sessionSpecialty = SessionSpecialty::create([
            'session_id' => $sessionId,
            'specialty_id' => $validated['specialty_id'],
            'study_type' => $validated['study_type']
        ]);

        $sessionSpecialty->load('specialty');

        return response()->json([
            'success' => true,
            'message' => 'Spécialité ajoutée avec succès.',
            'data' => $sessionSpecialty
        ], 201);
    }

    /**
     * Remove specialty from a session
     */
    public function removeSpecialty($sessionId, $sessionSpecialtyId)
    {
        $sessionSpecialty = SessionSpecialty::where('session_id', $sessionId)
            ->where('id', $sessionSpecialtyId)
            ->withCount('students')
            ->firstOrFail();

        if ($sessionSpecialty->students_count > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Impossible de supprimer cette spécialité car elle contient des étudiants.'
            ], 422);
        }

        $sessionSpecialty->delete();

        return response()->json([
            'success' => true,
            'message' => 'Spécialité retirée avec succès.'
        ]);
    }

    /**
     * Get all specialties for dropdown
     */
    public function getSpecialties()
    {
        $specialties = Specialty::where('is_active', true)
            ->select('id', 'name', 'code')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $specialties
        ]);
    }

    /**
     * Get study types
     */
    public function getStudyTypes()
    {
        return response()->json([
            'success' => true,
            'data' => SessionSpecialty::STUDY_TYPES
        ]);
    }

    /**
     * Get sessions for dropdown (student assignment)
     */
    public function getSessionsForDropdown()
    {
        $sessions = TrainingSession::current()
            ->with(['sessionSpecialties.specialty'])
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get()
            ->map(function ($session) {
                return [
                    'id' => $session->id,
                    'name' => $session->name,
                    'session_specialties' => $session->sessionSpecialties->map(function ($ss) {
                        return [
                            'id' => $ss->id,
                            'specialty_id' => $ss->specialty_id,
                            'specialty_name' => $ss->specialty->name,
                            'study_type' => $ss->study_type,
                            'study_type_label' => $ss->study_type_label,
                            'label' => $ss->specialty->name . ' (' . $ss->study_type_label . ')'
                        ];
                    })
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $sessions
        ]);
    }

    /**
     * Helper: Group specialties by study type
     */
    private function groupSpecialtiesByType($sessionSpecialties)
    {
        $grouped = [
            'presential' => [],
            'apprentissage' => [],
            'cours_soir' => []
        ];

        foreach ($sessionSpecialties as $ss) {
            $grouped[$ss->study_type][] = [
                'id' => $ss->id,
                'specialty_id' => $ss->specialty_id,
                'specialty_name' => $ss->specialty->name,
                'specialty_code' => $ss->specialty->code,
                'students_count' => $ss->students_count ?? 0
            ];
        }

        return [
            [
                'type' => 'presential',
                'label' => 'Présentiel',
                'specialties' => $grouped['presential']
            ],
            [
                'type' => 'apprentissage',
                'label' => 'Apprentissage',
                'specialties' => $grouped['apprentissage']
            ],
            [
                'type' => 'cours_soir',
                'label' => 'Cours du soir',
                'specialties' => $grouped['cours_soir']
            ]
        ];
    }
}
