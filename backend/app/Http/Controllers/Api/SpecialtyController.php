<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Specialty;
use Illuminate\Http\JsonResponse;

class SpecialtyController extends Controller
{
    /**
     * Get all active specialties (public)
     */
    public function index(): JsonResponse
    {
        $specialties = Specialty::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'code', 'description', 'duration_semesters', 'cover_image']);

        return response()->json([
            'specialties' => $specialties,
        ]);
    }

    /**
     * Get single specialty (public)
     */
    public function show($id): JsonResponse
    {
        $specialty = Specialty::where('is_active', true)
            ->with(['modules' => function ($query) {
                $query->orderBy('semester')->orderBy('name');
            }])
            ->findOrFail($id);

        return response()->json([
            'specialty' => [
                'id' => $specialty->id,
                'name' => $specialty->name,
                'code' => $specialty->code,
                'description' => $specialty->description,
                'duration_semesters' => $specialty->duration_semesters,
                'modules_count' => $specialty->modules->count(),
                'modules' => $specialty->modules->map(function ($module) {
                    return [
                        'name' => $module->name,
                        'code' => $module->code,
                        'semester' => $module->semester,
                        'coefficient' => $module->coefficient,
                    ];
                }),
            ],
        ]);
    }
}
