<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\HomePage;
use Illuminate\Support\Facades\URL;

class OtpNotification extends Notification
{
    use Queueable;

    public $otp;
    public $guestName;
    public $hotelLogo;

    public function __construct($otp, $guestName = null)
    {
        $this->otp = $otp;
        $this->guestName = $guestName;
        
        // Get hotel logo from HomePage settings
        $this->hotelLogo = $this->getHotelLogo();
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
                'expiresIn' => 10, // minutes
                'hotelLogo' => $this->hotelLogo
            ]);
    }

    /**
     * Get hotel logo from HomePage settings
     */
    private function getHotelLogo()
    {
        try {
            $homePage = HomePage::first();
            
            if ($homePage && $homePage->logo) {
                // Convert S3 URL to proxied URL using the same method as ContactResponseMail
                return $this->convertToProxiedUrl($homePage->logo);
            }
        } catch (\Exception $e) {
            // Log error but don't crash the notification
            \Log::error('Failed to get hotel logo for OTP notification: ' . $e->getMessage());
        }
        
        return null;
    }

    /**
     * Convert S3 URL to proxied URL
     * Same method as in ContactResponseMail
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