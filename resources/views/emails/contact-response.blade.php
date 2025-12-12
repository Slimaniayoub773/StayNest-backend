<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject }}</title>
    <style>
        /* Reset and basic styles */
        body { margin: 0; padding: 0; background-color: #f4f4f4; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table { border-spacing: 0; border-collapse: collapse; }
        img { border: 0; display: block; line-height: 100%; outline: none; text-decoration: none; }
        
        /* Mobile styles */
        @media screen and (max-width: 600px) {
            .container { width: 100% !important; }
            .content-padding { padding: 20px !important; }
        }
    </style>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; line-height: 1.6; color: #333333; margin: 0; padding: 0; background-color: #f4f4f4;">

    <div style="width: 100%; background-color: #f4f4f4; padding: 20px 0;">
        
        <div class="container" style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">

            <div style="background: linear-gradient(180deg, #d95a2b 0%, #c44720 100%); padding: 40px 20px 50px 20px; text-align: center; color: white; border-bottom-left-radius: 50% 30px; border-bottom-right-radius: 50% 30px;">
                <h1 style="margin: 0; font-size: 36px; font-weight: bold; letter-spacing: -1px; text-shadow: 0 1px 3px rgba(0,0,0,0.2);">StayNest</h1>
                <p style="margin: 5px 0 0 0; font-size: 18px; opacity: 0.9;">Response to Your Inquiry</p>
            </div>

            <div class="content-padding" style="padding: 40px 30px;">
                
                <p style="font-size: 18px; font-weight: bold; margin-bottom: 20px; color: #222;">
                    Dear {{ $contactMessage->name ?? 'John Smith' }},
                </p>

                <p style="color: #555; margin-bottom: 25px;">
                    Thank you for contacting StayNest. We appreciate you reaching out to us and have reviewed your inquiry.
                </p>

                <div style="background-color: #fcf4dd; border-left: 12px solid #bd4b1c; padding: 20px; margin-bottom: 25px; border-radius: 4px;">
                    <h3 style="color: #bd4b1c; margin: 0 0 10px 0; font-size: 20px; font-weight: bold;">Our Response:</h3>
                    <div style="color: #5d4a3a; font-size: 15px; line-height: 1.6;">
                        {!! nl2br(e($responseMessage ?? 'Thank you for your interest in StayNest. We are pleased to confirm that we have availability for your requested dates. Please visit our website to proceed with the booking.')) !!}
                    </div>
                </div>

                <div style="background-color: #f2f2f2; border: 1px solid #e0e0e0; padding: 20px; border-radius: 6px; margin-bottom: 30px;">
                    <h4 style="color: #444; margin: 0 0 10px 0; font-size: 16px; font-weight: bold;">Your original message:</h4>
                    <div style="color: #666; font-size: 14px; font-style: italic;">
                        "{!! nl2br(e($contactMessage->message ?? 'Hello, I am interested in booking a room for next month. Do you have any special offers?')) !!}"
                    </div>
                </div>

                <p style="text-align: center; color: #888; font-size: 13px; line-height: 1.5; margin-top: 30px; border-top: 1px solid #eee; padding-top: 20px;">
                    If you have any further questions, please contact us.<br>
                    We look forward to welcoming you to StayNest.
                </p>
            </div>

            <div style="background-color: #f9f9f9; padding: 30px 20px; text-align: center; border-top: 1px solid #eeeeee;">
                
                <div style="margin-bottom: 15px;">
                    @if(isset($hotelLogo) && $hotelLogo)
                        <img src="{{ $hotelLogo }}" alt="StayNest Logo" style="max-height: 50px; display: inline-block;">
                    @else
                        <div style="display: inline-block; vertical-align: middle;">
                            <strong style="color: #c48b45; font-size: 24px; display: block;">StayNest</strong>
                        </div>
                    @endif
                </div>

                <p style="color: #999; font-size: 12px; margin: 5px 0;">Phone: +212 636847568 | Email: support@StayNest.com</p>
                <p style="color: #999; font-size: 12px; margin: 5px 0;">© {{ date('Y') }} StayNest. All rights reserved.</p>
            </div>

        </div>
    </div>

</body>
</html>