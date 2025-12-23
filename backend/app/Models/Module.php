<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $fillable = ['specialty_id', 'name', 'code', 'description', 'semester', 'coefficient', 'hours_per_week'];
    protected $casts = ['semester' => 'integer', 'coefficient' => 'decimal:1', 'hours_per_week' => 'integer'];

    public function specialty() { return $this->belongsTo(Specialty::class); }
    public function teachers() { return $this->belongsToMany(Teacher::class, 'teacher_module')->withPivot('academic_year')->withTimestamps(); }
    public function schedules() { return $this->hasMany(Schedule::class); }
    public function lessons() { return $this->hasMany(Lesson::class); }
    public function exams() { return $this->hasMany(Exam::class); }
    public function grades() { return $this->hasMany(Grade::class); }

    public function scopeBySemester($query, $semester) { return $query->where('semester', $semester); }
    public function scopeBySpecialty($query, $specialtyId) { return $query->where('specialty_id', $specialtyId); }
}
