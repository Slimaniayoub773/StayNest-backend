<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomOffer extends Model
{
    use HasFactory;

    protected $fillable = ['room_id', 'offer_id'];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }
}