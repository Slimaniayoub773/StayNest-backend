<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Room extends Model
{
     use HasFactory;
    protected $primaryKey = 'id';
    protected $fillable = [
        'room_number',
        'type_id',
        'floor_number',
        'price_per_night',
        'description',
        'status',
    ];
    public function type()
    {
        return $this->belongsTo(RoomType::class, 'type_id');
    }

    public function images()
    {
        return $this->hasMany(RoomImage::class, 'room_id');
    }

    public function amenities()
    {
        return $this->belongsToMany(Amenity::class, 'room_amenities', 'room_id', 'amenity_id');
    }

    public function offers()
{
    return $this->belongsToMany(Offer::class, 'room_offers', 'room_id', 'offer_id')
                ->where('is_active', true);
}

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'room_id');
    }

    public function cleanings()
    {
        return $this->hasMany(CleaningSchedule::class, 'room_id');
    }

    public function roomServiceOrders()
    {
        return $this->hasMany(RoomServiceOrder::class, 'room_id');
    }
    public function roomOffers()
{ 
    return $this->hasMany(RoomOffer::class, 'room_id');
}
public function reviews()
{
    return $this->hasMany(Review::class);
}

public function getAverageRatingAttribute()
{
    return $this->reviews()->avg('rating') ?: 0;
}

public function getReviewsCountAttribute()
{
    return $this->reviews()->count();
}
private function getRatingLabel($avg)
{
    if ($avg >= 4.5) return 'Excellent'; // رائع جدًا / ممتاز
    if ($avg >= 4) return 'Very Good';   // جيد جدًا
    if ($avg >= 3) return 'Good';        // جيد
    if ($avg >= 2) return 'Fair';        // مقبول
    if ($avg > 0) return 'Poor';         // ضعيف

    return 'No ratings yet';              // لا توجد تقييمات بعد
}


}

