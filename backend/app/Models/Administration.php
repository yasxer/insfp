<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Administration extends Model
{
    protected $fillable = ['user_id', 'first_name', 'last_name', 'position'];
    protected $appends = ['full_name'];

    public function user() { return $this->belongsTo(User::class); }
    public function documents() { return $this->hasMany(Document::class); }

    public function scopeByPosition($query, $position) { return $query->where('position', 'like', "%{$position}%"); }

    public function getFullNameAttribute() { return $this->first_name . ' ' . $this->last_name; }
}
