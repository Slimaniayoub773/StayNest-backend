<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Booking;

class NewBookingNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $booking;

    /**
     * Create a new notification instance.
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
{
    return [
        'title' => 'New Booking Received',
        'message' => 'A new booking #' . $this->booking->id . ' has been created for ' . 
                    $this->booking->room->name . ' from ' . $this->booking->check_in_date->format('M d, Y'),
        'action_url' => '/bookings/' . $this->booking->id,
        'category' => 'booking',
        'priority' => 'high'
    ];
}
}