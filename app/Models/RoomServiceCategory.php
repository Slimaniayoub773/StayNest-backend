<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomServiceCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_ar',
        'name_en',
        'description',
        'is_food',
        'available_hours'
    ];

    protected $casts = [
        'is_food' => 'boolean',
    ];
     public function items()
    {
        return $this->hasMany(RoomServiceItem::class, 'category_id');
    }
}