<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email - StayNest</title>
    <style>
        /* Reset and basic styles */
        body { margin: 0; padding: 0; background-color: #f4f4f4; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table { border-spacing: 0; border-collapse: collapse; }
        img { border: 0; display: block; line-height: 100%; outline: none; text-decoration: none; }
        
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .header {
            background: linear-gradient(180deg, #d95a2b 0%, #c44720 100%);
            padding: 60px 20px 50px 20px;
            text-align: center;
            color: white;
            border-bottom-left-radius: 50% 30px;
            border-bottom-right-radius: 50% 30px;
            position: relative;
        }
        
        .header-stars {
            position: absolute;
            top: 10px;
            left: 0;
            width: 100%;
            height: 80px;
        }
        
        .header-stars circle {
            fill: white;
        }
        
        .logo-container {
            text-align: center;
            margin-bottom: 15px;
        }
        
        .logo {
            width: 100px;
            height: auto;
            display: inline-block;
        }
        
        .brand-name {
            margin: 0;
            font-size: 36px;
            font-weight: bold;
            letter-spacing: -1px;
            text-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }
        
        .brand-subtitle {
            margin: 5px 0 0 0;
            font-size: 18px;
            opacity: 0.9;
        }
        
        .content {
            padding: 40px 30px;
        }
        
        .greeting {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #222;
        }
        
        .message {
            color: #555;
            margin-bottom: 25px;
            line-height: 1.6;
            text-align: center;
        }
        
        .brand-highlight {
            color: #d95a2b;
            font-weight: bold;
        }
        
        .otp-container {
            text-align: center;
            margin: 40px 0;
        }
        
        .otp-code {
            display: inline-block;
            font-size: 48px;
            font-weight: 700;
            color: #d95a2b;
            letter-spacing: 15px;
            padding: 30px 50px;
            background: #fff8e1;
            border: 2px solid #d95a2b;
            border-radius: 16px;
            margin: 20px 0;
            box-shadow: 0 4px 20px rgba(217, 90, 43, 0.2);
            text-align: center;
            min-width: 300px;
        }
        
        .expiry-notice {
            background-color: #fff8e1;
            border-left: 12px solid #d95a2b;
            padding: 20px;
            margin: 30px 0;
            border-radius: 4px;
        }
        
        .expiry-notice h3 {
            color: #d95a2b;
            margin: 0 0 10px 0;
            font-size: 20px;
            font-weight: bold;
        }
        
        .expiry-notice div {
            color: #5d4a3a;
            font-size: 15px;
            line-height: 1.6;
        }
        
        .instructions {
            margin: 40px 0;
        }
        
        .instruction-item {
            background-color: #fff8e1;
            border: 1px solid #ffcc80;
            padding: 25px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .instruction-item:last-child {
            margin-bottom: 0;
        }
        
        .instruction-title {
            font-weight: 600;
            color: #d95a2b;
            margin-bottom: 8px;
            font-size: 18px;
        }
        
        .instruction-description {
            color: #5d4a3a;
            font-size: 15px;
            line-height: 1.6;
        }
        
        .security-note {
            background-color: #f9f9f9;
            border: 2px solid #ffcc80;
            padding: 20px;
            border-radius: 8px;
            font-size: 14px;
            color: #5d4a3a;
            text-align: center;
            margin: 30px 0;
            font-weight: bold;
        }
        
        .divider {
            height: 1px;
            background: linear-gradient(to right, transparent, #ffcc80, transparent);
            margin: 40px 0;
        }
        
        .footer {
            background-color: #f9f9f9;
            padding: 30px 20px;
            text-align: center;
            border-top: 1px solid #eeeeee;
        }
        
        .footer-logo {
            max-height: 50px;
            display: inline-block;
            margin-bottom: 15px;
        }
        
        .footer-info {
            color: #999;
            font-size: 12px;
            margin: 5px 0;
        }
        
        .footer-link {
            color: #d95a2b;
            text-decoration: none;
        }
        
        .footer-link:hover {
            text-decoration: underline;
        }
        
        /* Mobile styles */
        @media screen and (max-width: 600px) {
            .container { 
                width: 100% !important; 
                border-radius: 0;
            }
            
            .content { 
                padding: 20px !important; 
            }
            
            .header {
                padding: 40px 15px 30px 15px !important;
            }
            
            .brand-name {
                font-size: 28px !important;
            }
            
            .brand-subtitle {
                font-size: 16px !important;
            }
            
            .otp-code {
                font-size: 36px !important;
                letter-spacing: 10px !important;
                padding: 20px 15px !important;
                min-width: auto !important;
                width: 90% !important;
            }
            
            .greeting {
                font-size: 16px !important;
            }
            
            .instruction-item {
                padding: 20px !important;
            }
        }
    </style>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; line-height: 1.6; color: #333333; margin: 0; padding: 0; background-color: #f4f4f4;">

    <div style="width: 100%; background-color: #f4f4f4; padding: 20px 0;">
        <div class="container">
            <!-- Header with stars / logo / name -->
            <div class="header">
                <!-- Stars / Decorative SVG -->
                <svg class="header-stars">
                    <circle cx="50" cy="20" r="3" opacity="0.8"/>
                    <circle cx="120" cy="35" r="2" opacity="0.7"/>
                    <circle cx="250" cy="15" r="4" opacity="0.9"/>
                    <circle cx="350" cy="40" r="3" opacity="0.6"/>
                    <circle cx="500" cy="25" r="2" opacity="0.7"/>
                </svg>

                <!-- Logo centered above name -->
                @if(isset($hotelLogo) && $hotelLogo)
                    <div class="logo-container">
                        <img src="{{ $hotelLogo }}" alt="StayNest Logo" class="logo">
                    </div>
                @endif

                <h1 class="brand-name">StayNest</h1>
                <p class="brand-subtitle">Email Verification</p>
            </div>

            <!-- Content -->
            <div class="content">
                <p class="greeting">Hello {{ $name }}!</p>

                <p class="message">
                    Thank you for choosing <span class="brand-highlight">StayNest</span>! To complete your registration and secure your account, 
                    please verify your email address using the OTP code below.
                </p>

                <div class="otp-container">
                    <div class="otp-code">{{ $otp }}</div>
                </div>

                <div class="expiry-notice">
                    <h3>Important Notice</h3>
                    <div>
                        This OTP code will expire in <strong>{{ $expiresIn }} minutes</strong>. 
                        Please use it before it expires to ensure your account security.
                    </div>
                </div>

                <div class="instructions">
                    <div class="instruction-item">
                        <div class="instruction-title">Enter the OTP Code</div>
                        <div class="instruction-description">
                            Copy the 6-digit code above and enter it in the verification page on our website or app.
                        </div>
                    </div>
                    
                    <div class="instruction-item">
                        <div class="instruction-title">Complete Verification</div>
                        <div class="instruction-description">
                            Click the verify button to complete your email verification process.
                        </div>
                    </div>
                    
                    <div class="instruction-item">
                        <div class="instruction-title">Start Exploring</div>
                        <div class="instruction-description">
                            Once verified, you'll have full access to book stays, manage reservations, and enjoy exclusive member benefits.
                        </div>
                    </div>
                </div>

                <div class="security-note">
                    <strong>Security Tip:</strong> Never share your OTP code with anyone. 
                    StayNest will never ask for your password or OTP via email, phone, or text.
                </div>

                <div class="divider"></div>

                <p class="message" style="text-align: center; font-size: 14px; color: #888; line-height: 1.5;">
                    If you didn't request this verification code, please ignore this email or 
                    contact our support team if you have concerns about your account security.
                </p>
            </div>

            <!-- Footer -->
            <div class="footer">
                @if(isset($hotelLogo) && $hotelLogo)
                    <img src="{{ $hotelLogo }}" alt="StayNest Logo" class="footer-logo">
                @else
                    <div style="display: inline-block; vertical-align: middle;">
                        <strong style="color: #d95a2b; font-size: 24px; display: block;">StayNest</strong>
                    </div>
                @endif

                <p class="footer-info"><strong>StayNest Hotel Team</strong></p>
                <p class="footer-info">Need help? Contact our support team at <a href="mailto:support@StayNest.com" class="footer-link">support@StayNest.com</a></p>
                <p class="footer-info">© {{ date('Y') }} StayNest. All rights reserved.</p>
            </div>
        </div>
    </div>

</body>
</html>