<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $subject }}</title>
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
            margin: 0 0 8px 0;
            font-size: 28px;
            font-weight: 700;
            letter-spacing: -0.5px;
            position: relative;
            z-index: 1;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }
        
        .header p {
            color: rgba(255, 255, 255, 0.9);
            margin: 0;
            font-size: 16px;
            position: relative;
            z-index: 1;
        }
        
        .content {
            padding: 30px;
            background-color: #fff;
        }
        
        .greeting {
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 20px;
        }
        
        .message {
            color: #4b5563;
            margin-bottom: 25px;
            font-size: 15px;
        }
        
        .response-container {
            background: #fffbeb;
            border-left: 4px solid #f59e0b;
            padding: 20px;
            margin: 25px 0;
            border-radius: 0 8px 8px 0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }
        
        .response-title {
            font-weight: 600;
            color: #92400e;
            margin-bottom: 10px;
            font-size: 16px;
        }
        
        .original-message {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            padding: 18px;
            margin: 25px 0;
            border-radius: 8px;
        }
        
        .original-title {
            font-weight: 600;
            color: #374151;
            margin-bottom: 10px;
            font-size: 15px;
        }
        
        .footer {
            text-align: center;
            color: #6b7280;
            font-size: 14px;
            padding: 25px 30px;
            background: #f9fafb;
            border-top: 1px solid #f3f4f6;
        }
        
        .signature {
            font-weight: 600;
            color: #374151;
            margin-bottom: 15px;
        }
        
        .contact-info {
            margin-top: 15px;
            font-size: 13px;
            color: #9ca3af;
        }
        
        .logo-container {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
        }
        
        .hotel-name {
            font-size: 20px;
            font-weight: 700;
            background: linear-gradient(135deg, #92400e 0%, #b45309 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .divider {
            height: 1px;
            background: linear-gradient(to right, transparent, #e5e7eb, transparent);
            margin: 25px 0;
        }
        
        @media (max-width: 480px) {
            .email-container {
                margin: 10px;
            }
            
            .content {
                padding: 20px;
            }
            
            .header {
                padding: 25px 15px;
            }
            
            .header h1 {
                font-size: 24px;
            }
            
            .response-container, .original-message {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>StayNest</h1>
            <p>Response to Your Inquiry</p>
        </div>
            
            <p class="greeting">Dear {{ $contactMessage->name }},</p>
            
            <p class="message">Thank you for contacting StayNest. Here is our response to your inquiry:</p>
            
            <div class="response-container">
                <div class="response-title">Our Response:</div>
                {!! nl2br(e($responseMessage)) !!}
            </div>
            
            <div class="original-message">
                <div class="original-title">Your original message:</div>
                {!! nl2br(e($contactMessage->message)) !!}
            </div>
            
            <div class="divider"></div>
            
            <p class="message">If you have any further questions, please don't hesitate to contact us. We're here to help!</p>
        </div>
        
        <div class="footer">
            <p class="signature">Best regards,<br>The StayNest Team</p>
            <div class="contact-info">
                <p>Phone: +212 636847568 | Email: support@StayNest.com</p>
                <p>© {{ date('Y') }} StayNest. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>