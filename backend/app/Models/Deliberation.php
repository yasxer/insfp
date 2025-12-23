<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deliberation extends Model
{
    protected $fillable = ['student_id', 'semester', 'academic_year', 'average', 'result', 'observations', 'deliberation_date'];
    protected $casts = ['semester' => 'integer', 'average' => 'decimal:2', 'deliberation_date' => 'date'];

    public function student() { return $this->belongsTo(Student::class); }

    public function scopeByStudent($query, $studentId) { return $query->where('student_id', $studentId); }
    public function scopeBySemester($query, $semester) { return $query->where('semester', $semester); }
    public function scopeByAcademicYear($query, $year) { return $query->where('academic_year', $year); }
    public function scopePassed($query) { return $query->where('result', 'passed'); }
    public function scopeFailed($query) { return $query->where('result', 'failed'); }
}
