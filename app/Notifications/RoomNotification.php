<?php

namespace App\Notifications;

use App\Models\Room;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class RoomNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $room;
    public $title;
    public $message;
    public $category;
    public $priority;
    public $icon;

    /**
     * Create a new notification instance.
     */
    public function __construct(Room $room, string $title, string $message, string $category = 'room', string $priority = 'normal', string $icon = 'home')
    {
        $this->room = $room;
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
                    ->action('View Room Details', url('/admin/rooms/' . $this->room->id))
                    ->line('Room: ' . $this->room->room_number)
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification for database storage.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'room_notification',
            'title' => $this->title,
            'message' => $this->message,
            'room_id' => $this->room->id,
            'room_number' => $this->room->room_number,
            'room_status' => $this->room->status,
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
            'type' => 'room_notification',
            'title' => $this->title,
            'message' => $this->message,
            'room_id' => $this->room->id,
            'room_number' => $this->room->room_number,
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Determine the notification's database type.
     */
    public function databaseType(object $notifiable): string
    {
        return 'room-notification';
    }
}