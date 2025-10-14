<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class RoomServiceNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $booking;
    public $title;
    public $message;
    public $category;
    public $priority;
    public $icon;
    public $serviceType;
    public $urgency;

    /**
     * Create a new notification instance.
     */
    public function __construct(Booking $booking, string $title, string $message, string $category = 'room_service', string $priority = 'normal', string $icon = 'bell', string $serviceType = null, string $urgency = null)
    {
        $this->booking = $booking;
        $this->title = $title;
        $this->message = $message;
        $this->category = $category;
        $this->priority = $priority;
        $this->icon = $icon;
        $this->serviceType = $serviceType;
        $this->urgency = $urgency;
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
                    ->action('View Booking Details', url('/admin/bookings/' . $this->booking->id))
                    ->line('Room: ' . $this->booking->room->room_number)
                    ->line('Guest: ' . $this->booking->guest->name)
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification for database storage.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'room_service_notification',
            'title' => $this->title,
            'message' => $this->message,
            'booking_id' => $this->booking->id,
            'room_number' => $this->booking->room->room_number,
            'guest_name' => $this->booking->guest->name,
            'service_type' => $this->serviceType,
            'urgency' => $this->urgency,
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
            'type' => 'room_service_notification',
            'title' => $this->title,
            'message' => $this->message,
            'booking_id' => $this->booking->id,
            'room_number' => $this->booking->room->room_number,
            'service_type' => $this->serviceType,
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Determine the notification's database type.
     */
    public function databaseType(object $notifiable): string
    {
        return 'room-service-notification';
    }
}