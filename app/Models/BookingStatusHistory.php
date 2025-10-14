<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingStatusHistory extends Model
{
    use HasFactory;
        protected $table = 'booking_status_history';
    protected $fillable = [
        'booking_id',
        'status',
        'changed_by',
        'notes',
        'changed_at'    
    ];

    protected $casts = [
        'changed_at' => 'datetime'
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'confirmed' => 'Confirmed',
            'checked_in' => 'Checked In',
            'checked_out' => 'Checked Out',
            'cancelled' => 'Cancelled',
            'no_show' => 'No Show',
            default => ucfirst($this->status)
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'confirmed' => 'blue',
            'checked_in' => 'green',
            'checked_out' => 'indigo',
            'cancelled' => 'red',
            'no_show' => 'yellow',
            default => 'gray'
        };
    }
}