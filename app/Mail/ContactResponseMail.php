<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\ContactMessage;
use App\Models\HomePage;
use Illuminate\Support\Facades\URL; // Add this import

class ContactResponseMail extends Mailable
{
    use Queueable, SerializesModels;

    public $contactMessage;
    public $responseMessage;
    public $subject;
    public $hotelLogo;

    public function __construct(ContactMessage $contactMessage, $responseMessage, $subject = null)
    {
        $this->contactMessage = $contactMessage;
        $this->responseMessage = $responseMessage;
        $this->subject = $subject ?? 'Response to your inquiry - StayNest';
        
        // Get hotel logo from HomePage settings
        $homePage = HomePage::first();
        
        if ($homePage && $homePage->logo) {
    $this->hotelLogo = $homePage->logo; // direct URL, perfect for email
} else {
    $this->hotelLogo = null;
}
    }

    public function build()
    {
        return $this->subject($this->subject)
                    ->view('emails.contact-response')
                    ->with([
                        'contactMessage' => $this->contactMessage,
                        'responseMessage' => $this->responseMessage,
                        'hotelLogo' => $this->hotelLogo
                    ]);
    }

    /**
     * Convert S3 URL to proxied URL
     */
    private function convertToProxiedUrl($s3Url)
    {
        if (!$s3Url) {
            return null;
        }
        
        // Extract filename from S3 URL
        $parsedUrl = parse_url($s3Url);
        $path = $parsedUrl['path'] ?? '';
        
        // Get the filename from the path
        $filename = basename($path);
        
        if ($filename) {
            // Generate the proxied URL
            return URL::to('/api/home-page/proxy-logo/' . urlencode($filename));
        }
        
        return $s3Url;
    }
}