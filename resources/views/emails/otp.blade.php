<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification Code</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8fafc; line-height: 1.6; }
        .container { max-width: 500px; margin: 0 auto; background: white; }
        .header { background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); padding: 2rem; text-align: center; }
        .header h1 { color: white; font-size: 1.25rem; margin-bottom: 0.5rem; }
        .header p { color: rgba(255,255,255,0.9); font-size: 0.875rem; }
        .icon { font-size: 3rem; margin-bottom: 1rem; }
        .content { padding: 2rem; text-align: center; }
        .greeting { font-size: 1rem; color: #1e293b; margin-bottom: 1rem; }
        .otp-box { background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); border: 2px dashed #3b82f6; border-radius: 16px; padding: 1.5rem; margin: 1.5rem 0; }
        .otp-code { font-size: 2.5rem; font-weight: 700; color: #1e40af; letter-spacing: 8px; font-family: 'Courier New', monospace; }
        .otp-label { font-size: 0.75rem; color: #64748b; text-transform: uppercase; letter-spacing: 0.1em; margin-top: 0.5rem; }
        .warning { background: #fef3c7; border-radius: 8px; padding: 1rem; margin: 1rem 0; font-size: 0.875rem; color: #92400e; }
        .warning strong { color: #d97706; }
        .footer { background: #f8fafc; padding: 1.5rem; text-align: center; color: #64748b; font-size: 0.75rem; border-top: 1px solid #e2e8f0; }
        .expires { font-size: 0.875rem; color: #ef4444; font-weight: 600; margin-top: 0.5rem; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="icon">🔐</div>
            <h1>Two-Factor Authentication</h1>
            <p>Your verification code is ready</p>
        </div>
        
        <div class="content">
            <p class="greeting">Hi {{ $userName }},</p>
            <p>Use the code below to complete your login:</p>
            
            <div class="otp-box">
                <div class="otp-code">{{ $otp }}</div>
                <div class="otp-label">Verification Code</div>
            </div>
            
            <p class="expires">⏱️ Expires in 5 minutes</p>
            
            <div class="warning">
                <strong>Security Notice:</strong> Never share this code with anyone. We will never ask for your code via phone or message.
            </div>
            
            <p style="color: #64748b; font-size: 0.875rem; margin-top: 1rem;">
                If you didn't request this code, please ignore this email or contact support.
            </p>
        </div>
        
        <div class="footer">
            <p>This is an automated security message from TARUMT FBS.</p>
            <p>© {{ date('Y') }} TARUMT Facility Booking System</p>
        </div>
    </div>
</body>
</html>
