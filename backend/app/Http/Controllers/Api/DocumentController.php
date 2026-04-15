<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    /**
     * Get all documents for students or teachers
     * GET /api/{role}/documents
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = Document::query();

        if ($user->role === 'teacher') {
            $query->where(function ($q) {
                $q->where('is_public', true)
                  ->orWhere('target_type', 'all_teachers');
            });
        } elseif ($user->role === 'student' && $user->student) {
            $session_id = $user->student->session_id;
            $query->where(function ($q) use ($session_id) {
                $q->where('is_public', true)
                  ->orWhere('target_type', 'all_students')
                  ->orWhere(function ($sub) use ($session_id) {
                      $sub->where('target_type', 'session_students')
                          ->where('session_id', $session_id);
                  });
            });
        } else {
            $query->where('is_public', true);
        }

        $documents = $query->orderBy('created_at', 'desc')->paginate(20);

        $documents->getCollection()->transform(function($document) use ($user) {
            try {
                $fileSize = Storage::disk('public')->size($document->file_path);
            } catch (\Exception $e) {
                $fileSize = 0;
            }

            return [
                'id' => $document->id,
                'title' => $document->title,
                'description' => $document->description,
                'category' => $document->category,
                'file_path' => $document->file_path,
                'file_name' => $document->file_name,
                'original_filename' => $document->file_name,
                'file_size' => $fileSize,
                'is_new' => $document->created_at >= now()->subDays(7),
                'created_at' => $document->created_at->toIso8601String(),
            ];
        });

        return response()->json($documents);
    }

    /**
     * Download document
     * GET /api/{role}/documents/{id}/download
     */
    public function download(Request $request, $id): mixed
    {
        $document = Document::findOrFail($id);

        if (!Storage::disk('public')->exists($document->file_path)) {
            return response()->json(['message' => 'File not found'], 404);
        }

        $fullPath = Storage::disk('public')->path($document->file_path);
        return response()->download($fullPath, $document->file_name);
    }

    /**
     * Get new documents count
     * GET /api/{role}/documents/new/count
     */
    public function newCount(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = Document::where('created_at', '>=', now()->subDays(7));

        if ($user->role === 'teacher') {
            $query->where(function ($q) {
                $q->where('is_public', true)
                  ->orWhere('target_type', 'all_teachers');
            });
        } elseif ($user->role === 'student' && $user->student) {
            $session_id = $user->student->session_id;
            $query->where(function ($q) use ($session_id) {
                $q->where('is_public', true)
                  ->orWhere('target_type', 'all_students')
                  ->orWhere(function ($sub) use ($session_id) {
                      $sub->where('target_type', 'session_students')
                          ->where('session_id', $session_id);
                  });
            });
        } else {
            $query->where('is_public', true);
        }

        $newCount = $query->count();

        // Return both for compatibility just in case
        return response()->json(['count' => $newCount, 'new_count' => $newCount]);
    }
}
