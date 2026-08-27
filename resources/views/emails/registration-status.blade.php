<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Registration Update – Sabha</title>
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

                            @if ($type === 'step1_approved')
                                <p style="margin:0 0 20px;font-size:15px;color:#475569;line-height:1.6;">
                                    Good news — your registration application has been approved. Please log in to complete your membership by uploading your Aadhar card, PAN card, a business proof document, and your membership payment screenshot.
                                </p>
                                <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:12px;padding:16px 20px;margin-bottom:20px;">
                                    <p style="margin:0;font-size:14px;color:#166534;font-weight:600;">Next step: Log in and complete your document upload.</p>
                                </div>
                            @elseif ($type === 'step1_rejected')
                                <p style="margin:0 0 20px;font-size:15px;color:#475569;line-height:1.6;">
                                    Thank you for your interest in joining Sabha. After review, we're unable to approve your registration application at this time.
                                </p>
                                @if ($reason)
                                    <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:12px;padding:16px 20px;margin-bottom:20px;">
                                        <p style="margin:0;font-size:13px;color:#64748b;text-transform:uppercase;letter-spacing:0.05em;font-weight:600;">Reason</p>
                                        <p style="margin:6px 0 0;font-size:14px;color:#991b1b;">{{ $reason }}</p>
                                    </div>
                                @endif
                                <p style="margin:0;font-size:14px;color:#64748b;line-height:1.6;">
                                    You're welcome to submit a new application at any time.
                                </p>
                            @elseif ($type === 'step2_submitted')
                                <p style="margin:0 0 20px;font-size:15px;color:#475569;line-height:1.6;">
                                    We've received your documents and payment screenshot. Our team is reviewing them now — you'll receive another email once your membership is confirmed.
                                </p>
                            @elseif ($type === 'payment_approved')
                                <p style="margin:0 0 20px;font-size:15px;color:#475569;line-height:1.6;">
                                    Your payment has been verified and your Sabha membership is now fully active. You can log in and start using your account right away.
                                </p>
                                <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:12px;padding:16px 20px;margin-bottom:20px;">
                                    <p style="margin:0;font-size:14px;color:#166534;font-weight:600;">Welcome to the Sabha community!</p>
                                </div>
                            @elseif ($type === 'payment_rejected')
                                <p style="margin:0 0 20px;font-size:15px;color:#475569;line-height:1.6;">
                                    We reviewed your submitted documents/payment screenshot and need you to resubmit before we can activate your membership.
                                </p>
                                @if ($reason)
                                    <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:12px;padding:16px 20px;margin-bottom:20px;">
                                        <p style="margin:0;font-size:13px;color:#64748b;text-transform:uppercase;letter-spacing:0.05em;font-weight:600;">Reason</p>
                                        <p style="margin:6px 0 0;font-size:14px;color:#991b1b;">{{ $reason }}</p>
                                    </div>
                                @endif
                                <p style="margin:0;font-size:14px;color:#64748b;line-height:1.6;">
                                    Please log in to review and resubmit your documents.
                                </p>
                            @endif
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
