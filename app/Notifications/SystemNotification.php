<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class SystemIssueNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $title;
    public $message;
    public $category;
    public $priority;
    public $icon;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $title, string $message, string $category = 'system', string $priority = 'normal', string $icon = 'alert-triangle')
    {
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
                    ->action('View System Logs', url('/admin/system-logs'))
                    ->line('Please check the system immediately!');
    }

    /**
     * Get the array representation of the notification for database storage.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'system_issue_notification',
            'title' => $this->title,
            'message' => $this->message,
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
            'type' => 'system_issue_notification',
            'title' => $this->title,
            'message' => $this->message,
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Determine the notification's database type.
     */
    public function databaseType(object $notifiable): string
    {
        return 'system-issue-notification';
    }
}