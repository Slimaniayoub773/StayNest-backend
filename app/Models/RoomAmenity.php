<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomAmenity extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'room_id',
        'amenity_id',
    ];

    /**
     * Get the room that owns the amenity.
     */
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Get the amenity that belongs to the room.
     */
    public function amenity()
    {
        return $this->belongsTo(Amenity::class);
    }
}