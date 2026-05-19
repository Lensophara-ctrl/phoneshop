<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .container {
            background: #f9f9f9;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #4CAF50;
            margin: 0;
        }
        .otp-code {
            background: #fff;
            border: 2px dashed #4CAF50;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
        }
        .otp-code h2 {
            font-size: 36px;
            letter-spacing: 8px;
            color: #4CAF50;
            margin: 0;
        }
        .info {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔐 OTP Verification</h1>
            <p>{{ ucfirst(str_replace('_', ' ', $type)) }} Request</p>
        </div>

        <p>Hello,</p>
        <p>You have requested a one-time password (OTP) for your PhoneShop account.</p>

        <div class="otp-code">
            <p style="margin: 0; font-size: 14px; color: #666;">Your OTP Code:</p>
            <h2>{{ $code }}</h2>
        </div>

        <div class="info">
            <strong>⏰ Important:</strong>
            <ul style="margin: 10px 0;">
                <li>This code is valid for <strong>{{ $validityMinutes }} minutes</strong></li>
                <li>Do not share this code with anyone</li>
                <li>If you didn't request this code, please ignore this email</li>
            </ul>
        </div>

        <p>For security reasons, please do not reply to this email.</p>

        <div class="footer">
            <p>&copy; {{ date('Y') }} PhoneShop. All rights reserved.</p>
            <p>This is an automated message, please do not reply.</p>
        </div>
    </div>
</body>
</html>
