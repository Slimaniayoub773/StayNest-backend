<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\ContactMessage;
use App\Models\HomePage; // Add this import

class ContactResponseMail extends Mailable
{
    use Queueable, SerializesModels;

    public $contactMessage;
    public $responseMessage;
    public $subject;
    public $hotelLogo; // Add this property

    public function __construct(ContactMessage $contactMessage, $responseMessage, $subject = null)
    {
        $this->contactMessage = $contactMessage;
        $this->responseMessage = $responseMessage;
        $this->subject = $subject ?? 'Response to your inquiry - StayNest';
        
        // Get hotel logo from HomePage settings
        $homePage = HomePage::first();
        $this->hotelLogo = $homePage ? $homePage->logo : null;
    }

    public function build()
    {
        return $this->subject($this->subject)
                    ->view('emails.contact-response')
                    ->with([
                        'contactMessage' => $this->contactMessage,
                        'responseMessage' => $this->responseMessage,
                        'hotelLogo' => $this->hotelLogo // Pass logo to view
                    ]);
    }
}