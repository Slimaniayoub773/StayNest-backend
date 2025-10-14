<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'guest_id',
        'room_id',
        'offer_id',
        'check_in_date',
        'check_out_date',
        'booking_status',
        'total_price',
        'payment_status',
        'number_of_guests',
        'cancellation_policy',
        'special_requests'
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'total_price' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function guest()
    {
        return $this->belongsTo(Guest::class, 'guest_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function offer()
    {
        return $this->belongsTo(Offer::class, 'offer_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'booking_id');
    }

    public function statusHistory()
    {
        return $this->hasMany(BookingStatusHistory::class, 'booking_id');
    }

    public function roomServiceOrders()
    {
        return $this->hasMany(RoomServiceOrder::class, 'booking_id');
    }
}

