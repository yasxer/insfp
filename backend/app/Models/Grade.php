<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    protected $fillable = ['student_id', 'module_id', 'exam_id', 'grade', 'semester', 'academic_year'];
    protected $casts = ['grade' => 'decimal:2', 'semester' => 'integer'];

    public function student() { return $this->belongsTo(Student::class); }
    public function module() { return $this->belongsTo(Module::class); }
    public function exam() { return $this->belongsTo(Exam::class); }

    public function scopeByStudent($query, $studentId) { return $query->where('student_id', $studentId); }
    public function scopeBySemester($query, $semester) { return $query->where('semester', $semester); }
    public function scopeByAcademicYear($query, $year) { return $query->where('academic_year', $year); }
    public function scopePassing($query) { return $query->where('grade', '>=', 10); }
}
