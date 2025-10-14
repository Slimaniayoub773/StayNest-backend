<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomServiceOrderItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_id',
        'item_id',
        'quantity',
        'unit_price',
        'notes'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2'
    ];
    public function order()
    {
        return $this->belongsTo(RoomServiceOrder::class, 'order_id');
    }

    public function item()
    {
        return $this->belongsTo(RoomServiceItem::class, 'item_id');
    }
}
