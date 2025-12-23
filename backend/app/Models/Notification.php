<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = ['user_id', 'title', 'message', 'type', 'data', 'is_read', 'read_at'];
    protected $casts = ['data' => 'array', 'is_read' => 'boolean', 'read_at' => 'datetime'];

    public function user() { return $this->belongsTo(User::class); }

    public function scopeUnread($query) { return $query->where('is_read', false); }
    public function scopeByUser($query, $userId) { return $query->where('user_id', $userId); }
    public function scopeByType($query, $type) { return $query->where('type', $type); }
    public function scopeRecent($query) { return $query->orderBy('created_at', 'desc'); }
}
