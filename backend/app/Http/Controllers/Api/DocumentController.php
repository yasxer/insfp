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
     * Get all documents for students
     * GET /api/student/documents
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $student = $user->student;

        // Get all public documents
        $documents = Document::where('is_public', true)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $documents->getCollection()->transform(function($document) use ($user) {
            return [
                'id' => $document->id,
                'title' => $document->title,
                'description' => $document->description,
                'category' => $document->category,
                'file_path' => $document->file_path,
                'file_name' => $document->file_name,
                'is_viewed' => false, // Simplified
                'created_at' => $document->created_at->format('Y-m-d'),
            ];
        });

        return response()->json($documents);
    }

    /**
     * Download document
     * GET /api/student/documents/{id}/download
     */
    public function download(Request $request, $id): mixed
    {
        $user = $request->user();
        $document = Document::findOrFail($id);

        if (!Storage::exists($document->file_path)) {
            return response()->json(['message' => 'File not found'], 404);
        }

        return Storage::download($document->file_path, $document->file_name);
    }

    /**
     * Get new documents count
     * GET /api/student/documents/new/count
     */
    public function newCount(Request $request): JsonResponse
    {
        $user = $request->user();
        $student = $user->student;

        // Count documents from last 7 days
        $newCount = Document::where('is_public', true)
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        return response()->json(['new_count' => $newCount]);
    }
}
