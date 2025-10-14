<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class GuestCheckoutNotification extends Notification implements ShouldQueue
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
                    ->subject('Guest Checked Out - Room ' . $this->booking->room->room_number)
                    ->line('Guest has successfully checked out.')
                    ->line('Room: ' . $this->booking->room->room_number)
                    ->line('Guest: ' . $this->booking->guest->name)
                    ->line('Check-out Time: ' . now()->format('Y-m-d H:i:s'))
                    ->line('Stay Duration: ' . $this->calculateStayDuration() . ' days')
                    ->action('View Booking Details', url('/admin/bookings/' . $this->booking->id))
                    ->line('Room is now ready for cleaning and preparation for next guest.');
    }

    /**
     * Get the array representation of the notification for database storage.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'guest_checkout',
            'title' => 'Guest Checked Out',
            'message' => $this->booking->guest->name . ' has checked out from room ' . $this->booking->room->room_number,
            'booking_id' => $this->booking->id,
            'room_number' => $this->booking->room->room_number,
            'guest_name' => $this->booking->guest->name,
            'check_out_time' => now()->toISOString(),
            'stay_duration' => $this->calculateStayDuration(),
            'category' => 'guest',
            'priority' => 'normal',
            'icon' => 'log-out',
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'type' => 'guest_checkout',
            'title' => 'Guest Checked Out',
            'message' => 'Guest checked out from room ' . $this->booking->room->room_number,
            'booking_id' => $this->booking->id,
            'room_number' => $this->booking->room->room_number,
            'guest_name' => $this->booking->guest->name,
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Calculate stay duration in days
     */
    private function calculateStayDuration(): int
    {
        $checkIn = \Carbon\Carbon::parse($this->booking->actual_check_in ?? $this->booking->check_in_date);
        $checkOut = \Carbon\Carbon::parse($this->booking->actual_check_out ?? now());
        
        return $checkIn->diffInDays($checkOut);
    }

    /**
     * Determine the notification's database type.
     */
    public function databaseType(object $notifiable): string
    {
        return 'guest-checkout';
    }
}