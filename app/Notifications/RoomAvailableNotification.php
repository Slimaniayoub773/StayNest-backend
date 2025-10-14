<?php

namespace App\Notifications;

use App\Models\Room;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class RoomAvailableNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $room;
    public $availabilityReason;

    /**
     * Create a new notification instance.
     */
    public function __construct(Room $room, string $availabilityReason = null)
    {
        $this->room = $room;
        $this->availabilityReason = $availabilityReason;
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
        $mail = (new MailMessage)
                    ->subject('Room Available - ' . $this->room->room_number)
                    ->line('A room has become available and is ready for new bookings.')
                    ->line('Room Number: ' . $this->room->room_number)
                    ->line('Room Type: ' . ($this->room->type->name ?? 'N/A'))
                    ->line('Floor: ' . $this->room->floor_number)
                    ->line('Price Per Night: $' . number_format($this->room->price_per_night, 2))
                    ->line('Current Status: ' . ucfirst($this->room->status));

        // Add reason if provided
        if ($this->availabilityReason) {
            $mail->line('Availability Reason: ' . $this->availabilityReason);
        }

        $mail->action('View Room Details', url('/admin/rooms/' . $this->room->id))
             ->line('The room is now ready to be assigned to new guests.');

        return $mail;
    }

    /**
     * Get the array representation of the notification for database storage.
     */
    public function toArray(object $notifiable): array
    {
        $data = [
            'type' => 'room_available',
            'title' => 'Room Available',
            'message' => 'Room ' . $this->room->room_number . ' is now available for booking',
            'room_id' => $this->room->id,
            'room_number' => $this->room->room_number,
            'room_type' => $this->room->type->name ?? null,
            'floor_number' => $this->room->floor_number,
            'price_per_night' => $this->room->price_per_night,
            'room_status' => $this->room->status,
            'category' => 'room',
            'priority' => 'normal',
            'icon' => 'home',
            'timestamp' => now()->toISOString(),
        ];

        // Add reason if provided
        if ($this->availabilityReason) {
            $data['availability_reason'] = $this->availabilityReason;
            $data['message'] .= ' - ' . $this->availabilityReason;
        }

        return $data;
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        $broadcastData = [
            'type' => 'room_available',
            'title' => 'Room Available',
            'message' => 'Room ' . $this->room->room_number . ' is now available',
            'room_id' => $this->room->id,
            'room_number' => $this->room->room_number,
            'room_type' => $this->room->type->name ?? null,
            'timestamp' => now()->toISOString(),
        ];

        // Add reason if provided
        if ($this->availabilityReason) {
            $broadcastData['availability_reason'] = $this->availabilityReason;
        }

        return new BroadcastMessage($broadcastData);
    }

    /**
     * Determine the notification's database type.
     */
    public function databaseType(object $notifiable): string
    {
        return 'room-available';
    }
}