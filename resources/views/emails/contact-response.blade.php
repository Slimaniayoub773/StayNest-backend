<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $subject }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto;">
    
    <!-- Simple Header -->
    <div style="background: #d95a2b; padding: 20px; text-align: center; color: white;">
        <h1 style="margin: 0; font-size: 28px;">StayNest</h1>
        <p style="margin: 5px 0 0 0; font-size: 16px;">Response to Your Inquiry</p>
    </div>
    
    <!-- Content -->
    <div style="padding: 30px; background: white;">
        <p style="font-size: 18px; font-weight: bold; margin-bottom: 20px;">
            Dear {{ $contactMessage->name ?? 'Guest' }},
        </p>
        
        <p>Thank you for contacting StayNest. We appreciate you reaching out to us and have reviewed your inquiry.</p>
        
        <div style="background: #f9f9f9; border-left: 4px solid #d95a2b; padding: 15px; margin: 20px 0;">
            <strong style="color: #d95a2b;">Our Response:</strong><br>
            {!! nl2br(e($responseMessage ?? 'Thank you for your interest in StayNest.')) !!}
        </div>
        
        <div style="background: #f0f0f0; padding: 15px; margin: 20px 0; border-radius: 4px;">
            <strong>Your original message:</strong><br>
            {!! nl2br(e($contactMessage->message ?? 'No message provided.')) !!}
        </div>
        
        <p style="text-align: center; color: #666; margin-top: 30px;">
            If you have any further questions, please contact us.<br>
            We look forward to welcoming you to StayNest.
        </p>
    </div>
    
    <!-- Footer -->
    <div style="background: #f5f5f5; padding: 20px; text-align: center; color: #777; font-size: 14px; border-top: 1px solid #ddd;">
        @if(isset($hotelLogo) && $hotelLogo)
            <img src="{{ $hotelLogo }}" alt="StayNest Logo" style="max-height: 40px; margin-bottom: 10px;"><br>
        @else
            <strong style="color: #d95a2b;">StayNest</strong><br>
        @endif
        <p style="margin: 5px 0;">Phone: +212 636847568 | Email: support@StayNest.com</p>
        <p style="margin: 5px 0;">© {{ date('Y') }} StayNest. All rights reserved.</p>
    </div>
</body>
</html>