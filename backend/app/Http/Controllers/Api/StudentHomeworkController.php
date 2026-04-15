<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Homework;
use App\Models\HomeworkSubmission;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class StudentHomeworkController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $student = $request->user()->student;

        if (!$student) {
            return response()->json(['data' => []]);
        }

        // Fetch modules for the student based on specialty
        $moduleIds = \App\Models\Module::where('specialty_id', $student->specialty_id)
            ->where('semester', $student->current_semester)
            ->pluck('id')->toArray();

        $homeworks = Homework::whereIn('module_id', $moduleIds)
            ->with(['module', 'teacher.user'])
            ->orderBy('due_date', 'asc')
            ->get()
            ->map(function ($hw) use ($student) {
                $submission = HomeworkSubmission::where('homework_id', $hw->id)
                    ->where('student_id', $student->id)
                    ->first();

                // Ensure proper property access
                $teacherName = $hw->teacher && $hw->teacher->user
                    ? $hw->teacher->user->first_name . ' ' . $hw->teacher->user->last_name
                    : 'Unknown';

                return [
                    'id' => $hw->id,
                    'title' => $hw->title,
                    'description' => $hw->description,
                    'due_date' => $hw->due_date,
                    'submission_type' => $hw->submission_type,
                    'file_path' => $hw->file_path ? asset('storage/' . $hw->file_path) : null,
                    'module' => $hw->module ? $hw->module->name : 'N/A',
                    'teacher' => $teacherName,
                    'status' => $submission ? $submission->status : 'pending',
                    'submission' => $submission ? [
                        'id' => $submission->id,
                        'submission_text' => $submission->submission_text,
                        'file_path' => $submission->file_path ? asset('storage/' . $submission->file_path) : null,
                        'grade' => $submission->grade,
                        'feedback' => $submission->feedback,
                        'submitted_at' => $submission->submitted_at,
                    ] : null
                ];
            });

        return response()->json(['data' => $homeworks]);
    }

    public function submit(Request $request, $homeworkId): JsonResponse
    {
        $request->validate([
            'submission_text' => 'nullable|string',
            'file' => 'nullable|file|max:10240', // 10MB
        ]);

        if (!$request->has('submission_text') && !$request->hasFile('file')) {
            return response()->json(['message' => 'Veuillez fournir un texte ou un fichier.'], 400);
        }

        $studentId = $request->user()->student->id;
        $homework = Homework::findOrFail($homeworkId);

        // Check if past due
        // if (\Carbon\Carbon::now()->isAfter($homework->due_date)) {
        //    return response()->json(['message' => 'Date limite dépassée.'], 400);
        // }

        $submission = HomeworkSubmission::where('homework_id', $homeworkId)
            ->where('student_id', $studentId)
            ->first();

        $path = $submission ? $submission->file_path : null;
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('homework_submissions', 'public');
        }

        if ($submission) {
            $submission->update([
                'submission_text' => $request->submission_text ?? $submission->submission_text,
                'file_path' => $path,
                'status' => 'submitted',
                'submitted_at' => now(),
            ]);
        } else {
            $submission = HomeworkSubmission::create([
                'homework_id' => $homeworkId,
                'student_id' => $studentId,
                'submission_text' => $request->submission_text,
                'file_path' => $path,
                'status' => 'submitted',
                'submitted_at' => now(),
            ]);
        }

        return response()->json(['message' => 'Devoir soumis avec succès.', 'data' => $submission]);
    }
}
