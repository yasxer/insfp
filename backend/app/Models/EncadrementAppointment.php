<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EncadrementAppointment extends Model
{
    protected $fillable = ['encadrement_group_id', 'appointment_date', 'agenda', 'notes', 'status', 'is_approved', 'approved_at'];
    protected $casts = ['appointment_date' => 'datetime', 'is_approved' => 'boolean', 'approved_at' => 'datetime'];

    public function encadrementGroup() { return $this->belongsTo(EncadrementGroup::class); }

    public function scopeScheduled($query) { return $query->where('status', 'scheduled'); }
    public function scopeApproved($query) { return $query->where('is_approved', true); }
    public function scopeUpcoming($query) { return $query->where('appointment_date', '>=', now())->orderBy('appointment_date'); }
}
