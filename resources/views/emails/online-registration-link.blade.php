<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đăng ký khách online</title>
</head>
<body style="margin:0;background:#f4f6f8;font-family:Arial,sans-serif;color:#172033;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f4f6f8;padding:32px 16px;">
        <tr><td align="center">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:580px;background:#ffffff;border:1px solid #e5e7eb;border-radius:18px;overflow:hidden;">
                <tr><td style="height:8px;background:#d40511;"></td></tr>
                <tr><td style="padding:32px;">
                    <p style="margin:0 0 10px;color:#d40511;font-size:13px;font-weight:700;text-transform:uppercase;">{{ $mailBrandName }}</p>
                    <h1 style="margin:0 0 14px;font-size:25px;line-height:1.25;">Đăng ký thông tin trước khi đến</h1>
                    <p style="margin:0 0 24px;color:#64748b;font-size:15px;line-height:1.65;">Xin chào, vui lòng mở liên kết bên dưới và điền thông tin đăng ký khách. Yêu cầu của bạn sẽ được gửi đến bộ phận phụ trách để phê duyệt.</p>
                    <p style="margin:0 0 24px;text-align:center;">
                        <a href="{{ $registrationUrl }}" style="display:inline-block;padding:14px 24px;border-radius:10px;background:#d40511;color:#ffffff;font-size:15px;font-weight:700;text-decoration:none;">Mở trang đăng ký</a>
                    </p>
                    <p style="margin:0;color:#64748b;font-size:13px;line-height:1.55;">Nếu nút không mở được, hãy sao chép liên kết sau vào trình duyệt:<br><a href="{{ $registrationUrl }}" style="color:#b91c1c;word-break:break-all;">{{ $registrationUrl }}</a></p>
                </td></tr>
            </table>
        </td></tr>
    </table>
</body>
</html>