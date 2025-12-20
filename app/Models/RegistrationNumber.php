<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistrationNumber extends Model
{
    protected $fillable = [
        'number',
        'is_used',
        'specialty_id',
        'academic_year',
        'used_at',
    ];

    protected $casts = [
        'is_used' => 'boolean',
        'used_at' => 'datetime',
    ];

    public function specialty()
    {
        return $this->belongsTo(Specialty::class);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_used', false);
    }
}
