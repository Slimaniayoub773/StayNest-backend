<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_id',
        'invoice_number',
        'issue_date',
        'due_date',
        'amount',
        'status'
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            $invoice->invoice_number = static::generateInvoiceNumber();
            $invoice->status = 'issued';
        });
    }

    protected static function generateInvoiceNumber()
    {
        $year = date('Y');
        $month = date('m');
        $prefix = "INV-{$year}-{$month}-";
        
        $lastInvoice = static::where('invoice_number', 'like', "{$prefix}%")
            ->orderBy('invoice_number', 'desc')
            ->first();
        
        $nextNumber = $lastInvoice 
            ? (int)str_replace($prefix, '', $lastInvoice->invoice_number) + 1 
            : 1;
            
        return $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
}