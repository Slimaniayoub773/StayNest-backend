<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class PaymentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $payment;
    public $title;
    public $message;
    public $category;
    public $priority;
    public $icon;

    /**
     * Create a new notification instance.
     */
    public function __construct(Payment $payment, string $title, string $message, string $category = 'payment', string $priority = 'normal', string $icon = 'credit-card')
    {
        $this->payment = $payment;
        $this->title = $title;
        $this->message = $message;
        $this->category = $category;
        $this->priority = $priority;
        $this->icon = $icon;
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
                    ->subject($this->title)
                    ->line($this->message)
                    ->action('View Payment Details', url('/admin/payments/' . $this->payment->id))
                    ->line('Amount: $' . $this->payment->amount)
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification for database storage.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'payment_notification',
            'title' => $this->title,
            'message' => $this->message,
            'payment_id' => $this->payment->id,
            'booking_id' => $this->payment->booking_id,
            'amount' => $this->payment->amount,
            'payment_method' => $this->payment->payment_method,
            'status' => $this->payment->status,
            'category' => $this->category,
            'priority' => $this->priority,
            'icon' => $this->icon,
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'type' => 'payment_notification',
            'title' => $this->title,
            'message' => $this->message,
            'payment_id' => $this->payment->id,
            'amount' => $this->payment->amount,
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Determine the notification's database type.
     */
    public function databaseType(object $notifiable): string
    {
        return 'payment-notification';
    }
}