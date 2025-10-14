<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomImage extends Model
{
    // Remove the $primaryKey declaration to use default 'id'
    protected $fillable = [
        'room_id',
        'image_url',
        'is_primary'
    ];

    protected $casts = [
        'is_primary' => 'boolean'
    ];

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }
}