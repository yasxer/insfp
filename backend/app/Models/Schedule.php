<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = ['module_id', 'teacher_id', 'specialty_id', 'day', 'start_time', 'end_time', 'classroom', 'semester', 'academic_year'];
    protected $casts = [
        'semester' => 'integer',
    ];

    public function module() { return $this->belongsTo(Module::class); }
    public function teacher() { return $this->belongsTo(Teacher::class); }
    public function specialty() { return $this->belongsTo(Specialty::class); }
    public function attendances() { return $this->hasMany(Attendance::class); }

    public function scopeByDay($query, $day) { return $query->where('day', $day); }
    public function scopeBySemester($query, $semester) { return $query->where('semester', $semester); }
    public function scopeByAcademicYear($query, $year) { return $query->where('academic_year', $year); }
    public function scopeBySpecialty($query, $specialtyId) { return $query->where('specialty_id', $specialtyId); }
}
