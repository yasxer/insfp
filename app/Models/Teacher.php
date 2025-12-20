<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $fillable = ['user_id', 'first_name', 'last_name', 'specialization'];
    protected $appends = ['full_name'];

    public function user() { return $this->belongsTo(User::class); }
    public function modules() { return $this->belongsToMany(Module::class, 'teacher_module')->withPivot('academic_year')->withTimestamps(); }
    public function schedules() { return $this->hasMany(Schedule::class); }
    public function attendances() { return $this->hasMany(Attendance::class); }
    public function lessons() { return $this->hasMany(Lesson::class); }
    public function encadrementGroups() { return $this->hasMany(EncadrementGroup::class); }

    public function scopeByAcademicYear($query, $year) {
        return $query->whereHas('modules', function($q) use ($year) {
            $q->where('teacher_module.academic_year', $year);
        });
    }
    public function scopeWithModulesCount($query) { return $query->withCount('modules'); }

    public function getFullNameAttribute() { return $this->first_name . ' ' . $this->last_name; }
}
