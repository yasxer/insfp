<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    protected $fillable = ['module_id', 'teacher_id', 'title', 'description', 'file_path', 'file_name', 'file_type', 'file_size'];
    protected $casts = ['file_size' => 'integer'];

    public function module() { return $this->belongsTo(Module::class); }
    public function teacher() { return $this->belongsTo(Teacher::class); }

    public function scopeByModule($query, $moduleId) { return $query->where('module_id', $moduleId); }
    public function scopeByTeacher($query, $teacherId) { return $query->where('teacher_id', $teacherId); }
    public function scopeRecent($query) { return $query->orderBy('created_at', 'desc'); }
}
