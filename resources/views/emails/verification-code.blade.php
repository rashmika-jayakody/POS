<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification Code</title>
</head>
<body style="font-family: 'Inter', Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(135deg, #0A1A3D 0%, #4A9EFF 100%); padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
        <h1 style="color: white; margin: 0; font-size: 24px;">PosHere</h1>
    </div>
    
    <div style="background: #ffffff; padding: 40px; border: 1px solid #e2e8f0; border-top: none; border-radius: 0 0 10px 10px;">
        <h2 style="color: #0A1A3D; margin-top: 0;">
            @if($type === 'staff')
                Staff Account Verification
            @else
                Email Verification
            @endif
        </h2>
        
        <p style="color: #64748b; font-size: 16px;">
            @if($type === 'staff')
                You have been added as a staff member. Please use the verification code below to verify your email address:
            @else
                Thank you for registering! Please use the verification code below to verify your email address:
            @endif
        </p>
        
        <div style="background: #F0F8FF; border: 2px solid #4A9EFF; border-radius: 8px; padding: 20px; text-align: center; margin: 30px 0;">
            <div style="font-size: 32px; font-weight: 700; color: #0A1A3D; letter-spacing: 8px; font-family: 'Courier New', monospace;">
                {{ $code }}
            </div>
        </div>
        
        <p style="color: #64748b; font-size: 14px; margin-top: 30px;">
            This code will expire in <strong>15 minutes</strong>. If you didn't request this code, please ignore this email.
        </p>
        
        <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #e2e8f0;">
            <p style="color: #94a3b8; font-size: 12px; margin: 0;">
                This is an automated message from PosHere. Please do not reply to this email.
            </p>
        </div>
    </div>
</body>
</html>
