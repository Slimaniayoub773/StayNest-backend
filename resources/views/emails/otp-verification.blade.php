<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Verify Your Email - StayNest</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Inter', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #fffbeb;
            margin: 0;
            padding: 20px;
        }
        
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(146, 64, 14, 0.1);
            border: 1px solid #fed7aa;
            background-color: #ffffff;
        }
        
        .header {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            padding: 35px 20px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI2MCIgaGVpZ2h0PSI2MCI+CiAgPHBhdGggZD0iTTAgMGg2MHY2MEgweiIgZmlsbD0ibm9uZSIvPgogIDxwYXRoIGQ9Ik0zMCAzMG0tMTUgMGE1IDUgMCA1IDAgMTAgMGE1IDUgMCA1IDAtMTAgMCIgc3Ryb2tlPSJyZ2JhKDI0NSwgMTU4LCAxMSwgMC4xNSkiIHN0cm9rZS13aWR0aD0iMS41IiBmaWxsPSJub25lIi8+Cjwvc3ZnPgo=');
            opacity: 0.4;
            pointer-events: none;
        }
        
        .header h1 {
            color: #fff;
            margin: 0;
            font-size: 32px;
            font-weight: 700;
            letter-spacing: -0.5px;
            position: relative;
            z-index: 1;
            text-shadow: 0 2px 4px rgba(146, 64, 14, 0.2);
        }
        
        .logo-container {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
        }
        
        .hotel-name {
            font-size: 24px;
            font-weight: 700;
            background: linear-gradient(135deg, #92400e 0%, #b45309 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .content {
            padding: 40px 35px;
            background-color: #fff;
        }
        
        .greeting {
            font-size: 22px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .message {
            color: #4b5563;
            margin-bottom: 30px;
            font-size: 16px;
            text-align: center;
            line-height: 1.7;
        }
        
        .otp-container {
            text-align: center;
            margin: 40px 0;
        }
        
        .otp-code {
            display: inline-block;
            font-size: 42px;
            font-weight: 700;
            color: #f59e0b;
            letter-spacing: 10px;
            padding: 25px 40px;
            background: #fffbeb;
            border: 2px solid #f59e0b;
            border-radius: 16px;
            margin: 20px 0;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.15);
            position: relative;
            overflow: hidden;
        }
        
        .otp-code::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #f59e0b, #d97706, #f59e0b);
            background-size: 200% 100%;
            animation: shimmer 3s infinite linear;
        }
        
        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        
        .expiry-notice {
            background-color: #fffbeb;
            border-left: 4px solid #f59e0b;
            padding: 18px 22px;
            margin: 30px 0;
            border-radius: 0 12px 12px 0;
            font-size: 15px;
            color: #92400e;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .expiry-notice::before {
            content: '⏰';
            font-size: 18px;
        }
        
        .security-note {
            background-color: #fef3c7;
            border: 1px solid #fcd34d;
            padding: 18px;
            margin: 25px 0;
            border-radius: 12px;
            font-size: 14px;
            color: #92400e;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .security-note::before {
            content: '🔒';
            font-size: 16px;
        }
        
        .steps {
            margin: 40px 0;
        }
        
        .step {
            display: flex;
            align-items: flex-start;
            margin-bottom: 25px;
            padding: 20px;
            border-radius: 12px;
            transition: all 0.3s ease;
            background: #fffbeb;
        }
        
        .step:hover {
            background: #fef3c7;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.1);
        }
        
        .step-number {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 600;
            margin-right: 18px;
            flex-shrink: 0;
            box-shadow: 0 2px 6px rgba(245, 158, 11, 0.3);
        }
        
        .step-content {
            flex: 1;
        }
        
        .step-title {
            font-weight: 600;
            color: #92400e;
            margin-bottom: 6px;
            font-size: 16px;
        }
        
        .step-description {
            color: #b45309;
            font-size: 14px;
            line-height: 1.6;
        }
        
        .footer {
            text-align: center;
            color: #6b7280;
            font-size: 14px;
            padding: 30px 35px;
            background: #fffbeb;
            border-top: 1px solid #fed7aa;
        }
        
        .support-info {
            margin-top: 18px;
            font-size: 13px;
            color: #9ca3af;
        }
        
        .support-info a {
            color: #d97706;
            text-decoration: none;
        }
        
        .support-info a:hover {
            text-decoration: underline;
        }
        
        .divider {
            height: 1px;
            background: linear-gradient(to right, transparent, #fcd34d, transparent);
            margin: 30px 0;
        }
        
        .brand-highlight {
            color: #d97706;
            font-weight: 600;
        }
        
        @media (max-width: 480px) {
            body {
                padding: 10px;
                background-color: #fff;
            }
            
            .email-container {
                margin: 0;
                border-radius: 12px;
            }
            
            .content {
                padding: 30px 20px;
            }
            
            .header {
                padding: 25px 15px;
            }
            
            .header h1 {
                font-size: 26px;
            }
            
            .otp-code {
                font-size: 32px;
                letter-spacing: 8px;
                padding: 20px 25px;
            }
            
            .greeting {
                font-size: 20px;
            }
            
            .step {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>StayNest</h1>
        </div>
        
        <div class="content">
            <h2 class="greeting">Hello {{ $name }}!</h2>
            
            <p class="message">
                Thank you for choosing <span class="brand-highlight">StayNest</span>! To complete your registration and secure your account, 
                please verify your email address using the OTP code below.
            </p>
            
            <div class="otp-container">
                <div class="otp-code">{{ $otp }}</div>
            </div>
            
            <div class="expiry-notice">
                This OTP code will expire in <strong>{{ $expiresIn }} minutes</strong>. 
                Please use it before it expires to ensure your account security.
            </div>
            
            <div class="steps">
                <div class="step">
                    <div class="step-number">1</div>
                    <div class="step-content">
                        <div class="step-title">Enter the OTP Code</div>
                        <div class="step-description">
                            Copy the 6-digit code above and enter it in the verification page on our website or app.
                        </div>
                    </div>
                </div>
                
                <div class="step">
                    <div class="step-number">2</div>
                    <div class="step-content">
                        <div class="step-title">Complete Verification</div>
                        <div class="step-description">
                            Click the verify button to complete your email verification process.
                        </div>
                    </div>
                </div>
                
                <div class="step">
                    <div class="step-number">3</div>
                    <div class="step-content">
                        <div class="step-title">Start Exploring</div>
                        <div class="step-description">
                            Once verified, you'll have full access to book stays, manage reservations, and enjoy exclusive member benefits.
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="security-note">
                <strong>Security Tip:</strong> Never share your OTP code with anyone. 
                StayNest will never ask for your password or OTP via email, phone, or text.
            </div>
            
            <div class="divider"></div>
            
            <p class="message" style="font-size: 14px; color: #6b7280;">
                If you didn't request this verification code, please ignore this email or 
                contact our support team if you have concerns about your account security.
            </p>
        </div>
        
        <div class="footer">
            <p><strong>StayNest Hotel Team</strong></p>
            <div class="support-info">
                <p>Need help? Contact our support team at <a href="mailto:support@StayNest.com">support@StayNest.com</a></p>
                <p>© {{ date('Y') }} StayNest. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>