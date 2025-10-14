<?php

namespace App\Notifications;

use App\Models\Room;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class CleaningRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $room;
    public $cleaningType;
    public $urgency;
    public $requestedBy;

    /**
     * Create a new notification instance.
     */
    public function __construct(Room $room, string $cleaningType = 'standard', string $urgency = 'normal', string $requestedBy = null)
    {
        $this->room = $room;
        $this->cleaningType = $cleaningType;
        $this->urgency = $urgency;
        $this->requestedBy = $requestedBy;
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
                    ->subject('Cleaning Request - Room ' . $this->room->room_number)
                    ->line('New cleaning request has been submitted.')
                    ->line('Room: ' . $this->room->room_number)
                    ->line('Cleaning Type: ' . ucfirst($this->cleaningType))
                    ->line('Urgency: ' . ucfirst($this->urgency))
                    ->line('Requested By: ' . ($this->requestedBy ?? 'System'))
                    ->action('View Room Details', url('/admin/rooms/' . $this->room->id))
                    ->line('Please assign a cleaner as soon as possible.');
    }

    /**
     * Get the array representation of the notification for database storage.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'cleaning_request',
            'title' => 'Cleaning Request',
            'message' => 'Cleaning requested for room ' . $this->room->room_number . ' (' . $this->cleaningType . ' cleaning)',
            'room_id' => $this->room->id,
            'room_number' => $this->room->room_number,
            'cleaning_type' => $this->cleaningType,
            'urgency' => $this->urgency,
            'requested_by' => $this->requestedBy,
            'category' => 'cleaning',
            'priority' => $this->urgency === 'urgent' ? 'high' : 'normal',
            'icon' => 'broom',
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'type' => 'cleaning_request',
            'title' => 'Cleaning Request',
            'message' => 'Cleaning requested for room ' . $this->room->room_number,
            'room_id' => $this->room->id,
            'room_number' => $this->room->room_number,
            'cleaning_type' => $this->cleaningType,
            'urgency' => $this->urgency,
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Determine the notification's database type.
     */
    public function databaseType(object $notifiable): string
    {
        return 'cleaning-request';
    }
}