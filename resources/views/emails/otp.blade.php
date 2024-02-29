<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification OTP</title>
</head>
<body>
    <p>Dear {{$firstname}},</p>
    <p>Thank you for registering. Please use the following OTP to verify your email address:</p>

    <h2>{{ $otp }}</h2>

    <p>This OTP will expire in 2 minutes. If you did not create an account, no further action is required.</p>

    <p>Best regards,<br>Your Application Team</p>
</body>
</html>
