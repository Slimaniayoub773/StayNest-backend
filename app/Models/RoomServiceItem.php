<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomServiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name_ar',
        'name_en',
        'description',
        'price',
        'preparation_time',
        'is_available',
        'image_url'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'preparation_time' => 'integer',
        'is_available' => 'boolean'
    ];

    public function category()
    {
        return $this->belongsTo(RoomServiceCategory::class, 'category_id');
    }
    public function getImageUrlAttribute($value)
    {
        if (!$value) {
            return null;
        }
        
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }
        
        return asset("storage/{$value}");
    }
}