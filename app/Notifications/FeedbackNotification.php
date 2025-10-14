<?php

namespace App\Notifications;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class FeedbackNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $review;
    public $title;
    public $message;
    public $category;
    public $priority;
    public $icon;

    /**
     * Create a new notification instance.
     */
    public function __construct(Review $review, string $title, string $message, string $category = 'review', string $priority = 'normal', string $icon = 'star')
    {
        $this->review = $review;
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
                    ->action('View Review', url('/admin/reviews/' . $this->review->id))
                    ->line('Rating: ' . $this->review->rating . '/5')
                    ->line('Room: ' . $this->review->room->room_number)
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification for database storage.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'review_notification',
            'title' => $this->title,
            'message' => $this->message,
            'review_id' => $this->review->id,
            'rating' => $this->review->rating,
            'room_id' => $this->review->room_id,
            'room_number' => $this->review->room->room_number,
            'guest_name' => $this->review->guest->name,
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
            'type' => 'review_notification',
            'title' => $this->title,
            'message' => $this->message,
            'review_id' => $this->review->id,
            'rating' => $this->review->rating,
            'room_number' => $this->review->room->room_number,
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Determine the notification's database type.
     */
    public function databaseType(object $notifiable): string
    {
        return 'review-notification';
    }
}