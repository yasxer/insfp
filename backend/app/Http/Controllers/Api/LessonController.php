<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class LessonController extends Controller
{
    /**
     * Get all modules with lesson count for student
     * GET /api/student/lessons/modules
     */
    public function modules(Request $request): JsonResponse
    {
        $user = $request->user();
        $student = $user->student;

        if (!$student) {
            return response()->json(['message' => 'Student profile not found'], 404);
        }

        // Get modules for student's specialty and semester
        $modules = Module::where('specialty_id', $student->specialty_id)
            ->where('semester', $student->current_semester)
            ->withCount('lessons')
            ->with('teachers')
            ->get()
            ->map(function($module) {
                return [
                    'id' => $module->id,
                    'name' => $module->name,
                    'code' => $module->code,
                    'lessons_count' => $module->lessons_count,
                    'teachers' => $module->teachers->map(fn($t) => $t->name),
                ];
            });

        return response()->json(['modules' => $modules]);
    }

    /**
     * Get lessons for a specific module
     * GET /api/student/lessons/modules/{moduleId}
     */
    public function moduleDetails(Request $request, $moduleId): JsonResponse
    {
        $user = $request->user();
        $student = $user->student;

        $module = Module::where('id', $moduleId)
            ->where('specialty_id', $student->specialty_id)
            ->with(['lessons' => function($query) {
                $query->orderBy('created_at', 'desc');
            }])
            ->firstOrFail();

        $lessons = $module->lessons->map(function($lesson) use ($user) {
            return [
                'id' => $lesson->id,
                'title' => $lesson->title,
                'description' => $lesson->description,
                'file_path' => $lesson->file_path,
                'file_name' => $lesson->file_name,
                'file_size' => $lesson->file_size,
                'is_viewed' => false, // Simplified - always show as new
                'created_at' => $lesson->created_at->format('Y-m-d'),
            ];
        });

        return response()->json([
            'module' => [
                'id' => $module->id,
                'name' => $module->name,
                'code' => $module->code,
            ],
            'lessons' => $lessons,
        ]);
    }

    /**
     * Download lesson file
     * GET /api/student/lessons/{id}/download
     */
    public function download(Request $request, $id): mixed
    {
        $user = $request->user();
        $lesson = Lesson::findOrFail($id);

        if (!Storage::exists($lesson->file_path)) {
            return response()->json(['message' => 'File not found'], 404);
        }

        return Storage::download($lesson->file_path, $lesson->file_name);
    }

    /**
     * Get new lessons count
     * GET /api/student/lessons/new/count
     */
    public function newCount(Request $request): JsonResponse
    {
        $user = $request->user();
        $student = $user->student;

        // Simplified - count all lessons for student's modules
        $newCount = Lesson::whereHas('module', function($q) use ($student) {
                $q->where('specialty_id', $student->specialty_id)
                  ->where('semester', $student->current_semester);
            })
            ->where('created_at', '>=', now()->subDays(7)) // Lessons from last 7 days
            ->count();

        return response()->json(['new_count' => $newCount]);
    }
}
