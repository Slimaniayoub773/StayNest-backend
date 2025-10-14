<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class GuestCheckinNotification extends Notification implements ShouldQueue
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
                    ->subject('Guest Checked In - Room ' . $this->booking->room->room_number)
                    ->line('Guest has successfully checked in.')
                    ->line('Room: ' . $this->booking->room->room_number)
                    ->line('Guest: ' . $this->booking->guest->name)
                    ->line('Check-in Time: ' . now()->format('Y-m-d H:i:s'))
                    ->line('Expected Check-out: ' . $this->booking->check_out_date)
                    ->action('View Booking Details', url('/admin/bookings/' . $this->booking->id))
                    ->line('Please ensure room services are prepared.');
    }

    /**
     * Get the array representation of the notification for database storage.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'guest_checkin',
            'title' => 'Guest Checked In',
            'message' => $this->booking->guest->name . ' has checked into room ' . $this->booking->room->room_number,
            'booking_id' => $this->booking->id,
            'room_number' => $this->booking->room->room_number,
            'guest_name' => $this->booking->guest->name,
            'check_in_time' => now()->toISOString(),
            'expected_check_out' => $this->booking->check_out_date,
            'category' => 'guest',
            'priority' => 'normal',
            'icon' => 'log-in',
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'type' => 'guest_checkin',
            'title' => 'Guest Checked In',
            'message' => 'Guest checked into room ' . $this->booking->room->room_number,
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
        return 'guest-checkin';
    }
}