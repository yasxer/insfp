<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Homework;
use App\Models\HomeworkSubmission;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class TeacherHomeworkController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $teacherId = $request->user()->teacher->id;
        $homeworks = Homework::where('teacher_id', $teacherId)
            ->with(['module:id,name', 'submissions.student'])
            ->withCount('submissions')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($hw) {
                return [
                    'id' => $hw->id,
                    'title' => $hw->title,
                    'description' => $hw->description,
                    'due_date' => $hw->due_date,
                    'submission_type' => $hw->submission_type,
                    'file_path' => $hw->file_path ? asset('storage/' . $hw->file_path) : null,
                    'module' => $hw->module,
                    'submissions_count' => $hw->submissions_count,
                    'created_at' => $hw->created_at,
                ];
            });

        return response()->json(['data' => $homeworks]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'module_id' => 'required|exists:modules,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'due_date' => 'required|date',
            'file' => 'nullable|file|max:10240', // 10MB max
            'submission_type' => 'required|in:online,in_person'
        ]);

        $teacherId = $request->user()->teacher->id;

        $path = null;
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('homeworks_files', 'public');
        }

        $homework = Homework::create([
            'teacher_id' => $teacherId,
            'module_id' => $request->module_id,
            'title' => $request->title,
            'description' => $request->description,
            'file_path' => $path,
            'due_date' => \Carbon\Carbon::parse($request->due_date),
            'submission_type' => $request->submission_type,
        ]);

        return response()->json(['message' => 'Devoir créé avec succès', 'data' => $homework], 201);
    }

    public function show($id): JsonResponse
    {
        $homework = Homework::with(['module', 'submissions.student'])->findOrFail($id);

        $mappedSubmissions = $homework->submissions->map(function ($sub) {
            return [
                'id' => $sub->id,
                'student' => [
                    'id' => $sub->student->id,
                    'first_name' => $sub->student->first_name,
                    'last_name' => $sub->student->last_name,
                    'registration_number' => $sub->student->registration_number,
                ],
                'submission_text' => $sub->submission_text,
                'file_path' => $sub->file_path ? asset('storage/' . $sub->file_path) : null,
                'grade' => $sub->grade,
                'feedback' => $sub->feedback,
                'status' => $sub->status,
                'submitted_at' => $sub->submitted_at,
            ];
        });

        return response()->json([
            'homework' => [
                'id' => $homework->id,
                'title' => $homework->title,
                'description' => $homework->description,
                'due_date' => $homework->due_date,
                'submission_type' => $homework->submission_type,
                'file_path' => $homework->file_path ? asset('storage/' . $homework->file_path) : null,
                'module' => $homework->module->name,
            ],
            'submissions' => $mappedSubmissions
        ]);
    }

    public function gradeSubmission(Request $request, $homeworkId, $submissionId): JsonResponse
    {
        $request->validate([
            'grade' => 'required|numeric|min:0|max:20',
            'feedback' => 'nullable|string'
        ]);

        $submission = HomeworkSubmission::where('id', $submissionId)
            ->where('homework_id', $homeworkId)
            ->firstOrFail();

        $submission->update([
            'grade' => $request->grade,
            'feedback' => $request->feedback,
            'status' => 'graded'
        ]);

        return response()->json(['message' => 'Note attribuée avec succès']);
    }
}
