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
            .header-logo { width: 80px !important; height: auto !important; }
            .button { padding: 12px 25px !important; font-size: 14px !important; }
            h1 { font-size: 28px !important; }
            h2 { font-size: 20px !important; }
        }
    </style>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; line-height: 1.6; color: #333333; margin: 0; padding: 0; background-color: #f4f4f4;">

    <div style="width: 100%; background-color: #f4f4f4; padding: 20px 0;">
        
        <div class="container" style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">

            <!-- Header with stars / logo / name -->
            <div style="background: linear-gradient(180deg, #d95a2b 0%, #c44720 100%);
                        padding: 60px 20px 50px 20px; text-align: center;
                        color: white; border-bottom-left-radius: 50% 30px;
                        border-bottom-right-radius: 50% 30px; position: relative;">

                <!-- Stars / Decorative SVG -->
                <svg width="100%" height="80" style="position:absolute; top:10px; left:0;">
                    <circle cx="50" cy="20" r="3" fill="white" opacity="0.8"/>
                    <circle cx="120" cy="35" r="2" fill="white" opacity="0.7"/>
                    <circle cx="250" cy="15" r="4" fill="white" opacity="0.9"/>
                    <circle cx="350" cy="40" r="3" fill="white" opacity="0.6"/>
                    <circle cx="500" cy="25" r="2" fill="white" opacity="0.7"/>
                </svg>

                <!-- Logo centered above name -->
                @if(isset($hotelLogo) && $hotelLogo)
                    <div style="text-align:center; margin-bottom: 15px;">
                        <img src="{{ $hotelLogo }}" alt="StayNest Logo" style="width:100px; height:auto; display:inline-block;">
                    </div>
                @endif

                <h1 style="margin: 0; font-size: 36px; font-weight: bold; letter-spacing: -1px; text-shadow: 0 1px 3px rgba(0,0,0,0.2);">
                    StayNest
                </h1>
                <p style="margin: 5px 0 0 0; font-size: 18px; opacity: 0.9;">Reset Your Password</p>
            </div>

            <!-- Content -->
            <div class="content-padding" style="padding: 40px 30px;">
                <h2 style="font-size: 22px; font-weight: bold; margin-bottom: 20px; color: #222; text-align: center;">
                    Hello {{ $name }}!
                </h2>

                <p style="color: #555; margin-bottom: 25px; text-align: center;">
                    You are receiving this email because we received a password reset request for your StayNest account.
                    Click the button below to reset your password.
                </p>

                <!-- Reset Button -->
                <div style="text-align: center; margin: 40px 0;">
                    <a href="{{ $resetUrl }}" class="button" style="display: inline-block; padding: 16px 40px; background: linear-gradient(135deg, #d95a2b 0%, #c44720 100%); color: #ffffff !important; text-decoration: none; border-radius: 50px; font-weight: 600; font-size: 16px; box-shadow: 0 4px 12px rgba(217, 90, 43, 0.3); transition: all 0.3s ease;">
                        Reset Password
                    </a>
                </div>

                <!-- Expiry Notice -->
                <div style="background-color: #fff8f0; border-left: 12px solid #d95a2b; padding: 20px; margin-bottom: 30px; border-radius: 4px;">
                    <h3 style="color: #d95a2b; margin: 0 0 10px 0; font-size: 18px; font-weight: bold;">
                        Important Notice
                    </h3>
                    <div style="color: #5d4a3a; font-size: 15px; line-height: 1.6;">
                        This password reset link will expire in <strong>{{ $expires }} minutes</strong>. 
                        Please use it before it expires for security reasons.
                    </div>
                </div>

                <!-- Manual URL -->
                <div style="background-color: #f9f9f9; border: 1px solid #e0e0e0; padding: 20px; border-radius: 6px; margin-bottom: 30px; text-align: center;">
                    <h4 style="color: #444; margin: 0 0 15px 0; font-size: 16px; font-weight: bold;">
                        Having trouble with the button?
                    </h4>
                    <div style="color: #666; font-size: 14px; word-break: break-all; background-color: #ffffff; padding: 12px; border-radius: 4px; border: 1px solid #eee;">
                        {{ $resetUrl }}
                    </div>
                    <p style="color: #888; font-size: 13px; margin-top: 10px;">
                        Copy and paste this URL into your browser
                    </p>
                </div>

                <!-- Simple Instructions -->
                <div style="margin-bottom: 30px; text-align: center;">
                    <h4 style="color: #444; margin: 0 0 20px 0; font-size: 18px; font-weight: bold;">
                        How to reset your password
                    </h4>
                    
                    <div style="color: #555; font-size: 15px; line-height: 1.6;">
                        <p style="margin-bottom: 15px;">
                            Click the "Reset Password" button above to begin the reset process. 
                            You will be directed to a secure page where you can create a new password for your account.
                        </p>
                        
                        <p style="margin-bottom: 15px;">
                            Choose a strong password that you haven't used before. 
                            We recommend using a combination of letters, numbers, and special characters.
                        </p>
                        
                        <p>
                            After resetting your password, you can log in to your StayNest account with your new credentials.
                        </p>
                    </div>
                </div>

                <!-- Security Note -->
                <div style="background-color: #f0f9ff; border: 2px solid #b3d9ff; padding: 20px; border-radius: 8px; margin-bottom: 30px; text-align: center;">
                    <div style="color: #0066cc; font-weight: bold; margin-bottom: 10px; font-size: 16px;">
                        Security Reminder
                    </div>
                    <div style="color: #555; font-size: 14px;">
                        Never share your password reset link with anyone. StayNest will never ask for your password via email, phone, or text message.
                    </div>
                </div>

                <!-- Footer Message -->
                <div style="text-align: center; color: #888; font-size: 13px; line-height: 1.5; margin-top: 30px; border-top: 1px solid #eee; padding-top: 20px;">
                    If you didn't request a password reset, please ignore this email or 
                    contact our support team if you have concerns about your account security.
                </div>
            </div>

            <!-- Footer -->
            <div style="background-color: #f9f9f9; padding: 30px 20px; text-align: center; border-top: 1px solid #eeeeee;">
                
                <div style="margin-bottom: 15px;">
                    @if(isset($hotelLogo) && $hotelLogo)
                        <img src="{{ $hotelLogo }}" alt="StayNest Logo" style="max-height: 50px; display: inline-block;">
                    @else
                        <div style="display: inline-block; vertical-align: middle;">
                            <strong style="color: #d95a2b; font-size: 24px; display: block;">StayNest</strong>
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