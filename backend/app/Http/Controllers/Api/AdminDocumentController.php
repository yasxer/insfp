<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\TrainingSession;
use App\Models\SessionSpecialty;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AdminDocumentController extends Controller
{
    /**
     * Get all documents for admin
     * GET /api/admin/documents
     */
    public function index(Request $request): JsonResponse
    {
        $query = Document::with(['session', 'administration'])
            ->orderBy('created_at', 'desc');

        // Filter by target type
        if ($request->has('target_type') && $request->target_type !== 'all') {
            $query->where('target_type', $request->target_type);
        }

        $documents = $query->paginate(20);

        $documents->getCollection()->transform(function($document) {
            return [
                'id' => $document->id,
                'title' => $document->title,
                'description' => $document->description,
                'file_name' => $document->file_name,
                'target_type' => $document->target_type,
                'target_description' => $document->target_description,
                'session_id' => $document->session_id,
                'session_name' => $document->session?->name,
                'specialty_ids' => $document->specialty_ids,
                'created_at' => $document->created_at->format('Y-m-d'),
            ];
        });

        return response()->json($documents);
    }

    public function store(Request $request): JsonResponse
    {
        // Parse specialty_ids if it's a JSON string
        $specialtyIds = [];
        if ($request->has('specialty_ids')) {
            $val = $request->get('specialty_ids');
            if (is_string($val)) {
                $specialtyIds = json_decode($val, true) ?? [];
            } else {
                $specialtyIds = is_array($val) ? $val : [];
            }
        }

        // Replace specialty_ids in request for validation
        if (!empty($specialtyIds)) {
            $request->merge(['specialty_ids' => $specialtyIds]);
        }

        $validator = Validator::make($request->all(), [
            'file' => 'required|file|max:51200', // 50MB max
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_type' => 'required|in:all_teachers,all_students,session_students,specialty_students',
            'session_id' => 'required_if:target_type,session_students,specialty_students|nullable|exists:sessions,id',
            'specialty_ids' => 'required_if:target_type,specialty_students|nullable|array',
            'specialty_ids.*' => 'integer|exists:specialties,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        $administration = $user->administration;

        if (!$administration) {
            return response()->json(['message' => 'Administration not found'], 403);
        }

        // Store the file
        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $path = $file->store('documents', 'public');

        // Prepare specialty_ids as integer array if needed
        $finalSpecialtyIds = null;
        if ($request->target_type === 'specialty_students' && !empty($specialtyIds)) {
            $finalSpecialtyIds = array_map('intval', $specialtyIds);
        }

        // Create document record
        $document = Document::create([
            'administration_id' => $administration->id,
            'title' => $request->title,
            'description' => $request->description,
            'file_path' => $path,
            'file_name' => $originalName,
            'is_public' => true,
            'target_type' => $request->target_type,
            'session_id' => in_array($request->target_type, ['session_students', 'specialty_students'])
                ? $request->session_id
                : null,
            'specialty_ids' => $finalSpecialtyIds,
        ]);

        return response()->json([
            'message' => 'Document uploaded successfully',
            'document' => [
                'id' => $document->id,
                'title' => $document->title,
                'description' => $document->description,
                'file_name' => $document->file_name,
                'target_type' => $document->target_type,
                'target_description' => $document->target_description,
                'created_at' => $document->created_at->format('Y-m-d'),
            ]
        ], 201);
    }

    /**
     * Delete a document
     * DELETE /api/admin/documents/{id}
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        $document = Document::findOrFail($id);

        // Delete file from storage
        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return response()->json(['message' => 'Document deleted successfully']);
    }

    /**
     * Get sessions for dropdown
     * GET /api/admin/documents/sessions
     */
    public function getSessions(): JsonResponse
    {
        $sessions = TrainingSession::where('is_active', true)
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get(['id', 'name']);

        return response()->json($sessions);
    }

    /**
     * Get specialties for a session
     * GET /api/admin/documents/sessions/{sessionId}/specialties
     */
    public function getSessionSpecialties($sessionId): JsonResponse
    {
        $sessionSpecialties = SessionSpecialty::with('specialty:id,name,code')
            ->where('session_id', $sessionId)
            ->get()
            ->map(function($ss) {
                return [
                    'id' => $ss->specialty_id,
                    'name' => $ss->specialty->name,
                    'code' => $ss->specialty->code,
                    'study_type' => $ss->study_type_label,
                ];
            });

        return response()->json($sessionSpecialties);
    }

    /**
     * Download document (admin can download any document)
     * GET /api/admin/documents/{id}/download
     */
    public function download($id): mixed
    {
        try {
            $document = Document::findOrFail($id);

            if (!Storage::disk('public')->exists($document->file_path)) {
                return response()->json(['message' => 'File not found'], 404);
            }

            $fullPath = Storage::disk('public')->path($document->file_path);
            return response()->download($fullPath, $document->file_name);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error downloading file',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
