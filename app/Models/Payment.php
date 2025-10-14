<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'booking_id',
        'payment_method',
        'amount',
        'payment_date',
        'status',
        'receipt_url',
        'transaction_id'
    ];

    protected $casts = [
        'payment_date' => 'datetime',
        'amount' => 'decimal:2'
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    // Helper methods for payment status
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isFailed()
    {
        return $this->status === 'failed';
    }

    // Payment method options
    public static function paymentMethods()
    {
        return [
            'credit_card' => 'Credit Card',
            'debit_card' => 'Debit Card',
            'cash' => 'Cash',
            'bank_transfer' => 'Bank Transfer'
        ];
    }

    // Status options
    public static function statusOptions()
    {
        return [
            'pending' => 'Pending',
            'completed' => 'Completed',
            'failed' => 'Failed'
        ];
    }
}