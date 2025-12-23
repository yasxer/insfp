<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = ['administration_id', 'title', 'description', 'category', 'file_path', 'file_name', 'is_public', 'valid_until'];
    protected $casts = ['is_public' => 'boolean', 'valid_until' => 'date'];

    public function administration() { return $this->belongsTo(Administration::class); }

    public function scopePublic($query) { return $query->where('is_public', true); }
    public function scopeByCategory($query, $category) { return $query->where('category', $category); }
    public function scopeValid($query) { return $query->where(function($q) { $q->whereNull('valid_until')->orWhere('valid_until', '>=', now()); }); }
}
