# SSL/TLS Go-Live Checklist

Tài liệu này dùng cho kỹ thuật khi cài VMS trên cloud/VPS Windows, IIS, hosting hoặc server của khách.

## 1. Trước khi cấu hình SSL

- Đã có domain production của khách.
- Domain đã trỏ DNS về server chạy VMS.
- Server mở port 80 và 443.
- IIS/hosting đã có certificate hợp lệ.
- File `.env` đã cấu hình production.

## 2. Cấu hình `.env`

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.example
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
```

Sau khi sửa `.env`, chạy:

```powershell
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## 3. Cấu hình IIS

1. Mở IIS Manager.
2. Chọn website VMS.
3. Vào `Bindings`.
4. Thêm binding:
   - Type: `https`
   - Port: `443`
   - Host name: domain của khách
   - SSL certificate: certificate đã cấp
5. Đảm bảo binding HTTP port 80 có redirect sang HTTPS.

## 4. Kiểm tra sau cấu hình

| Kiểm tra | Kết quả mong đợi |
| --- | --- |
| Mở domain trên browser | Có biểu tượng khóa bảo mật |
| Truy cập `http://domain` | Tự động chuyển sang `https://domain` |
| Đăng nhập admin | Đăng nhập được, session không bị văng |
| Gửi email QR | Link trong email đúng domain HTTPS |
| Logo/upload | Ảnh hiển thị được từ máy khác |
| Kiosk/mobile | Truy cập được bằng HTTPS |

## 5. Bằng chứng nên lưu

- Ảnh màn hình browser có khóa HTTPS.
- Ảnh màn hình chi tiết certificate.
- Ảnh màn hình IIS binding HTTPS.
- Ảnh màn hình `.env` đã che mật khẩu.
- Kết quả header nếu có:

```powershell
curl -I https://your-domain.example
```

## 6. Lỗi hay gặp

| Lỗi | Nguyên nhân thường gặp | Cách xử lý |
| --- | --- | --- |
| Đăng nhập xong bị văng ra | `APP_URL` sai hoặc cookie secure không khớp HTTPS | Sửa `.env`, clear config |
| Ảnh/logo máy khác không thấy | URL public đang là localhost/IP sai | Sửa `APP_URL`, chạy lại storage link nếu cần |
| Email link sai domain | Cache cấu hình cũ | `php artisan config:clear` |
| Browser báo Not secure | Chưa có SSL hợp lệ hoặc cert sai domain | Cấp lại certificate đúng domain |

