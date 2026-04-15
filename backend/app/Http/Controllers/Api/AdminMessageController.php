<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class AdminMessageController extends Controller
{
    /**
     * Send message to students or teachers (individual or broadcast)
     * POST /api/admin/messages/send
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $targetRole = $request->input('target_role', 'student');

        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'recipient_type' => 'required|in:individual,all',
            'recipient_ids' => 'array',
            'recipient_ids.*' => $targetRole === 'teacher' ? 'exists:teachers,id' : 'exists:students,id',
            'target_role' => 'nullable|string|in:student,teacher',
            'is_graduated' => 'nullable|boolean',
        ]);

        $sender = $request->user();
        $recipientType = $validated['recipient_type'];
        $subject = $validated['subject'];
        $body = $validated['message'];
        $targetRole = $validated['target_role'] ?? 'student';

        DB::beginTransaction();

        try {
            $recipientCount = 0;

            if ($recipientType === 'individual') {
                // Individual: Create one message per user
                if (empty($validated['recipient_ids'])) {
                    return response()->json([
                        'message' => 'No recipients specified'
                    ], 400);
                }

                if ($targetRole === 'teacher') {
                    $users = Teacher::with('user')
                        ->whereIn('id', $validated['recipient_ids'])
                        ->get();
                } else {
                    $users = Student::with('user')
                        ->whereIn('id', $validated['recipient_ids'])
                        ->get();
                }

                foreach ($users as $targetUser) {
                    if ($targetUser->user) {
                        Message::create([
                            'sender_id' => $sender->id,
                            'recipient_id' => $targetUser->user->id,
                            'recipient_type' => 'individual',
                            'subject' => $subject,
                            'body' => $body,
                            'is_read' => false,
                        ]);
                        $recipientCount++;
                    }
                }

            } else {
                // Broadcast: Create ONE message only
                if ($targetRole === 'teacher') {
                    $query = Teacher::with('user');
                    if (!empty($validated['recipient_ids'])) {
                        $query->whereIn('id', $validated['recipient_ids']);
                    }
                    $recipientCount = $query->whereHas('user')->count();
                    $actualRecipientType = 'teachers';
                } else {
                    $query = Student::with('user');
                    if (!empty($validated['recipient_ids'])) {
                        $query->whereIn('id', $validated['recipient_ids']);
                    } elseif (isset($validated['is_graduated'])) {
                        $query->where('is_graduated', $validated['is_graduated']);
                    }
                    $recipientCount = $query->whereHas('user')->count();
                    $actualRecipientType = 'students';
                }

                // Create single broadcast message
                Message::create([
                    'sender_id' => $sender->id,
                    'recipient_id' => null,
                    'recipient_type' => $actualRecipientType, // Use specific type
                    'subject' => $subject,
                    'body' => $body,
                    'is_read' => false,
                    'recipient_count' => $recipientCount,
                    'recipient_filter' => json_encode([
                        'recipient_ids' => $validated['recipient_ids'] ?? [],
                        'is_graduated' => $validated['is_graduated'] ?? null,
                    ]),
                ]);
            }

            DB::commit();

            $noun = $targetRole === 'teacher' ? 'teacher(s)' : 'student(s)';
            return response()->json([
                'message' => "Message sent successfully to {$recipientCount} {$noun}",
                'recipient_count' => $recipientCount,
                'sent_at' => now()->toDateTimeString(),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to send message',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get message history
     * GET /api/admin/messages
     */
    public function index(Request $request): JsonResponse
    {
        $sender = $request->user();

        $messages = Message::where('sender_id', $sender->id)
            ->with(['recipient'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($messages);
    }

    /**
     * Get sent message statistics
     * GET /api/admin/messages/stats
     */
    public function stats(Request $request): JsonResponse
    {
        $sender = $request->user();

        $stats = [
            'total_sent' => Message::where('sender_id', $sender->id)->count(),
            'total_read' => Message::where('sender_id', $sender->id)->where('is_read', true)->count(),
            'total_unread' => Message::where('sender_id', $sender->id)->where('is_read', false)->count(),
            'individual_messages' => Message::where('sender_id', $sender->id)->where('recipient_type', 'individual')->count(),
            'broadcast_messages' => Message::where('sender_id', $sender->id)->whereIn('recipient_type', ['all', 'students', 'teachers'])->count(),
        ];

        return response()->json($stats);
    }
}

