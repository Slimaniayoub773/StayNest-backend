<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;

class CustomResetPasswordNotification extends Notification
{
    use Queueable;

    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
{
    $resetUrl = url(config('app.frontend_url').'/reset-password?token='.$this->token.'&email='.$notifiable->email);
    $expires = config('auth.passwords.'.config('auth.defaults.passwords').'.expire');
    
    return (new MailMessage)
        ->subject('StayNest Password Reset Notification')
        ->markdown('emails.password-reset', [
            'resetUrl' => $resetUrl,
            'expires' => $expires
        ]);
}
}