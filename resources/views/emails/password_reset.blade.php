<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Maison Gazette — Administrative Passkey Reset</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #0b0f12; color: #f1f5f9; margin: 0; padding: 40px 20px; }
        .email-container { max-width: 520px; margin: 0 auto; background-color: #141a1f; border: 1px solid rgba(201, 169, 110, 0.25); border-radius: 16px; padding: 36px; box-shadow: 0 20px 40px rgba(0,0,0,0.4); }
        .brand-monogram { width: 44px; height: 44px; background: linear-gradient(135deg, #c9a96e, #856d3d); color: #0b0f12; font-weight: 900; font-size: 20px; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-bottom: 24px; text-align: center; line-height: 44px; }
        h1 { font-size: 22px; color: #f8fafc; margin-top: 0; margin-bottom: 12px; font-weight: 700; letter-spacing: -0.02em; }
        p { color: #94a3b8; font-size: 14px; line-height: 1.6; margin-bottom: 24px; }
        .btn-box { text-align: center; margin: 30px 0; }
        .btn { display: inline-block; background: linear-gradient(135deg, #c9a96e, #a38243); color: #0b0f12; font-weight: 700; text-decoration: none; padding: 14px 28px; border-radius: 8px; font-size: 14px; text-transform: uppercase; letter-spacing: 0.1em; }
        .meta-notice { font-size: 12px; color: #64748b; line-height: 1.5; border-top: 1px solid rgba(255,255,255,0.08); padding-top: 20px; margin-top: 28px; }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="brand-monogram">X</div>
        <h1>Passkey Reset Request</h1>
        <p>Greetings {{ $user->name }},<br>A passkey reset request has been initiated for your administrative account at <strong>XIVIG Maison Gazette Atelier</strong>. To securely establish a new administrative password, please click the button below:</p>

        <div class="btn-box">
            <a href="{{ $resetUrl }}" class="btn">Reset Passkey &rarr;</a>
        </div>

        <p style="font-size: 13px; color: #cbd5e1;">This security passkey link will expire in <strong>60 minutes</strong>. If you did not request a password reset, no further action is required; your credentials remain secure.</p>

        <div class="meta-notice">
            If you're having trouble clicking the "Reset Passkey" button, copy and paste the URL below into your web browser:<br>
            <span style="color: #94a3b8; word-break: break-all;">{{ $resetUrl }}</span><br><br>
            &copy; {{ date('Y') }} Maison XIVIG. Haute Couture &amp; Horological Gazette Administration Suite.<br>
            Dispatched securely via Administrative Credential Authority.
        </div>
    </div>
</body>
</html>
