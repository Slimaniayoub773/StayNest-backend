<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Amenity extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'icon_class'
    ];

    // Add this accessor to always get an icon
    public function getIconAttribute()
    {
        return $this->icon_class ?: 'Default';
    }
    public function rooms()
    {
        return $this->belongsToMany(Room::class, 'room_amenities', 'amenity_id', 'room_id');
    }
}

