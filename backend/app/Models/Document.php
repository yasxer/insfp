<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'administration_id',
        'title',
        'description',
        'category',
        'file_path',
        'file_name',
        'is_public',
        'valid_until',
        'target_type',
        'session_id',
        'specialty_ids'
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'valid_until' => 'date',
        'specialty_ids' => 'array'
    ];

    // Target type constants
    const TARGET_ALL_TEACHERS = 'all_teachers';
    const TARGET_ALL_STUDENTS = 'all_students';
    const TARGET_SESSION_STUDENTS = 'session_students';
    const TARGET_SPECIALTY_STUDENTS = 'specialty_students';

    // Relationships
    public function administration()
    {
        return $this->belongsTo(Administration::class);
    }

    public function session()
    {
        return $this->belongsTo(TrainingSession::class, 'session_id');
    }

    // Scopes
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeValid($query)
    {
        return $query->where(function($q) {
            $q->whereNull('valid_until')->orWhere('valid_until', '>=', now());
        });
    }

    public function scopeForTeachers($query)
    {
        return $query->where('target_type', self::TARGET_ALL_TEACHERS);
    }

    public function scopeForStudent($query, $student)
    {
        $sessionId = $student->sessionSpecialty?->session_id;
        $specialtyId = $student->sessionSpecialty?->specialty_id;

        return $query->where(function($q) use ($sessionId, $specialtyId) {
            // All students
            $q->where('target_type', self::TARGET_ALL_STUDENTS);

            // Or students from specific session
            if ($sessionId) {
                $q->orWhere(function($q2) use ($sessionId) {
                    $q2->where('target_type', self::TARGET_SESSION_STUDENTS)
                       ->where('session_id', $sessionId);
                });
            }

            // Or students from specific specialties
            if ($sessionId && $specialtyId) {
                $q->orWhere(function($q2) use ($sessionId, $specialtyId) {
                    $q2->where('target_type', self::TARGET_SPECIALTY_STUDENTS)
                       ->where('session_id', $sessionId)
                       ->whereJsonContains('specialty_ids', (int)$specialtyId);
                });
            }
        });
    }

    // Helper to get target description
    public function getTargetDescriptionAttribute()
    {
        switch ($this->target_type) {
            case self::TARGET_ALL_TEACHERS:
                return 'All Teachers';
            case self::TARGET_ALL_STUDENTS:
                return 'All Students';
            case self::TARGET_SESSION_STUDENTS:
                $sessionName = $this->session?->name ?? 'Unknown Session';
                return "Session: {$sessionName} (All Specialties)";
            case self::TARGET_SPECIALTY_STUDENTS:
                $sessionName = $this->session?->name ?? 'Unknown Session';
                $count = is_array($this->specialty_ids) ? count($this->specialty_ids) : 0;
                return "Session: {$sessionName} ({$count} Specialties)";
            default:
                return 'Unknown';
        }
    }
}
