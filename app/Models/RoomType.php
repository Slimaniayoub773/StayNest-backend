<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomType extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'description',
        'base_price',
        'max_occupancy'
    ];

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function getPriceForDates($startDate, $endDate)
    {
        // You can implement seasonal pricing logic here
        return $this->base_price;
    }
}