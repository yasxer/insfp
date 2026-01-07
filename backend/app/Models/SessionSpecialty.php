<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SessionSpecialty extends Model
{
    protected $fillable = [
        'session_id',
        'specialty_id',
        'study_type'
    ];

    protected $casts = [
        'session_id' => 'integer',
        'specialty_id' => 'integer'
    ];

    // Study type labels
    public const STUDY_TYPES = [
        'presential' => 'Présentiel',
        'apprentissage' => 'Apprentissage',
        'cours_soir' => 'Cours du soir'
    ];

    protected $appends = ['study_type_label'];

    // Relationships
    public function session()
    {
        return $this->belongsTo(TrainingSession::class, 'session_id');
    }

    public function specialty()
    {
        return $this->belongsTo(Specialty::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'session_specialty_id');
    }

    // Accessors
    public function getStudyTypeLabelAttribute()
    {
        return self::STUDY_TYPES[$this->study_type] ?? $this->study_type;
    }

    // Scopes
    public function scopeByStudyType($query, $studyType)
    {
        return $query->where('study_type', $studyType);
    }

    public function scopeBySession($query, $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }
}
