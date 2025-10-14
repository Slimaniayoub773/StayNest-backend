<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'guest_id',
        'room_id',
        'booking_id',
        'rating',
        'comment',
    ];

    // العلاقة مع الضيف
    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    // العلاقة مع الغرفة
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    // العلاقة مع الحجز (اختياري إذا تحتاجه)
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
