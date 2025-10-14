<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CleaningSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'cleaner_id',
        'cleaning_date',
        'cleaning_status',  
        'priority_level',
        'notes'
    ];

    protected $casts = [
        'cleaning_date' => 'date',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function cleaner()
    {
        return $this->belongsTo(User::class, 'cleaner_id');
    }
}
