<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Specialty;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SpecialtyController extends Controller
{
    /**
     * Get all specialties with details
     */
    public function index(): JsonResponse
    {
        $specialties = Specialty::withCount(['students', 'modules'])
            ->with(['modules.teachers'])
            ->get()
            ->map(function ($specialty) {
                $teachersCount = $specialty->modules->flatMap(function ($module) {
                    return $module->teachers;
                })->unique('id')->count();

                return [
                    'id' => $specialty->id,
                    'name' => $specialty->name,
                    'code' => $specialty->code,
                    'description' => $specialty->description,
                    'students_count' => $specialty->students_count,
                    'is_active' => $specialty->is_active,
                    'classes_count' => 1,
                    'teachers_count' => $teachersCount,
                    'current_semester' => $specialty->current_semester,
                    'duration_years' => $specialty->duration_years,
                    'program_url' => $specialty->program_pdf_path ? asset(Storage::url($specialty->program_pdf_path)) : null,
                    'cover_image_url' => $specialty->cover_image ? asset(Storage::url($specialty->cover_image)) : null,
                ];
            });

        return response()->json([
            'specialties' => $specialties,
        ]);
    }

    /**
     * Get single specialty
     */
    public function show($id): JsonResponse
    {
        $specialty = Specialty::with(['modules', 'modules.teachers'])->findOrFail($id);

        $teachers = $specialty->modules->flatMap(function ($module) {
            return $module->teachers;
        })->unique('id')->values();

        return response()->json([
            'specialty' => [
                'id' => $specialty->id,
                'name' => $specialty->name,
                'code' => $specialty->code,
                'description' => $specialty->description,
                'duration_semesters' => $specialty->duration_semesters,
                'duration_years' => $specialty->duration_years,
                'current_semester' => $specialty->current_semester,
                'program_url' => $specialty->program_pdf_path ? asset(Storage::url($specialty->program_pdf_path)) : null,
                'cover_image_url' => $specialty->cover_image ? asset(Storage::url($specialty->cover_image)) : null,
                'modules_count' => $specialty->modules->count(),
                'modules' => $specialty->modules->map(function ($module) {
                    return [
                        'id' => $module->id,
                        'name' => $module->name,
                        'code' => $module->code,
                        'description' => $module->description,
                        'specialty_id' => $module->specialty_id,
                        'semester' => $module->semester,
                        'coefficient' => $module->coefficient,
                        'hours_per_week' => $module->hours_per_week,
                    ];
                }),
                'teachers' => $teachers->map(function($teacher) {
                    return [
                        'id' => $teacher->id,
                        'full_name' => $teacher->full_name,
                        'specialization' => $teacher->specialization,
                    ];
                }),
            ]
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:specialties',
            'description' => 'nullable|string',
            'duration_years' => 'required|numeric',
            'program_pdf' => 'nullable|file|mimes:pdf|max:10240',
            'cover_image' => 'nullable|image|max:5120', // 5MB max
        ]);

        $programPath = null;
        if ($request->hasFile('program_pdf')) {
            $programPath = $request->file('program_pdf')->store('specialties/programs', 'public');
        }

        $coverPath = null;
        if ($request->hasFile('cover_image')) {
            $coverPath = $request->file('cover_image')->store('specialties/covers', 'public');
        }

        $specialty = Specialty::create([
            'name' => $validated['name'],
            'code' => $validated['code'],
            'description' => $validated['description'] ?? null,
            'duration_years' => $validated['duration_years'],
            'current_semester' => 1,
            'program_pdf_path' => $programPath,
            'cover_image' => $coverPath,
            'is_active' => true,
        ]);

        return response()->json($specialty, 201);
    }

    public function update(Request $request, $id)
    {
        $specialty = Specialty::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'code' => 'sometimes|string|max:50|unique:specialties,code,' . $id,
            'description' => 'nullable|string',
            'duration_years' => 'sometimes|numeric',
            'current_semester' => 'sometimes|integer',
            'program_pdf' => 'nullable|file|mimes:pdf|max:10240',
            'cover_image' => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('program_pdf')) {
            if ($specialty->program_pdf_path) {
                Storage::disk('public')->delete($specialty->program_pdf_path);
            }
            $validated['program_pdf_path'] = $request->file('program_pdf')->store('specialties/programs', 'public');
        }

        if ($request->hasFile('cover_image')) {
            if ($specialty->cover_image) {
                Storage::disk('public')->delete($specialty->cover_image);
            }
            $validated['cover_image'] = $request->file('cover_image')->store('specialties/covers', 'public');
        }

        $specialty->update($validated);

        return response()->json($specialty);
    }

    public function destroy($id)
    {
        $specialty = Specialty::findOrFail($id);
        if ($specialty->program_pdf_path) {
            Storage::disk('public')->delete($specialty->program_pdf_path);
        }
        if ($specialty->cover_image) {
            Storage::disk('public')->delete($specialty->cover_image);
        }
        $specialty->delete();
        return response()->json(null, 204);
    }
}
