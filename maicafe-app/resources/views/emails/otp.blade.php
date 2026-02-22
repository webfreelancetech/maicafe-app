<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $type === 'registration' ? 'Verify Your Email' : 'Password Reset OTP' }} — Mai Cafe</title>
    <style>
        body { margin: 0; padding: 0; background-color: #f4f4f5; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }
        .wrapper { max-width: 560px; margin: 40px auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08); }
        .header { background: #1a1a2e; padding: 32px 40px; text-align: center; }
        .header h1 { color: #f59e0b; margin: 0; font-size: 24px; font-weight: 700; letter-spacing: 1px; }
        .header p { color: #94a3b8; margin: 4px 0 0; font-size: 13px; }
        .body { padding: 40px; }
        .greeting { font-size: 16px; color: #374151; margin-bottom: 16px; }
        .message { font-size: 14px; color: #6b7280; line-height: 1.7; margin-bottom: 32px; }
        .otp-box { background: #f9fafb; border: 2px dashed #e5e7eb; border-radius: 12px; padding: 28px; text-align: center; margin-bottom: 32px; }
        .otp-label { font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 1.5px; color: #9ca3af; margin-bottom: 12px; }
        .otp-code { font-size: 48px; font-weight: 800; letter-spacing: 12px; color: #1a1a2e; font-family: 'Courier New', monospace; }
        .otp-expiry { font-size: 12px; color: #f59e0b; margin-top: 12px; font-weight: 600; }
        .warning { background: #fffbeb; border: 1px solid #fde68a; border-radius: 8px; padding: 14px 16px; font-size: 13px; color: #92400e; margin-bottom: 28px; line-height: 1.6; }
        .footer { background: #f9fafb; padding: 24px 40px; text-align: center; border-top: 1px solid #e5e7eb; }
        .footer p { font-size: 12px; color: #9ca3af; margin: 4px 0; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <h1>Mai Cafe</h1>
        <p>{{ $type === 'registration' ? 'Email Verification' : 'Password Reset' }}</p>
    </div>

    <div class="body">
        <p class="greeting">Hi {{ $recipientName }},</p>

        @if($type === 'registration')
        <p class="message">
            Thank you for registering with <strong>Mai Cafe</strong>! To complete your account setup,
            please use the one-time password (OTP) below to verify your email address.
        </p>
        @else
        <p class="message">
            We received a request to reset your <strong>Mai Cafe</strong> account password.
            Use the OTP below to proceed. If you did not request this, you can safely ignore this email.
        </p>
        @endif

        <div class="otp-box">
            <div class="otp-label">Your OTP Code</div>
            <div class="otp-code">{{ $otp }}</div>
            <div class="otp-expiry"><i>⏱</i> Expires in 10 minutes</div>
        </div>

        <div class="warning">
            <strong>Security Notice:</strong> Never share this OTP with anyone.
            Mai Cafe staff will never ask for your OTP. This code is valid for a single use only.
        </div>
    </div>

    <div class="footer">
        <p>&copy; {{ date('Y') }} Mai Cafe. All rights reserved.</p>
        <p>This is an automated email — please do not reply.</p>
    </div>
</div>
</body>
</html>
