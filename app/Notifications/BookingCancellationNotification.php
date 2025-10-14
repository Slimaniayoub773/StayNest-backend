<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class BookingCancellationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $booking;
    public $cancellationReason;

    /**
     * Create a new notification instance.
     */
    public function __construct(Booking $booking, string $cancellationReason = null)
    {
        $this->booking = $booking;
        $this->cancellationReason = $cancellationReason;
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
                    ->subject('Booking Cancellation - #' . $this->booking->id)
                    ->line('Booking #' . $this->booking->id . ' has been cancelled.')
                    ->line('Room: ' . $this->booking->room->room_number)
                    ->line('Guest: ' . $this->booking->guest->name)
                    ->line('Cancellation Reason: ' . ($this->cancellationReason ?? 'Not specified'))
                    ->action('View Booking Details', url('/admin/bookings/' . $this->booking->id))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification for database storage.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'booking_cancellation',
            'title' => 'Booking Cancelled',
            'message' => 'Booking #' . $this->booking->id . ' for room ' . $this->booking->room->room_number . ' has been cancelled by ' . $this->booking->guest->name,
            'booking_id' => $this->booking->id,
            'room_number' => $this->booking->room->room_number,
            'guest_name' => $this->booking->guest->name,
            'cancellation_reason' => $this->cancellationReason,
            'category' => 'booking',
            'priority' => 'high',
            'icon' => 'x-circle',
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'type' => 'booking_cancellation',
            'title' => 'Booking Cancelled',
            'message' => 'Booking #' . $this->booking->id . ' has been cancelled',
            'booking_id' => $this->booking->id,
            'room_number' => $this->booking->room->room_number,
            'guest_name' => $this->booking->guest->name,
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Determine the notification's database type.
     */
    public function databaseType(object $notifiable): string
    {
        return 'booking-cancellation';
    }
}