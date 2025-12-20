<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EncadrementGroup extends Model
{
    protected $fillable = ['teacher_id', 'specialty_id', 'project_title', 'project_description', 'academic_year', 'status'];

    public function teacher() { return $this->belongsTo(Teacher::class); }
    public function specialty() { return $this->belongsTo(Specialty::class); }
    public function students() { return $this->belongsToMany(Student::class, 'encadrement_students'); }
    public function appointments() { return $this->hasMany(EncadrementAppointment::class); }

    public function scopeActive($query) { return $query->where('status', 'active'); }
    public function scopeByTeacher($query, $teacherId) { return $query->where('teacher_id', $teacherId); }
    public function scopeByAcademicYear($query, $year) { return $query->where('academic_year', $year); }
}
