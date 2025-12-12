<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\ContactMessage;
use App\Models\HomePage;

class ContactResponseMail extends Mailable
{
    use Queueable, SerializesModels;

    public $contactMessage;
    public $responseMessage;
    public $subject;
    public $hotelLogo;
    public $hotelName;

    public function __construct(ContactMessage $contactMessage, $responseMessage, $subject = null)
    {
        $this->contactMessage = $contactMessage;
        $this->responseMessage = $responseMessage;
        $this->subject = $subject ?? 'Response to your inquiry - StayNest';
        
        // Get hotel logo and name from HomePage settings
        $homePage = HomePage::first();
        $this->hotelLogo = $homePage ? $this->getLogoUrl($homePage->logo) : null;
        $this->hotelName = $homePage ? $homePage->name : 'StayNest';
    }

    public function build()
    {
        $email = $this->subject($this->subject)
                    ->view('emails.contact-response')
                    ->with([
                        'contactMessage' => $this->contactMessage,
                        'responseMessage' => $this->responseMessage,
                        'hotelLogo' => $this->hotelLogo,
                        'hotelName' => $this->hotelName
                    ]);

        // Embed logo as inline attachment for better email compatibility
        if ($this->hotelLogo) {
            $email->attach($this->hotelLogo, [
                'as' => 'logo.png',
                'mime' => 'image/png',
            ]);
        }

        return $email;
    }

    /**
     * Convert S3 URL to a format that works in emails
     * If it's an S3 URL, use our proxy endpoint
     */
    private function getLogoUrl($logoUrl)
    {
        if (!$logoUrl) {
            return null;
        }

        // If it's an S3 URL, extract filename and use proxy
        if (str_contains($logoUrl, 'staynest-images.s3.eu-central-2.idrivee2.com')) {
            $urlParts = parse_url($logoUrl);
            $pathParts = explode('/', $urlParts['path']);
            $filename = end($pathParts);
            
            // Use absolute URL for the proxy endpoint
            return url("/api/home-page-logo/{$filename}");
        }

        // If it's already a direct URL, use it
        if (filter_var($logoUrl, FILTER_VALIDATE_URL)) {
            return $logoUrl;
        }

        return null;
    }
}