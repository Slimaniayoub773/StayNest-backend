<?php

namespace App\Notifications;

use App\Models\Guest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;

class GuestRegisteredNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $guest;

    /**
     * Create a new notification instance.
     */
    public function __construct(Guest $guest)
    {
        $this->guest = $guest;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast']; // You can add 'mail' if you want email notifications too
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('New Guest Registration')
                    ->line('A new guest has registered: ' . $this->guest->name)
                    ->line('Email: ' . $this->guest->email)
                    ->line('Phone: ' . $this->guest->phone)
                    ->action('View Guest Details', url('/admin/guests/' . $this->guest->id))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification for database storage.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'guest_registered',
            'title' => 'New Guest Registration',
            'message' => 'A new guest ' . $this->guest->name . ' has registered.',
            'guest_id' => $this->guest->id,
            'guest_name' => $this->guest->name,
            'guest_email' => $this->guest->email,
            'guest_phone' => $this->guest->phone,
            'action_url' => '/admin/guests/' . $this->guest->id,
            'icon' => 'user-plus',
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'type' => 'guest_registered',
            'title' => 'New Guest Registration',
            'message' => 'A new guest ' . $this->guest->name . ' has registered.',
            'guest_id' => $this->guest->id,
            'guest_name' => $this->guest->name,
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Determine the notification's database type.
     */
    public function databaseType(object $notifiable): string
    {
        return 'guest-registered';
    }
}