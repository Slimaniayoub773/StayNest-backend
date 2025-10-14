<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class UserRegisteredNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $user;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
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
                    ->subject('New User Registration')
                    ->line('A new user has registered in the system.')
                    ->line('Name: ' . $this->user->name)
                    ->line('Email: ' . $this->user->email)
                    ->line('Role: ' . $this->user->role)
                    ->action('View User Details', url('/admin/users/' . $this->user->id))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification for database storage.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'user_registered',
            'title' => 'New User Registration',
            'message' => 'New user ' . $this->user->name . ' (' . $this->user->email . ') has registered',
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'user_email' => $this->user->email,
            'user_role' => $this->user->role,
            'category' => 'user',
            'priority' => 'normal',
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
            'type' => 'user_registered',
            'title' => 'New User Registration',
            'message' => 'New user ' . $this->user->name . ' has registered',
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Determine the notification's database type.
     */
    public function databaseType(object $notifiable): string
    {
        return 'user-registered';
    }
}