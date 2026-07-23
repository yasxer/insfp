<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AdvancementReview;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AdvancementReviewController extends Controller
{
    /**
     * GET /admin/advancement-reviews
     * List students awaiting a manual advancement decision (failed a semester).
     */
    public function index(Request $request): JsonResponse
    {
        $status = $request->get('status', 'pending');

        $query = AdvancementReview::with([
            'student:id,first_name,last_name,registration_number,specialty_id,study_mode,current_semester',
            'student.specialty:id,name,code',
        ])->orderByDesc('created_at');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $reviews = $query->get()->map(fn ($r) => [
            'id'             => $r->id,
            'student_id'     => $r->student_id,
            'student_name'   => $r->student?->full_name,
            'registration'   => $r->student?->registration_number,
            'specialty'      => $r->student?->specialty?->name,
            'study_mode'     => $r->student?->study_mode,
            'semester'       => $r->semester,
            'academic_year'  => $r->academic_year,
            'average'        => $r->average,
            'status'         => $r->status,
            'resolved_at'    => $r->resolved_at,
        ]);

        return response()->json([
            'success' => true,
            'pending_count' => AdvancementReview::pending()->count(),
            'data' => $reviews,
        ]);
    }

    /**
     * POST /admin/advancement-reviews/{id}/resolve
     * decision: redouble | advance | exclude | wait
     */
    public function resolve(Request $request, $id): JsonResponse
    {
        $validated = $request->validate([
            'decision' => 'required|in:redouble,advance,exclude,wait',
            'reason'   => 'nullable|string|max:255',
        ]);

        $review = AdvancementReview::with('student.specialty')->findOrFail($id);
        $student = $review->student;

        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Étudiant introuvable.'], 404);
        }

        switch ($validated['decision']) {
            case 'redouble':
                // Stays in the same semester; nothing to change on the student.
                $review->update(['status' => 'redoubled', 'resolved_at' => now()]);
                $message = 'Étudiant maintenu (redouble le semestre).';
                break;

            case 'advance':
                $maxSemester = $student->specialty->duration_semesters ?? 6;
                if ($student->current_semester >= $maxSemester) {
                    $student->update([
                        'is_graduated'        => true,
                        'graduation_year'     => (int) substr($review->academic_year, 0, 4),
                        'graduation_semester' => $student->current_semester,
                        'final_gpa'           => $review->average,
                    ]);
                    $message = 'Étudiant diplômé (passage forcé du dernier semestre).';
                } else {
                    $student->increment('current_semester');
                    $message = 'Étudiant passé au semestre suivant (décision manuelle).';
                }
                $review->update(['status' => 'advanced', 'resolved_at' => now()]);
                break;

            case 'exclude':
                $student->update([
                    'is_excluded'      => true,
                    'excluded_at'      => now(),
                    'exclusion_reason' => $validated['reason'] ?? null,
                ]);
                $review->update(['status' => 'excluded', 'resolved_at' => now()]);
                $message = 'Étudiant exclu du cycle.';
                break;

            case 'wait':
            default:
                $review->update(['status' => 'pending', 'resolved_at' => null]);
                $message = 'Décision reportée.';
                break;
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'pending_count' => AdvancementReview::pending()->count(),
        ]);
    }
}
