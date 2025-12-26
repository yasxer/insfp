<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = ['sender_id', 'recipient_id', 'subject', 'body', 'recipient_type', 'is_read', 'read_at'];
    protected $casts = ['is_read' => 'boolean', 'read_at' => 'datetime'];

    public function sender() { return $this->belongsTo(User::class, 'sender_id'); }
    public function recipient() { return $this->belongsTo(User::class, 'recipient_id'); }

    // Many-to-many: Users who read this message
    public function readers() {
        return $this->belongsToMany(User::class, 'message_reads')
                    ->withTimestamps()
                    ->withPivot('read_at');
    }

    // Check if a specific user has read this message
    public function isReadBy($userId) {
        return $this->readers()->where('user_id', $userId)->exists();
    }

    // Mark message as read by a user
    public function markAsReadBy($userId) {
        if (!$this->isReadBy($userId)) {
            $this->readers()->attach($userId, ['read_at' => now()]);
        }
    }

    public function scopeUnread($query) { return $query->where('is_read', false); }
    public function scopeByRecipient($query, $userId) { return $query->where('recipient_id', $userId); }
    public function scopeBySender($query, $userId) { return $query->where('sender_id', $userId); }
    public function scopeRecent($query) { return $query->orderBy('created_at', 'desc'); }
}
