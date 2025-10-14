<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reset Your Password - StayNest</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Inter', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #fefefe;
            margin: 0;
            padding: 0;
        }
        
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            border: 1px solid #f0f0f0;
        }
        
        .header {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            padding: 30px 20px;
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
            background-image: url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI2MCIgaGVpZ2h0PSI2MCI+CiAgPHBhdGggZD0iTTAgMGg2MHY2MEgweiIgZmlsbD0ibm9uZSIvPgogIDxwYXRoIGQ9Ik0zMCAzMG0tMTUgMGE1IDUgMCA1IDAgMTAgMGE1IDUgMCA1IDAtMTAgMCIgc3Ryb2tlPSJyZ2JhKDI0NSwgMTU4LCAxMSwgMC4xKSIgc3Ryb2tlLXdpZHRoPSIxLjUiIGZpbGw9Im5vbmUiLz4KPC9zdmc+Cg==');
            opacity: 0.3;
            pointer-events: none;
        }
        
        .header h1 {
            color: #fff;
            margin: 0;
            font-size: 28px;
            font-weight: 700;
            letter-spacing: -0.5px;
            position: relative;
            z-index: 1;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }
        
        .content {
            padding: 40px 30px;
            background-color: #fff;
        }
        
        .logo-container {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }
        
        .hotel-name {
            font-size: 24px;
            font-weight: 700;
            background: linear-gradient(135deg, #92400e 0%, #b45309 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .greeting {
            font-size: 20px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .message {
            color: #4b5563;
            margin-bottom: 30px;
            font-size: 15px;
            text-align: center;
            line-height: 1.7;
        }
        
        .button-container {
            text-align: center;
            margin: 40px 0;
        }
        
        .button {
            display: inline-block;
            padding: 16px 40px;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: #fff !important;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            font-size: 16px;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }
        
        .button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(245, 158, 11, 0.4);
        }
        
        .expiry-notice {
            background-color: #fffbeb;
            border-left: 4px solid #f59e0b;
            padding: 15px 20px;
            margin: 30px 0;
            border-radius: 0 8px 8px 0;
            font-size: 14px;
            color: #92400e;
        }
        
        .security-note {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            padding: 15px;
            margin: 25px 0;
            border-radius: 8px;
            font-size: 13px;
            color: #6b7280;
            text-align: center;
        }
        
        .steps {
            margin: 30px 0;
        }
        
        .step {
            display: flex;
            align-items: flex-start;
            margin-bottom: 20px;
        }
        
        .step-number {
            background: #f59e0b;
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 600;
            margin-right: 15px;
            flex-shrink: 0;
        }
        
        .step-content {
            flex: 1;
        }
        
        .step-title {
            font-weight: 600;
            color: #374151;
            margin-bottom: 5px;
        }
        
        .step-description {
            color: #6b7280;
            font-size: 14px;
        }
        
        .footer {
            text-align: center;
            color: #6b7280;
            font-size: 14px;
            padding: 25px 30px;
            background: #f9fafb;
            border-top: 1px solid #f3f4f6;
        }
        
        .support-info {
            margin-top: 15px;
            font-size: 13px;
            color: #9ca3af;
        }
        
        .divider {
            height: 1px;
            background: linear-gradient(to right, transparent, #e5e7eb, transparent);
            margin: 30px 0;
        }
        
        .url-container {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            padding: 15px;
            margin: 25px 0;
            border-radius: 8px;
            font-size: 13px;
            color: #6b7280;
            text-align: center;
            word-break: break-all;
        }
        
        .url-link {
            color: #f59e0b;
            font-weight: 500;
        }
        
        @media (max-width: 480px) {
            .email-container {
                margin: 10px;
            }
            
            .content {
                padding: 30px 20px;
            }
            
            .header {
                padding: 25px 15px;
            }
            
            .header h1 {
                font-size: 24px;
            }
            
            .button {
                padding: 14px 32px;
                font-size: 15px;
            }
            
            .greeting {
                font-size: 18px;
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
                You are receiving this email because we received a password reset request for your StayNest account.
            </p>
            
            <div class="button-container">
                <a href="{{ $resetUrl }}" class="button">Reset Password</a>
            </div>
            
            <div class="expiry-notice">
                ⏰ This password reset link will expire in <strong>{{ $expires }} minutes</strong>. 
                Please use it before it expires.
            </div>
            
            <div class="steps">
                <div class="step">
                    <div class="step-number">1</div>
                    <div class="step-content">
                        <div class="step-title">Click the Reset Button</div>
                        <div class="step-description">
                            Use the "Reset Password" button above to securely reset your password.
                        </div>
                    </div>
                </div>
                
                <div class="step">
                    <div class="step-number">2</div>
                    <div class="step-content">
                        <div class="step-title">Create New Password</div>
                        <div class="step-description">
                            Choose a strong, unique password that you haven't used before.
                        </div>
                    </div>
                </div>
                
                <div class="step">
                    <div class="step-number">3</div>
                    <div class="step-content">
                        <div class="step-title">Access Your Account</div>
                        <div class="step-description">
                            Once reset, you can log in to your account with your new password.
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="url-container">
                If you're having trouble clicking the button, copy and paste this URL into your browser:<br>
                <span class="url-link">{{ $resetUrl }}</span>
            </div>
            
            <div class="security-note">
                🔒 <strong>Security Tip:</strong> Never share your password reset link with anyone. 
                StayNest will never ask for your password via email, phone, or text.
            </div>
            
            <div class="divider"></div>
            
            <p class="message" style="font-size: 14px; color: #6b7280;">
                If you didn't request a password reset, please ignore this email or 
                contact our support team if you have concerns about your account security.
            </p>
        </div>
        
        <div class="footer">
            <p><strong>StayNest Hotel Team</strong></p>
            <div class="support-info">
                <p>Need help? Contact our support team at support@StayNest.com</p>
                <p>© {{ date('Y') }} StayNest. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>