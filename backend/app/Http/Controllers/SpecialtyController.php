<?php

namespace App\Http\Controllers;

use App\Models\Specialty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SpecialtyController extends Controller
{
    public function index()
    {
        $specialties = Specialty::withCount(['students', 'modules'])
            ->with(['modules.teachers']) // Eager load to count teachers
            ->get()
            ->map(function ($specialty) {
                // Calculate distinct teachers count via modules
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
                    'classes_count' => 1, // Placeholder
                    'teachers_count' => $teachersCount,
                    'current_semester' => $specialty->current_semester,
                    'duration_years' => $specialty->duration_years,
                    'program_url' => $specialty->program_pdf_path ? Storage::url($specialty->program_pdf_path) : null,
                ];
            });

        return response()->json($specialties);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:specialties',
            'description' => 'nullable|string',
            'duration_years' => 'required|numeric',
            'program_pdf' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        $path = null;
        if ($request->hasFile('program_pdf')) {
            $path = $request->file('program_pdf')->store('specialties/programs', 'public');
        }

        $specialty = Specialty::create([
            'name' => $validated['name'],
            'code' => $validated['code'],
            'description' => $validated['description'] ?? null,
            'duration_years' => $validated['duration_years'],
            'current_semester' => 1,
            'program_pdf_path' => $path,
            'is_active' => true,
        ]);

        return response()->json($specialty, 201);
    }

    public function show($id)
    {
        $specialty = Specialty::with(['modules', 'modules.teachers'])->findOrFail($id);

        $teachers = $specialty->modules->flatMap(function ($module) {
            return $module->teachers;
        })->unique('id')->values();

        return response()->json([
            'specialty' => $specialty,
            'teachers' => $teachers,
            'modules' => $specialty->modules,
            'program_url' => $specialty->program_pdf_path ? Storage::url($specialty->program_pdf_path) : null,
        ]);
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
        ]);

        if ($request->hasFile('program_pdf')) {
            if ($specialty->program_pdf_path) {
                Storage::disk('public')->delete($specialty->program_pdf_path);
            }
            $validated['program_pdf_path'] = $request->file('program_pdf')->store('specialties/programs', 'public');
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
        $specialty->delete();
        return response()->json(null, 204);
    }
}
