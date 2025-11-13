<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class CustomResetPasswordNotification extends Notification
{
    use Queueable;

    public $token;
    public $expires;

    public function __construct($token)
    {
        $this->token = $token;
        $this->expires = config('auth.passwords.guests.expire', 60);
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        // Use your frontend reset password URL
        $frontendUrl = env('FRONTEND_URL', 'http://localhost:3000');
        $resetUrl = $frontendUrl . '/reset-password?' . http_build_query([
            'token' => $this->token,
            'email' => $notifiable->email,
        ]);

        return (new MailMessage)
            ->subject('Reset Your Password - StayNest')
            ->view('emails.password-reset', [
                'name' => $notifiable->name,
                'resetUrl' => $resetUrl,
                'expires' => $this->expires
            ]);
    }
}