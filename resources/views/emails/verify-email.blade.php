<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Email Verification</title>
</head>
<body>
    <h1>Email Verification</h1>
    <h2>Hi {{ $user->name }},</h2>
    <p>Click the link below to verify your email:</p>
    <p><a href="{{ $verificationUrl }}">Verify Email</a></p>
    <p>If you did not register, ignore this email.</p>
    <p>@ {{ date('Y') }} {{ config('app.name') }}</p>
</body>
</html>
