<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'user_id',
        'specialty_id',
        'session_specialty_id',
        'registration_number',
        'first_name',
        'last_name',
        'date_of_birth',  // Added
        'address',        // Added
        'study_mode',
        'current_semester',
        'group',
        'years_enrolled',
        'is_graduated',
        'graduation_year',
        'graduation_semester',
        'final_gpa'
    ];
    protected $casts = [
        'current_semester' => 'integer',
        'years_enrolled' => 'integer',
        'is_graduated' => 'boolean',
        'date_of_birth' => 'date',
        'graduation_year' => 'integer',
        'graduation_semester' => 'integer',
        'final_gpa' => 'decimal:2'
    ];
    protected $appends = ['full_name', 'promotion', 'study_type'];

    public function user() { return $this->belongsTo(User::class); }
    public function specialty() { return $this->belongsTo(Specialty::class); }
    public function sessionSpecialty() { return $this->belongsTo(SessionSpecialty::class); }
    public function session() { return $this->hasOneThrough(TrainingSession::class, SessionSpecialty::class, 'id', 'id', 'session_specialty_id', 'session_id'); }
    public function attendances() { return $this->hasMany(Attendance::class); }
    public function grades() { return $this->hasMany(Grade::class); }
    public function deliberations() { return $this->hasMany(Deliberation::class); }
    public function encadrementGroups() { return $this->belongsToMany(EncadrementGroup::class, 'encadrement_students'); }

    // Get promotion/session info
    public function getPromotionAttribute()
    {
        if ($this->sessionSpecialty && $this->sessionSpecialty->session) {
            return $this->sessionSpecialty->session->name;
        }
        return null;
    }

    // Get study type from session specialty
    public function getStudyTypeAttribute()
    {
        if ($this->sessionSpecialty) {
            return $this->sessionSpecialty->study_type_label;
        }
        return null;
    }

    public function scopeActive($query) { return $query->where('is_graduated', false); }
    public function scopeGraduated($query) { return $query->where('is_graduated', true); }
    public function scopeBySemester($query, $semester) { return $query->where('current_semester', $semester); }
    public function scopeBySpecialty($query, $specialtyId) { return $query->where('specialty_id', $specialtyId); }

    public function getFullNameAttribute() { return $this->first_name . ' ' . $this->last_name; }
}
