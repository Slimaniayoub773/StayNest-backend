<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class PaymentReceivedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $payment;

    /**
     * Create a new notification instance.
     */
    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Payment Received - Booking #' . $this->payment->booking_id)
                    ->line('A new payment has been successfully processed.')
                    ->line('Payment ID: #' . $this->payment->id)
                    ->line('Amount: $' . number_format($this->payment->amount, 2))
                    ->line('Booking ID: #' . $this->payment->booking_id)
                    ->line('Payment Method: ' . ucfirst($this->payment->payment_method))
                    ->line('Transaction ID: ' . $this->payment->transaction_id)
                    ->line('Payment Date: ' . $this->payment->payment_date->format('Y-m-d H:i:s'))
                    ->action('View Payment Details', url('/admin/payments/' . $this->payment->id))
                    ->line('Thank you for using our payment system!');
    }

    /**
     * Get the array representation of the notification for database storage.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'payment_received',
            'title' => 'Payment Received',
            'message' => 'Payment of $' . number_format($this->payment->amount, 2) . ' received for Booking #' . $this->payment->booking_id,
            'payment_id' => $this->payment->id,
            'booking_id' => $this->payment->booking_id,
            'amount' => $this->payment->amount,
            'payment_method' => $this->payment->payment_method,
            'transaction_id' => $this->payment->transaction_id,
            'payment_date' => $this->payment->payment_date->toISOString(),
            'status' => $this->payment->status,
            'category' => 'payment',
            'priority' => 'normal',
            'icon' => 'credit-card',
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'type' => 'payment_received',
            'title' => 'Payment Received',
            'message' => 'Payment of $' . number_format($this->payment->amount, 2) . ' received',
            'payment_id' => $this->payment->id,
            'booking_id' => $this->payment->booking_id,
            'amount' => $this->payment->amount,
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Determine the notification's database type.
     */
    public function databaseType(object $notifiable): string
    {
        return 'payment-received';
    }
}