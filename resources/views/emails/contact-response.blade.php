<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $subject ?? 'Response from StayNest' }}</title>
  <style>
    /* Reset & Basic Styles */
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      line-height: 1.6;
      color: #333;
      background-color: #f0f2f5;
      margin: 0;
      padding: 20px 0;
    }

    .email-container {
      max-width: 640px;
      margin: 0 auto;
      background-color: #ffffff;
      border-radius: 0 0 12px 12px;
      overflow: hidden;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    /* --- Header Style --- */
    .header-wrap {
      background-color: #f0f2f5;
      padding-bottom: 10px;
    }

    .header {
      /* التدرج اللوني البرتقالي */
      background: linear-gradient(135deg, #d95a2b 0%, #e86a35 100%);
      padding: 40px 20px 60px 20px;
      text-align: center;
      color: #fff;
      /* الانحناء في الأسفل */
      border-radius: 12px 12px 50% 50% / 12px 12px 30px 30px;
      position: relative;
      overflow: hidden;
      /* مهم جداً لقص الدوائر التي تخرج عن الحدود */
    }

    /* --- Decorative Circles (الدوائر الخفيفة) --- */
    .circle {
      position: absolute;
      border-radius: 50%;
      border: 1px solid rgba(255, 255, 255, 0.15);
      /* حدود بيضاء شفافة */
      z-index: 1;
      /* خلف النص */
      pointer-events: none;
    }

    /* دائرة 1: كبيرة أعلى اليسار */
    .circle-1 {
      width: 150px;
      height: 150px;
      top: -50px;
      left: -30px;
      border-width: 2px;
      /* أسمك قليلاً */
    }

    /* دائرة 2: صغيرة بجانب الأولى */
    .circle-2 {
      width: 60px;
      height: 60px;
      top: 20px;
      left: 80px;
      background-color: rgba(255, 255, 255, 0.05);
      /* تعبئة خفيفة */
    }

    /* دائرة 3: كبيرة جداً على اليمين */
    .circle-3 {
      width: 200px;
      height: 200px;
      bottom: -80px;
      right: -50px;
      opacity: 0.6;
    }

    /* دائرة 4: صغيرة عائمة في الأعلى */
    .circle-4 {
      width: 40px;
      height: 40px;
      top: 10px;
      right: 20%;
      border: 1px solid rgba(255, 255, 255, 0.1);
    }

    /* النصوص فوق الدوائر */
    .header h1,
    .header p {
      position: relative;
      z-index: 10;
      /* لضمان ظهور النص فوق الدوائر */
    }

    .header h1 {
      margin: 0;
      font-size: 36px;
      font-weight: 800;
      letter-spacing: -0.5px;
      text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .header p {
      margin: 5px 0 0 0;
      font-size: 18px;
      font-weight: 400;
      opacity: 0.95;
    }

    /* --- Content Body --- */
    .content {
      padding: 40px 35px;
      background-color: #fff;
    }

    .greeting {
      font-size: 20px;
      font-weight: 700;
      color: #222;
      margin-bottom: 20px;
    }

    .message-intro {
      color: #555;
      font-size: 15px;
      margin-bottom: 30px;
    }

    /* --- Response Box --- */
    .response-box-container {
      display: flex;
      background-color: #f3ecd9;
      /* لون البيج */
      border-radius: 8px;
      overflow: hidden;
      margin-bottom: 30px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
    }

    .response-bar {
      background-color: #ba5022;
      /* الشريط الجانبي البرتقالي الغامق */
      width: 15px;
      min-width: 15px;
    }

    .response-content {
      padding: 25px;
      flex-grow: 1;
      color: #8c5e35;
    }

    .box-title {
      font-size: 18px;
      font-weight: 700;
      margin-bottom: 12px;
      color: #ba5022;
    }

    /* --- Original Message Box --- */
    .original-box {
      background-color: #f4f4f4;
      /* رمادي فاتح */
      border: 1px solid #dcdcdc;
      padding: 25px;
      border-radius: 8px;
      margin-bottom: 30px;
      color: #555;
    }

    .original-title {
      font-size: 16px;
      font-weight: 700;
      margin-bottom: 12px;
      color: #333;
    }

    /* --- Footer --- */
    .footer {
      text-align: center;
      padding: 30px 20px;
      background-color: #f9f9f9;
      border-top: 1px solid #eee;
      color: #888;
      font-size: 13px;
    }

    .footer-logo-text {
      font-size: 24px;
      font-weight: 800;
      color: #ba5022;
      display: inline-block;
      margin-bottom: 10px;
    }

    @media (max-width: 480px) {
      .header {
        padding: 30px 15px 40px 15px;
      }

      .header h1 {
        font-size: 28px;
      }

      .content {
        padding: 25px 15px;
      }
    }
  </style>
</head>

<body>
  <div class="email-container">
    <div class="header-wrap">
      <div class="header">
        <div class="circle circle-1"></div>
        <div class="circle circle-2"></div>
        <div class="circle circle-3"></div>
        <div class="circle circle-4"></div>

        <h1>StayNest</h1>
        <p>Response to Your Inquiry</p>
      </div>
    </div>

    <div class="content">
      <p class="greeting">Dear {{ $contactMessage->name ?? 'John Smith' }}.</p>

      <p class="message-intro">
        Thank you for contacting StayNest. We appreciate you reaching out to us and have reviewed your inquiry.
      </p>

      <div class="response-box-container">
        <div class="response-bar"></div>
        <div class="response-content">
          <div class="box-title">Our Response:</div>
          {!! nl2br(e($responseMessage ?? 'Thank you for your interest in StayNest. Regarding your question about
          booking availability, we are pleased to confirm we have spaces open.')) !!}
        </div>
      </div>

      <div class="original-box">
        <div class="original-title">Your original message:</div>
        {!! nl2br(e($contactMessage->message ?? 'Hello, I am interested in booking a room for next month. Do you have
        any special offers?')) !!}
      </div>

      <p style="text-align: center; color: #666; font-size: 14px; margin-top: 30px;">
        If you have any further questions, please contact us.<br>
                We look forward to welcoming you to StayNest.
      </p>
    </div>

    <div class="footer">
      <div style="margin-bottom: 10px;">
        @if(isset($hotelLogo) && $hotelLogo)
        <img src="{{ $hotelLogo }}" alt="Logo" style="height: 35px;">
                @else
        <span class="footer-logo-text">StayNest</span>
        @endif
      </div>
      <p>Phone: +212 636847568 | Email: support@StayNest.com</p>
      <p>© {{ date('Y') }} StayNest. All rights reserved.</p>
    </div>
  </div>
</body>

</html>