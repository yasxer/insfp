<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdvancementReview extends Model
{
    protected $fillable = ['student_id', 'semester', 'academic_year', 'average', 'status', 'resolved_at'];
    protected $casts = ['semester' => 'integer', 'average' => 'decimal:2', 'resolved_at' => 'datetime'];

    public function student() { return $this->belongsTo(Student::class); }

    public function scopePending($query) { return $query->where('status', 'pending'); }
}
