<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Maison Gazette — Two-Step Verification Code</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #0b0f12; color: #f1f5f9; margin: 0; padding: 40px 20px; }
        .email-container { max-width: 520px; margin: 0 auto; background-color: #141a1f; border: 1px solid rgba(201, 169, 110, 0.25); border-radius: 16px; padding: 36px; box-shadow: 0 20px 40px rgba(0,0,0,0.4); }
        .brand-monogram { width: 44px; height: 44px; background: linear-gradient(135deg, #c9a96e, #856d3d); color: #0b0f12; font-weight: 900; font-size: 20px; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-bottom: 24px; text-align: center; line-height: 44px; }
        h1 { font-size: 22px; color: #f8fafc; margin-top: 0; margin-bottom: 12px; font-weight: 700; letter-spacing: -0.02em; }
        p { color: #94a3b8; font-size: 14px; line-height: 1.6; margin-bottom: 24px; }
        .otp-box { background: rgba(201, 169, 110, 0.08); border: 1px dashed #c9a96e; border-radius: 12px; padding: 20px; text-align: center; margin-bottom: 24px; }
        .otp-code { font-size: 32px; font-weight: 900; color: #c9a96e; letter-spacing: 0.35em; font-family: monospace; }
        .meta-notice { font-size: 12px; color: #64748b; line-height: 1.5; border-top: 1px solid rgba(255,255,255,0.08); padding-top: 20px; margin-top: 28px; }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="brand-monogram">X</div>
        <h1>Two-Step Verification</h1>
        <p>Greetings {{ $user->name }},<br>A request has been initiated to authenticate your administrative session for the <strong>XIVIG Maison Gazette Atelier</strong>. Please use the single-use cryptographic security code below:</p>

        <div class="otp-box">
            <div class="otp-code">{{ $otp }}</div>
        </div>

        <p style="font-size: 13px; color: #cbd5e1;">This single-use verification code remains valid for <strong>10 minutes</strong>. If you did not initiate this administrative login attempt, please notify the security team immediately and change your password.</p>

        <div class="meta-notice">
            &copy; {{ date('Y') }} Maison XIVIG. Haute Couture &amp; Horological Gazette Administration Suite.<br>
            Dispatched securely via Cryptographic Verification Authority.
        </div>
    </div>
</body>
</html>
