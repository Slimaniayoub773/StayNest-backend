<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'image',
        'category',
        'author'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Add slug functionality for URLs
    public function getSlugAttribute()
    {
        return \Str::slug($this->title);
    }

    // Scope for published blogs (if you add published_at later)
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    // Get excerpt for preview
    public function getExcerptAttribute($length = 150)
    {
        return \Str::limit(strip_tags($this->content), $length);
    }
}