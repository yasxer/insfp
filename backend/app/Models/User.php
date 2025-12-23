<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = ['email', 'phone', 'password', 'role', 'is_approved'];
    protected $hidden = ['password', 'remember_token'];
    protected $casts = ['is_approved' => 'boolean', 'email_verified_at' => 'datetime'];

    public function student() { return $this->hasOne(Student::class); }
    public function teacher() { return $this->hasOne(Teacher::class); }
    public function administration() { return $this->hasOne(Administration::class); }
    public function sentMessages() { return $this->hasMany(Message::class, 'sender_id'); }
    public function receivedMessages() { return $this->hasMany(Message::class, 'recipient_id'); }
    public function notifications() { return $this->hasMany(Notification::class); }

    public function scopeApproved($query) { return $query->where('is_approved', true); }
    public function scopeByRole($query, $role) { return $query->where('role', $role); }
}
