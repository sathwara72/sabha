<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Verify your email – Sabha</title>
</head>
<body style="margin:0;padding:0;background:#f1f5f9;font-family:Arial,sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9;padding:40px 0;">
        <tr>
            <td align="center">
                <table width="560" cellpadding="0" cellspacing="0"
                       style="background:#ffffff;border-radius:16px;border:1px solid #e2e8f0;overflow:hidden;max-width:560px;width:100%;">

                    {{-- Header --}}
                    <tr>
                        <td align="center" style="background:linear-gradient(135deg,#1e3a8a 0%,#2563eb 100%);padding:32px 40px 28px;">
                            <p style="margin:0;font-size:26px;font-weight:800;color:#ffffff;letter-spacing:-0.5px;">SABHA</p>
                            <p style="margin:6px 0 0;font-size:13px;color:#bfdbfe;letter-spacing:0.05em;text-transform:uppercase;">Community for Businesses</p>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding:36px 40px 32px;">
                            <p style="margin:0 0 8px;font-size:20px;font-weight:700;color:#0f172a;">Hello, {{ $userName }}!</p>
                            <p style="margin:0 0 24px;font-size:15px;color:#475569;line-height:1.6;">
                                You requested to create a Sabha account. Use the verification code below to complete your registration.
                            </p>

                            {{-- OTP Box --}}
                            <div style="background:#f8fafc;border:2px dashed #2563eb;border-radius:12px;padding:24px;text-align:center;margin-bottom:28px;">
                                <p style="margin:0 0 6px;font-size:12px;color:#64748b;text-transform:uppercase;letter-spacing:0.08em;font-weight:600;">Your Verification Code</p>
                                <p style="margin:0;font-size:40px;font-weight:800;color:#1e3a8a;letter-spacing:0.3em;font-family:monospace;">{{ $otp }}</p>
                            </div>

                            <p style="margin:0 0 6px;font-size:14px;color:#64748b;line-height:1.6;">
                                This code is valid for <strong>15 minutes</strong>. Do not share it with anyone.
                            </p>
                            <p style="margin:0;font-size:13px;color:#94a3b8;line-height:1.5;">
                                If you did not request this, you can safely ignore this email.
                            </p>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background:#f8fafc;border-top:1px solid #e2e8f0;padding:20px 40px;text-align:center;">
                            <p style="margin:0;font-size:12px;color:#94a3b8;">
                                &copy; {{ date('Y') }} Sabha &mdash; Community for Businesses. All rights reserved.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
