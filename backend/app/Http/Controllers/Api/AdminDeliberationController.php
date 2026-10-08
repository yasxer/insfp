<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Deliberation;
use App\Models\Student;
use App\Models\SessionSpecialty;
use App\Services\GradeCalculator;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AdminDeliberationController extends Controller
{
    public function index(Request $request, GradeCalculator $calculator): JsonResponse
    {
        $request->validate([
            'session_id' => 'required|exists:sessions,id',
            'specialty_id' => 'required|exists:specialties,id',
            'semester' => 'nullable|integer',
        ]);

        // Auto-assign semester if not provided
        if (!$request->semester) {
            $semStudent = Student::whereHas('sessionSpecialty', function ($query) use ($request) {
                $query->where('session_id', $request->session_id)
                      ->where('specialty_id', $request->specialty_id);
            })->first();
            $request->merge(['semester' => $semStudent ? $semStudent->current_semester : 1]);
        }

        // Get students for this session and specialty
        $students = Student::whereHas('sessionSpecialty', function ($query) use ($request) {
            $query->where('session_id', $request->session_id)
                  ->where('specialty_id', $request->specialty_id);
        })
        ->with([
            'deliberations' => function ($query) use ($request) {
                $query->where('semester', $request->semester);
            },
            'grades.module',
            'grades.exam',
        ])
        ->get()
        ->map(function($student) use ($request, $calculator) {
            $deliberation = $student->deliberations->first();

            // Same rules as the student's own grade page: (C1 + C2 + 2E) / 4 per
            // module, weighted by coefficient, published grades only.
            $semesterGrades = $student->grades->where('semester', (int) $request->semester);
            $semester = $calculator->semesterAverage($semesterGrades);
            // No published grade at all: nothing to propose (not a 0/20 failure).
            $hasGrades = !empty($semester['modules']);
            $calculatedAverage = $hasGrades ? $semester['average'] : null;
            $calculatedResult = $hasGrades ? ($calculatedAverage >= 10 ? 'passed' : 'failed') : null;

            // Rattrapage: semester average before the rattrapage session, the modules
            // to retake, and whether a rattrapage mark has been published yet.
            $before = $calculator->semesterAverage($semesterGrades, false);
            $rattrapageModuleIds = $calculator->rattrapageModules($semesterGrades);
            // Done only once every module to retake has a published rattrapage mark.
            $rattrapageDone = empty(array_diff(
                array_map('intval', $rattrapageModuleIds),
                $calculator->modulesWithRattrapage($semesterGrades)
            ));

            $rattrapageStatus = match (true) {
                !$hasGrades => null,
                empty($rattrapageModuleIds) => 'admis',
                !$rattrapageDone => 'en_attente',
                $calculatedAverage >= 10 => 'admis_apres_rattrapage',
                default => 'ajourne',
            };

            return [
                'id' => $student->id,
                'name' => $student->full_name,
                'registration_number' => $student->registration_number ?? 'N/A',
                'auto_semester' => $request->semester,
                'calculated_average' => $calculatedAverage,
                'calculated_result' => $calculatedResult,
                'average_before_rattrapage' => $hasGrades ? $before['average'] : null,
                'rattrapage_status' => $rattrapageStatus,
                'rattrapage_modules' => $semesterGrades
                    ->filter(fn ($g) => $g->module && in_array($g->module_id, $rattrapageModuleIds))
                    ->unique('module_id')
                    ->map(fn ($g) => ['id' => $g->module->id, 'name' => $g->module->name])
                    ->values(),
                'deliberation' => $deliberation ? [
                    'id' => $deliberation->id,
                    'average' => $deliberation->average,
                    'result' => $deliberation->result,
                    'observations' => $deliberation->observations,                    'deliberation_date' => $deliberation->deliberation_date,
                    'academic_year' => $deliberation->academic_year
                ] : null
            ];
        });

        return response()->json(['students' => $students]);
    }

    public function storeOrUpdate(Request $request): JsonResponse
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'semester' => 'required|integer',
            'academic_year' => 'required|string',
            'average' => 'required|numeric|min:0|max:20',
            'result' => 'required|in:passed,failed',
            'observations' => 'nullable|string',
            'deliberation_date' => 'required|date|before_or_equal:today'
        ], [
            'deliberation_date.before_or_equal' => 'La date de délibération ne peut pas être dans le futur.',
        ]);

        // The decision must follow the institute rule: admis only with a moyenne >= 10.
        $expected = $request->average >= 10 ? 'passed' : 'failed';
        if ($request->result !== $expected) {
            return response()->json([
                'message' => 'Le résultat ne correspond pas à la moyenne (admis seulement si moyenne ≥ 10).',
                'errors' => ['result' => ['Résultat incohérent avec la moyenne.']],
            ], 422);
        }

        $deliberation = Deliberation::updateOrCreate(
            [
                'student_id' => $request->student_id,
                'semester' => $request->semester,
                'academic_year' => $request->academic_year
            ],
            [
                'average' => $request->average,
                'result' => $request->result,
                'observations' => $request->observations,
                'deliberation_date' => $request->deliberation_date
            ]
        );

        return response()->json([
            'message' => 'Deliberation saved successfully',
            'deliberation' => $deliberation
        ]);
    }
}
