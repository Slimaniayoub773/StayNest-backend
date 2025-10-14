<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class BookingConfirmationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $booking;

    /**
     * Create a new notification instance.
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
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
                    ->subject('Booking Confirmed - #' . $this->booking->id)
                    ->line('Your booking has been confirmed!')
                    ->line('Booking ID: #' . $this->booking->id)
                    ->line('Room: ' . $this->booking->room->room_number)
                    ->line('Check-in: ' . $this->booking->check_in_date)
                    ->line('Check-out: ' . $this->booking->check_out_date)
                    ->line('Total Amount: $' . $this->booking->total_price)
                    ->action('View Booking Details', url('/admin/bookings/' . $this->booking->id))
                    ->line('Thank you for choosing our hotel!');
    }

    /**
     * Get the array representation of the notification for database storage.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'booking_confirmation',
            'title' => 'Booking Confirmed',
            'message' => 'Booking #' . $this->booking->id . ' for room ' . $this->booking->room->room_number . ' has been confirmed',
            'booking_id' => $this->booking->id,
            'room_number' => $this->booking->room->room_number,
            'guest_name' => $this->booking->guest->name,
            'check_in_date' => $this->booking->check_in_date,
            'check_out_date' => $this->booking->check_out_date,
            'total_amount' => $this->booking->total_price,
            'category' => 'booking',
            'priority' => 'normal',
            'icon' => 'check-circle',
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'type' => 'booking_confirmation',
            'title' => 'Booking Confirmed',
            'message' => 'Booking #' . $this->booking->id . ' has been confirmed',
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
        return 'booking-confirmation';
    }
}