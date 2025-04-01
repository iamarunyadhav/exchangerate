<!DOCTYPE html>
<html>
<head>
    <title>Your Login OTP</title>
</head>
<body>
    <h2>Your One-Time Password (OTP)</h2>
    <p>Use the following OTP to login to your account:</p>

    <div style="font-size: 24px; font-weight: bold; margin: 20px 0;">
        {{ $otp }}
    </div>

    <p>This OTP is valid for 10 minutes.</p>
    <p>If you didn't request this, please ignore this email.</p>
</body>
</html>
