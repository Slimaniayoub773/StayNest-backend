<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\HomePage;

class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $resetUrl;
    public $expires;
    public $hotelLogo;
    public $subject;

    public function __construct($name, $resetUrl, $expires)
    {
        $this->name = $name;
        $this->resetUrl = $resetUrl;
        $this->expires = $expires;
        $this->subject = 'Reset Your Password - StayNest';
        
        // Get hotel logo from HomePage settings
        $homePage = HomePage::first();
        
        if ($homePage && $homePage->logo) {
            // Convert S3 URL to proxied URL
            $this->hotelLogo = $this->convertToProxiedUrl($homePage->logo);
        } else {
            $this->hotelLogo = null;
        }
    }

    public function build()
    {
        return $this->subject($this->subject)
                    ->view('emails.password-reset')
                    ->with([
                        'name' => $this->name,
                        'resetUrl' => $this->resetUrl,
                        'expires' => $this->expires,
                        'hotelLogo' => $this->hotelLogo,
                        'subject' => $this->subject
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

        $parsedUrl = parse_url($s3Url);
        $path = $parsedUrl['path'] ?? '';
        $filename = basename($path);

        if ($filename) {
            return "https://helpful-brenna-leorio7-d20bcb58.koyeb.app/api/home-page-logo/" . urlencode($filename);
        }

        return $s3Url;
    }
}