<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class PaymentFailedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $payment;
    public $errorMessage;
    public $retryCount;

    /**
     * Create a new notification instance.
     */
    public function __construct(Payment $payment, string $errorMessage, int $retryCount = 0)
    {
        $this->payment = $payment;
        $this->errorMessage = $errorMessage;
        $this->retryCount = $retryCount;
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
                    ->subject('Payment Failed - Booking #' . $this->payment->booking_id)
                    ->line('A payment attempt has failed.')
                    ->line('Booking ID: #' . $this->payment->booking_id)
                    ->line('Amount: $' . number_format($this->payment->amount, 2))
                    ->line('Payment Method: ' . ucfirst($this->payment->payment_method))
                    ->line('Error: ' . $this->errorMessage)
                    ->line('Retry Attempt: ' . ($this->retryCount + 1))
                    ->line('Failed At: ' . now()->format('Y-m-d H:i:s'))
                    ->action('View Booking Details', url('/admin/bookings/' . $this->payment->booking_id))
                    ->line('Please review the payment details and contact the guest if necessary.');
    }

    /**
     * Get the array representation of the notification for database storage.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'payment_failed',
            'title' => 'Payment Failed',
            'message' => 'Payment of $' . number_format($this->payment->amount, 2) . ' failed for Booking #' . $this->payment->booking_id,
            'payment_id' => $this->payment->id,
            'booking_id' => $this->payment->booking_id,
            'amount' => $this->payment->amount,
            'payment_method' => $this->payment->payment_method,
            'error_message' => $this->errorMessage,
            'retry_count' => $this->retryCount,
            'status' => $this->payment->status,
            'category' => 'payment',
            'priority' => 'high',
            'icon' => 'alert-triangle',
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'type' => 'payment_failed',
            'title' => 'Payment Failed',
            'message' => 'Payment failed for Booking #' . $this->payment->booking_id,
            'payment_id' => $this->payment->id,
            'booking_id' => $this->payment->booking_id,
            'amount' => $this->payment->amount,
            'error_message' => $this->errorMessage,
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Determine the notification's database type.
     */
    public function databaseType(object $notifiable): string
    {
        return 'payment-failed';
    }
}