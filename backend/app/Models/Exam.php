<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $fillable = ['module_id', 'specialty_id', 'exam_type', 'exam_date', 'duration_minutes', 'classroom', 'semester', 'academic_year'];
    protected $casts = ['exam_date' => 'datetime', 'duration_minutes' => 'integer', 'semester' => 'integer'];

    public function module() { return $this->belongsTo(Module::class); }
    public function specialty() { return $this->belongsTo(Specialty::class); }
    public function grades() { return $this->hasMany(Grade::class); }

    public function scopeByType($query, $type) { return $query->where('exam_type', $type); }
    public function scopeBySemester($query, $semester) { return $query->where('semester', $semester); }
    public function scopeByAcademicYear($query, $year) { return $query->where('academic_year', $year); }
    public function scopeUpcoming($query) { return $query->where('exam_date', '>=', now())->orderBy('exam_date'); }
}
