<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TeacherLessonController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $teacher = $request->user()->teacher;

        if (!$teacher) {
            return response()->json(['message' => 'Teacher profile not found'], 404);
        }

        // Get modules assigned to this teacher with their lessons
        $modules = Module::whereHas('teachers', function ($q) use ($teacher) {
                $q->where('teacher_id', $teacher->id);
            })
            ->with(['lessons' => function($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id)->orderBy('created_at', 'desc');
            }])
            ->get()
            ->map(function($module) {
                return [
                    'id' => $module->id,
                    'name' => $module->name,
                    'code' => $module->code,
                    'lessons' => $module->lessons->map(fn($lesson) => [
                        'id' => $lesson->id,
                        'title' => $lesson->title,
                        'description' => $lesson->description,
                        'file_name' => $lesson->file_name,
                        'file_size' => $lesson->file_size,
                        'created_at' => $lesson->created_at->format('Y-m-d H:i')
                    ]),
                ];
            });

        return response()->json(['modules' => $modules]);
    }

    public function store(Request $request): JsonResponse
    {
        $teacher = $request->user()->teacher;

        $validator = Validator::make($request->all(), [
            'module_id' => 'required|exists:modules,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'required|file|max:51200' // max 50MB
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Verify the teacher teaches this module
        $isTeachingModule = Module::whereHas('teachers', function ($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        })->where('id', $request->module_id)->exists();

        if (!$isTeachingModule) {
            return response()->json(['message' => 'Unauthorized to upload lesson for this module'], 403);
        }

        $file = $request->file('file');
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('lessons', $filename, 'public');

        $lesson = Lesson::create([
            'module_id' => $request->module_id,
            'teacher_id' => $teacher->id,
            'title' => $request->title,
            'description' => $request->description,
            'file_path' => 'public/' . $path,
            'file_name' => $file->getClientOriginalName(),
            'file_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize()
        ]);

        return response()->json([
            'message' => 'Course uploaded successfully',
            'lesson' => $lesson
        ], 201);
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        $teacher = $request->user()->teacher;
        $lesson = Lesson::where('id', $id)->where('teacher_id', $teacher->id)->first();

        if (!$lesson) {
            return response()->json(['message' => 'Course not found or unauthorized'], 404);
        }

        $filePath = str_replace('public/', '', $lesson->file_path);

        if (Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        } elseif (Storage::exists($lesson->file_path)) {
            Storage::delete($lesson->file_path);
        }

        $lesson->delete();

        return response()->json(['message' => 'Course deleted successfully']);
    }
}
