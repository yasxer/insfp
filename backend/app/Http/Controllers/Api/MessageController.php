<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MessageController extends Controller
{
    /**
     * Get all messages for authenticated student
     * GET /api/student/messages
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $student = $user->student;

        if (!$student) {
            return response()->json(['message' => 'Student profile not found'], 404);
        }

        // Get messages for this student
        // recipient_type 'all' = broadcast to everyone
        // recipient_type 'individual' = direct message to this user
        $messages = Message::where(function($query) use ($user) {
                $query->where('recipient_type', 'all')
                    ->orWhere(function($q) use ($user) {
                        $q->where('recipient_type', 'individual')
                          ->where('recipient_id', $user->id);
                    });
            })
            ->with('sender')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $messages->getCollection()->transform(function($message) use ($user) {
            // Check if user has read this message (works for both broadcast and individual)
            $isRead = $message->isReadBy($user->id);

            // For individual messages, also check old is_read field
            if ($message->recipient_type === 'individual' && $message->recipient_id == $user->id) {
                $isRead = $isRead || $message->is_read;
            }

            return [
                'id' => $message->id,
                'subject' => $message->subject,
                'body' => $message->body,
                'sender' => [
                    'name' => $message->sender->name ?? 'System',
                    'role' => $message->sender->role ?? 'system',
                ],
                'recipient_type' => $message->recipient_type,
                'is_read' => $isRead,
                'created_at' => $message->created_at->format('Y-m-d H:i'),
            ];
        });

        return response()->json($messages);
    }

    /**
     * Get single message details
     * GET /api/student/messages/{id}
     */
    public function show(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        $student = $user->student;

        $message = Message::where(function($query) use ($user) {
                $query->where('recipient_type', 'all')
                    ->orWhere(function($q) use ($user) {
                        $q->where('recipient_type', 'individual')
                          ->where('recipient_id', $user->id);
                    });
            })
            ->with('sender')
            ->findOrFail($id);

        // Mark as read for this user (works for both broadcast and individual messages)
        $message->markAsReadBy($user->id);

        // For individual messages, also update old is_read field
        if ($message->recipient_type === 'individual' && $message->recipient_id == $user->id && !$message->is_read) {
            $message->update(['is_read' => true, 'read_at' => now()]);
        }

        return response()->json([
            'id' => $message->id,
            'subject' => $message->subject,
            'body' => $message->body,
            'sender' => [
                'name' => $message->sender->name ?? 'System',
                'role' => $message->sender->role ?? 'system',
            ],
            'recipient_type' => $message->recipient_type,
            'created_at' => $message->created_at->format('Y-m-d H:i'),
        ]);
    }

    /**
     * Get unread messages count
     * GET /api/student/messages/unread/count
     */
    public function unreadCount(Request $request): JsonResponse
    {
        $user = $request->user();
        $student = $user->student;

        // Get all messages visible to this user
        $allMessages = Message::where(function($query) use ($user) {
                $query->where('recipient_type', 'all')
                    ->orWhere(function($q) use ($user) {
                        $q->where('recipient_type', 'individual')
                          ->where('recipient_id', $user->id);
                    });
            })->get();

        // Count messages not read by this user
        $unreadCount = $allMessages->filter(function($message) use ($user) {
            return !$message->isReadBy($user->id);
        })->count();

        return response()->json(['count' => $unreadCount]);
    }
}
