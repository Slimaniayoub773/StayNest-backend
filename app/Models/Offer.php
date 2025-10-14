<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'description',
        'discount_percentage',
        'start_date',
        'end_date',
        'promo_code',
        'is_active'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean'
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now());
    }

    public function rooms()
    {
        return $this->belongsToMany(Room::class, 'room_offers', 'offer_id', 'room_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'offer_id');
    }
}

