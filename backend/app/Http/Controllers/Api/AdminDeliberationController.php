<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Deliberation;
use App\Models\Student;
use App\Models\SessionSpecialty;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AdminDeliberationController extends Controller
{
    public function index(Request $request): JsonResponse
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
            'grades.module' // load modules for calculating average
        ])
        ->get()
        ->map(function($student) use ($request) {
            $deliberation = $student->deliberations->first();

            // Calculate automatic average from grades for the targeted semester
            $totalPoints = 0;
            $totalCoeff = 0;
            $modulesGraded = [];

            foreach ($student->grades as $grade) {
                if ($grade->semester == $request->semester && $grade->module) {
                    $modId = $grade->module->id;
                    if (!isset($modulesGraded[$modId])) {
                        $modulesGraded[$modId] = [
                            'sum' => 0,
                            'count' => 0,
                            'coeff' => $grade->module->coefficient
                        ];
                    }
                    $modulesGraded[$modId]['sum'] += $grade->grade;
                    $modulesGraded[$modId]['count']++;
                }
            }

            foreach ($modulesGraded as $m) {
                $modAvg = $m['sum'] / $m['count'];
                $totalPoints += ($modAvg * $m['coeff']);
                $totalCoeff += $m['coeff'];
            }

            $calculatedAverage = $totalCoeff > 0 ? round($totalPoints / $totalCoeff, 2) : 0;
            $calculatedResult = $calculatedAverage >= 10 ? 'passed' : 'failed';

            return [
                'id' => $student->id,
                'name' => $student->full_name,
                'registration_number' => $student->registration_number ?? 'N/A',
                'auto_semester' => $request->semester,
                'calculated_average' => $calculatedAverage,
                'calculated_result' => $calculatedResult,                'deliberation' => $deliberation ? [
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
            'deliberation_date' => 'required|date'
        ]);

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
