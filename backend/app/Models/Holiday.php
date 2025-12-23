<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    protected $fillable = ['name', 'start_date', 'end_date', 'description'];
    protected $casts = ['start_date' => 'date', 'end_date' => 'date'];

    public function scopeUpcoming($query) { return $query->where('start_date', '>=', now())->orderBy('start_date'); }
    public function scopeCurrent($query) { return $query->where('start_date', '<=', now())->where('end_date', '>=', now()); }
}
