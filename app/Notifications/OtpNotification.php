<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OtpNotification extends Notification
{
    use Queueable;

    public $otp;
    public $guestName;

    public function __construct($otp, $guestName = null)
    {
        $this->otp = $otp;
        $this->guestName = $guestName;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $name = $this->guestName ?: $notifiable->name;
        
        return (new MailMessage)
            ->subject('Verify Your Email - StayNest OTP Code')
            ->view('emails.otp-verification', [
                'name' => $name,
                'otp' => $this->otp,
                'expiresIn' => 10 // minutes
            ]);
    }
}