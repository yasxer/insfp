<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = ['student_id', 'schedule_id', 'teacher_id', 'attendance_date', 'status', 'notes'];
    protected $casts = ['attendance_date' => 'date'];

    public function student() { return $this->belongsTo(Student::class); }
    public function schedule() { return $this->belongsTo(Schedule::class); }
    public function teacher() { return $this->belongsTo(Teacher::class); }

    public function scopeByStudent($query, $studentId) { return $query->where('student_id', $studentId); }
    public function scopeByDate($query, $date) { return $query->whereDate('attendance_date', $date); }
    public function scopeByStatus($query, $status) { return $query->where('status', $status); }
    public function scopePresent($query) { return $query->where('status', 'present'); }
    public function scopeAbsent($query) { return $query->where('status', 'absent'); }
}
