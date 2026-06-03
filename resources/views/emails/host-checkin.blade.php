<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Khách đã check-in {{ $visit->code }}</title>
</head>
<body style="margin:0;padding:0;background:#f4f8fc;color:#0b1f3a;font-family:Arial,sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f4f8fc;padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:560px;background:#ffffff;border:1px solid #d8e5f2;border-radius:18px;overflow:hidden;">
                    <tr>
                        <td style="padding:22px 24px;border-bottom:1px solid #edf3fb;">
                            <div style="font-size:13px;font-weight:700;color:#146bd7;text-transform:uppercase;">VMS KIOSK</div>
                            <h1 style="margin:8px 0 0;font-size:23px;line-height:1.25;color:#0b1f3a;">Khách đã check-in thành công</h1>
                            <p style="margin:8px 0 0;color:#536b88;font-size:14px;line-height:1.5;">
                                Khách của bạn đã tới công ty. Vui lòng ra khu vực lễ tân để tiếp khách.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:22px 24px;">
                            <div style="padding:14px 16px;border-radius:14px;background:#edf5ff;color:#146bd7;font-size:22px;font-weight:800;letter-spacing:.5px;text-align:center;">
                                {{ $visit->code }}
                            </div>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin-top:16px;border-collapse:collapse;font-size:14px;">
                                <tr><td style="padding:9px 0;color:#6f88a4;">Khách</td><td align="right" style="padding:9px 0;font-weight:700;">{{ $visit->visitor?->full_name ?? '-' }}</td></tr>
                                <tr><td style="padding:9px 0;color:#6f88a4;border-top:1px solid #edf3fb;">Công ty</td><td align="right" style="padding:9px 0;font-weight:700;border-top:1px solid #edf3fb;">{{ $visit->visitor?->company ?? '-' }}</td></tr>
                                <tr><td style="padding:9px 0;color:#6f88a4;border-top:1px solid #edf3fb;">Số điện thoại</td><td align="right" style="padding:9px 0;font-weight:700;border-top:1px solid #edf3fb;">{{ $visit->visitor?->phone ?? '-' }}</td></tr>
                                <tr><td style="padding:9px 0;color:#6f88a4;border-top:1px solid #edf3fb;">Mục đích</td><td align="right" style="padding:9px 0;font-weight:700;border-top:1px solid #edf3fb;">{{ $visit->purpose ?? '-' }}</td></tr>
                                <tr><td style="padding:9px 0;color:#6f88a4;border-top:1px solid #edf3fb;">Check-in lúc</td><td align="right" style="padding:9px 0;font-weight:700;border-top:1px solid #edf3fb;">{{ $visit->actual_checkin_at?->format('d/m/Y H:i') ?? now()->format('d/m/Y H:i') }}</td></tr>
                                <tr><td style="padding:9px 0;color:#6f88a4;border-top:1px solid #edf3fb;">Khu vực</td><td align="right" style="padding:9px 0;font-weight:700;border-top:1px solid #edf3fb;">{{ $visit->access_zone ?? '-' }}</td></tr>
                            </table>

                            <a href="{{ $visitUrl }}" style="display:block;margin-top:18px;padding:13px 16px;border-radius:12px;background:#146bd7;color:#ffffff;text-align:center;text-decoration:none;font-weight:700;">Xem chi tiết lịch hẹn</a>
                            <p style="margin:16px 0 0;color:#7a93b0;font-size:12px;line-height:1.5;">
                                Email này được gửi tự động khi khách hoàn tất check-in tại kiosk hoặc quầy lễ tân.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
