<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomServiceOrder extends Model
{
    use HasFactory;
    protected $fillable = [
        'booking_id',
        'room_id',
        'guest_id',
        'order_date',
        'status',
        'special_instructions',
        'total_price',
        'delivery_charge',
        'expected_delivery_time',
        'actual_delivery_time'
    ];

    protected $casts = [
        'order_date' => 'datetime',
        'expected_delivery_time' => 'datetime:H:i',
        'actual_delivery_time' => 'datetime:H:i',
        'total_price' => 'decimal:2',
        'delivery_charge' => 'decimal:2',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function guest()
    {
        return $this->belongsTo(Guest::class, 'guest_id');
    }

    public function items()
    {
        return $this->hasMany(RoomServiceOrderItem::class, 'order_id');
    }
}
