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
        'is_active',
        'status'
    ];

    const ALLOWED_MONTHS = [2, 9]; // Février et Septembre uniquement

    protected $casts = [
        'month' => 'integer',
        'year' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean'
    ];

    protected $appends = ['status_label', 'formatted_name', 'is_activatable'];

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

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCurrent($query)
    {
        return $query->where('end_date', '>=', now());
    }

    public function scopeArchived($query)
    {
        return $query->where('end_date', '<', now());
    }

    // Whether this pending session's month has arrived and it can be activated
    public function isActivatable(): bool
    {
        return $this->status === 'pending' && $this->start_date <= now();
    }

    /**
     * The next Février/Septembre intake to open, computed from today's date.
     * Once a month has passed, the target jumps to the following one — never
     * a slot in the past. If that slot was already created in advance (e.g.
     * an admin opened next year's session early), keep advancing to the next
     * free Février/Septembre slot.
     */
    public static function nextSlot(): array
    {
        $now = Carbon::now();

        if ($now->month > 9) {
            $month = 2;
            $year = $now->year + 1;
        } elseif ($now->month > 2) {
            $month = 9;
            $year = $now->year;
        } else {
            $month = 2;
            $year = $now->year;
        }

        while (self::where('month', $month)->where('year', $year)->exists()) {
            if ($month === 2) {
                $month = 9;
            } else {
                $month = 2;
                $year++;
            }
        }

        return ['month' => $month, 'year' => $year];
    }

    // Accessors
    public function getIsActivatableAttribute(): bool
    {
        return $this->isActivatable();
    }

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'active' => 'en cours',
            'archived' => 'terminée',
            default => 'en attente',
        };
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
