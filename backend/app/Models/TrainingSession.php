<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TrainingSession extends Model
{
    protected $table = 'sessions';

    protected $fillable = [
        'name',
        'month',
        'year',
        'start_date',
        'end_date',
        'is_active'
    ];

    protected $casts = [
        'month' => 'integer',
        'year' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean'
    ];

    protected $appends = ['status', 'formatted_name'];

    // Boot method to auto-calculate dates
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($session) {
            $session->calculateDates();
            $session->generateName();
        });

        static::updating(function ($session) {
            if ($session->isDirty(['month', 'year'])) {
                $session->calculateDates();
                $session->generateName();
            }
        });
    }

    // Calculate start and end dates
    public function calculateDates()
    {
        $this->start_date = Carbon::create($this->year, $this->month, 1);
        $this->end_date = $this->start_date->copy()->addMonths(30); // 2.5 years
    }

    // Generate session name
    public function generateName()
    {
        $months = [
            1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
            5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
            9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
        ];
        $this->name = "Session {$months[$this->month]} {$this->year}";
    }

    // Relationships
    public function sessionSpecialties()
    {
        return $this->hasMany(SessionSpecialty::class, 'session_id');
    }

    public function specialties()
    {
        return $this->belongsToMany(Specialty::class, 'session_specialties', 'session_id', 'specialty_id')
                    ->withPivot('study_type')
                    ->withTimestamps();
    }

    public function students()
    {
        return $this->hasManyThrough(
            Student::class,
            SessionSpecialty::class,
            'session_id',
            'session_specialty_id',
            'id',
            'id'
        );
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeCurrent($query)
    {
        return $query->where('end_date', '>=', now());
    }

    public function scopeArchived($query)
    {
        return $query->where('end_date', '<', now());
    }

    // Accessors
    public function getStatusAttribute()
    {
        if ($this->end_date < now()) {
            return 'terminée';
        }
        if ($this->start_date > now()) {
            return 'à venir';
        }
        return 'en cours';
    }

    public function getFormattedNameAttribute()
    {
        return $this->name;
    }

    // Get specialties grouped by study type
    public function getSpecialtiesByType()
    {
        return $this->sessionSpecialties()
            ->with('specialty')
            ->get()
            ->groupBy('study_type');
    }
}
