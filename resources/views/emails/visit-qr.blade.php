<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Mã QR lịch hẹn {{ $visit->code }}</title>
</head>
<body style="margin:0;padding:0;background:#f4f8fc;color:#0b1f3a;font-family:Arial,sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f4f8fc;padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:560px;background:#ffffff;border:1px solid #d8e5f2;border-radius:18px;overflow:hidden;">
                    <tr>
                        <td style="padding:22px 24px;border-bottom:1px solid #edf3fb;">
                            <div style="font-size:13px;font-weight:700;color:#146bd7;text-transform:uppercase;">VMS KIOSK</div>
                            <h1 style="margin:8px 0 0;font-size:24px;line-height:1.25;color:#0b1f3a;">Mã QR lịch hẹn của bạn</h1>
                            <p style="margin:8px 0 0;color:#536b88;font-size:14px;line-height:1.5;">Vui lòng xuất trình mã này tại quầy lễ tân khi đến công ty.</p>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding:24px;">
                            <div style="display:inline-block;padding:14px;border:1px solid #d8e5f2;border-radius:16px;background:#ffffff;">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=220x220&margin=4&ecc=M&data={{ urlencode($visit->qr_token) }}" alt="QR {{ $visit->code }}" width="200" height="200" style="display:block;width:200px;height:200px;border:0;outline:none;text-decoration:none;">
                            </div>
                            <div style="margin-top:16px;padding:12px 16px;border-radius:12px;background:#edf5ff;color:#146bd7;font-size:26px;font-weight:800;letter-spacing:1px;">
                                {{ $visit->code }}
                            </div>
                            <p style="margin:10px 0 0;color:#536b88;font-size:13px;">Mã QR/check-in: <strong style="color:#0b1f3a;">{{ $visit->qr_token }}</strong></p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:0 24px 22px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;font-size:14px;">
                                <tr><td style="padding:8px 0;color:#6f88a4;">Khách</td><td align="right" style="padding:8px 0;font-weight:700;">{{ $visit->visitor?->full_name ?? '-' }}</td></tr>
                                <tr><td style="padding:8px 0;color:#6f88a4;border-top:1px solid #edf3fb;">Công ty</td><td align="right" style="padding:8px 0;font-weight:700;border-top:1px solid #edf3fb;">{{ $visit->visitor?->company ?? '-' }}</td></tr>
                                <tr><td style="padding:8px 0;color:#6f88a4;border-top:1px solid #edf3fb;">Người tiếp</td><td align="right" style="padding:8px 0;font-weight:700;border-top:1px solid #edf3fb;">{{ $visit->hostEmployee?->name ?? '-' }}</td></tr>
                                <tr><td style="padding:8px 0;color:#6f88a4;border-top:1px solid #edf3fb;">Giờ hẹn</td><td align="right" style="padding:8px 0;font-weight:700;border-top:1px solid #edf3fb;">{{ $visit->scheduled_at?->format('d/m/Y H:i') ?? '-' }}</td></tr>
                            </table>
                            <a href="{{ $statusUrl }}" style="display:block;margin-top:18px;padding:13px 16px;border-radius:12px;background:#146bd7;color:#ffffff;text-align:center;text-decoration:none;font-weight:700;">Tra cứu / check-in</a>
                            <p style="margin:16px 0 0;color:#7a93b0;font-size:12px;line-height:1.5;">Mã QR chỉ dùng cho lượt khách này. Vui lòng không chia sẻ mã cho người khác.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
