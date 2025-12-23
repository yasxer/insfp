<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Specialty extends Model
{
    protected $fillable = ['name', 'code', 'description', 'duration_semesters', 'is_active', 'cover_image', 'brochure_path', 'brochure_name'];
    protected $casts = ['duration_semesters' => 'integer', 'is_active' => 'boolean'];

    public function students() { return $this->hasMany(Student::class); }
    public function modules() { return $this->hasMany(Module::class); }
    public function schedules() { return $this->hasMany(Schedule::class); }
    public function exams() { return $this->hasMany(Exam::class); }
    public function encadrementGroups() { return $this->hasMany(EncadrementGroup::class); }

    public function scopeActive($query) { return $query->where('is_active', true); }
    public function scopeWithStudentsCount($query) { return $query->withCount('students'); }
}
