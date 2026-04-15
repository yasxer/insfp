<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class StudentDeliberationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $student = $request->user()->student;

        if (!$student) {
            return response()->json(['message' => 'Student not found'], 404);
        }

        $deliberations = $student->deliberations()
            ->orderBy('semester', 'asc')
            ->get()
            ->map(function($deliberation) {
                return [
                    'id' => $deliberation->id,
                    'semester' => $deliberation->semester,
                    'academic_year' => $deliberation->academic_year,
                    'average' => $deliberation->average,
                    'result' => $deliberation->result,
                    'observations' => $deliberation->observations,
                    'deliberation_date' => $deliberation->deliberation_date->format('Y-m-d')
                ];
            });

        return response()->json(['deliberations' => $deliberations]);
    }
}
