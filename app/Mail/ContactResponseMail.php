<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\ContactMessage;

class ContactResponseMail extends Mailable
{
    use Queueable, SerializesModels;

    public $contactMessage;
    public $responseMessage;
    public $subject;

    public function __construct(ContactMessage $contactMessage, $responseMessage, $subject = null)
    {
        $this->contactMessage = $contactMessage;
        $this->responseMessage = $responseMessage;
        $this->subject = $subject ?? 'Response to your inquiry - Luxury Hotel';
    }

    public function build()
    {
        return $this->subject($this->subject)
                    ->view('emails.contact-response')
                    ->with([
                        'contactMessage' => $this->contactMessage,
                        'responseMessage' => $this->responseMessage
                    ]);
    }
}