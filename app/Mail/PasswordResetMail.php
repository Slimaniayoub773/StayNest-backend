<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $resetUrl;
    public $expires;

    public function __construct($name, $resetUrl, $expires)
    {
        $this->name = $name;
        $this->resetUrl = $resetUrl;
        $this->expires = $expires;
    }

    public function build()
    {
        return $this->subject('Reset Your Password - StayNest')
                    ->view('emails.password-reset'); // This should point to your HTML template
    }
}