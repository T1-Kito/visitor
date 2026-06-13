# VMS Security Customer Pack

Ngày lập: 12/06/2026  
Dự án: Visitor Management System (VMS)  
Phạm vi: Bản cài đặt on-premise / cloud riêng cho từng khách hàng  
Loại tài liệu: Tự đánh giá bảo mật nội bộ và checklist triển khai

> Lưu ý: Tài liệu này cung cấp tổng quan về các biện pháp bảo mật, cơ chế kiểm soát truy cập, ghi nhận sự kiện và các khuyến nghị triển khai an toàn của hệ thống VMS. Các hoạt động đánh giá bảo mật độc lập (nếu được yêu cầu) sẽ được thực hiện theo phạm vi triển khai 

## 1. Tóm tắt hiện trạng

Hệ thống VMS đã có các lớp kiểm soát cơ bản cho môi trường doanh nghiệp:

- Đăng nhập quản trị và phân quyền theo vai trò.
- Các màn hình quản trị quan trọng được bảo vệ bằng middleware `auth` và `permission`.
- Form có CSRF token theo cơ chế của Laravel.
- Dữ liệu quan trọng được validate ở controller/request trước khi ghi database.
- Có audit log ghi nhận người thực hiện, hành động, thời gian và thông tin request.
- Có cấu hình email SMTP riêng theo môi trường doanh nghiệp.
- Có hỗ trợ chạy HTTPS khi triển khai domain/SSL trên IIS, hosting hoặc cloud.

Kết quả kiểm tra gần nhất trên source code hiện tại:

| Hạng mục | Kết quả |
| --- | --- |
| Composer security audit | Không có advisory bảo mật đã biết |
| Composer abandoned packages | Có 2 package abandoned thuộc nhóm dev/test, cần theo dõi khi nâng cấp PHPUnit |
| Test hotfix on-premise | `OnpremAuditHotfixTest`: 7 tests pass, 53 assertions |
| Security header IIS | Có `X-Frame-Options: SAMEORIGIN` trong `public/web.config` |
| Session HTTPS | Hỗ trợ `SESSION_SECURE_COOKIE=true` khi chạy production HTTPS |

## 2. Tài liệu tham chiếu

- OWASP Top 10 Web Application Security Risks: https://owasp.org/www-project-top-ten/
- OWASP Application Security Verification Standard (ASVS): https://owasp.org/www-project-application-security-verification-standard/

## 3. Bảng đối chiếu OWASP

| Nhóm rủi ro | Hiện trạng trong VMS | Trạng thái | Ghi chú |
| --- | --- | --- | --- |
| Broken Access Control | Có đăng nhập, role/permission, middleware bảo vệ route quản trị | Đạt cơ bản | Cần tiếp tục test phân quyền theo từng vai trò thực tế của khách |
| Cryptographic Failures | Hỗ trợ HTTPS qua IIS/hosting/cloud, session secure cookie, mật khẩu hash bằng Laravel | Đạt cơ bản | Bằng chứng SSL/TLS chỉ có sau khi triển khai domain thật |
| Injection | Sử dụng Laravel/Eloquent và validation, hạn chế query thuần | Đạt cơ bản | Nên bổ sung test input bất thường khi làm pentest |
| Insecure Design | Đã có luồng phê duyệt, check-in/check-out, audit log và cảnh báo | Đang hoàn thiện | Cần chốt ma trận nghiệp vụ với khách trước go-live |
| Security Misconfiguration | Có cấu hình production, IIS `web.config`, SMTP riêng | Cần kiểm tra khi triển khai | Bắt buộc đặt `APP_DEBUG=false` ở production |
| Vulnerable/Outdated Components | `composer audit` không thấy advisory bảo mật | Đạt cơ bản | Cần lập lịch audit dependency định kỳ |
| Identification/Auth Failures | Có login, session Laravel, CSRF | Đạt cơ bản | Nên bổ sung chính sách password/lockout nếu khách yêu cầu |
| Software/Data Integrity Failures | Migration/seeder quản lý bằng code, license file có chữ ký | Đạt cơ bản | Cần bảo vệ private key license ngoài gói cài đặt khách |
| Logging/Monitoring Failures | Đã có audit log cho sự kiện quan trọng | Đạt cơ bản | Cần thống nhất danh sách event bắt buộc log với khách |
| SSRF | Chưa thấy tính năng gọi URL tùy ý từ người dùng | Rủi ro thấp hiện tại | Vẫn cần kiểm tra lại nếu bổ sung webhook/API ngoài |

## 4. Audit log / Event log

Tính năng audit log nên được trình bày với khách theo hướng:

- Ghi nhận ai thực hiện thao tác: tên người dùng, email/tài khoản nếu có.
- Ghi nhận thao tác: tạo/sửa/xóa, duyệt, từ chối, check-in, check-out, cấu hình hệ thống.
- Ghi nhận thời điểm: timestamp theo server.
- Ghi nhận ngữ cảnh request: IP, method, URL và user agent nếu có.
- Cho phép lọc theo từ khóa, loại hành động, người thực hiện và thời gian.

Đây là bằng chứng nội bộ quan trọng khi khách hỏi về truy vết sự kiện, nhưng không thay thế SIEM hoặc hệ thống giám sát bảo mật chuyên dụng.

## 5. SSL/TLS deployment checklist

Khi triển khai public domain/HTTPS, cần thu thập bằng chứng sau:

| Hạng mục | Yêu cầu |
| --- | --- |
| Domain production | Ví dụ `https://visitor.customer.com` |
| SSL certificate | Chứng chỉ hợp lệ từ Let's Encrypt, Cloudflare, DigiCert, Sectigo hoặc CA khác |
| Thời hạn chứng chỉ | Có ngày bắt đầu và ngày hết hạn |
| HTTPS binding | IIS/hosting/cloud bind port 443 với certificate |
| HTTP redirect | Truy cập HTTP port 80 tự động chuyển sang HTTPS |
| APP_URL | `.env` đặt đúng domain HTTPS |
| Cookie secure | `SESSION_SECURE_COOKIE=true` nếu chạy HTTPS |
| Debug production | `APP_DEBUG=false` |
| Cache config | Chạy `php artisan config:clear` sau khi sửa `.env` |
| Kiểm tra trình duyệt | Có ổ khóa bảo mật trên browser |
| Kiểm tra header | Nên có `X-Frame-Options`, khuyến nghị thêm HSTS/CSP nếu khách yêu cầu |

Lệnh kiểm tra nhanh sau triển khai:

```powershell
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

Nếu có quyền terminal trên server, có thể kiểm tra header:

```powershell
curl -I https://your-domain.example
```

## 6. Những hạng mục chưa thể tự cấp chứng chỉ

| Hạng mục khách hỏi | Tình trạng | Cách xử lý |
| --- | --- | --- |
| Penetration testing report | Chưa có báo cáo bên thứ ba | Cần thuê đơn vị pentest độc lập |
| VAPT report | Chưa có báo cáo bên thứ ba | Cần thuê đơn vị VAPT độc lập |
| OWASP compliance certificate | OWASP không phải chứng chỉ sản phẩm tự động | Có thể cung cấp self-assessment, checklist ASVS; nếu cần chứng nhận phải qua đơn vị audit |
| SSL/TLS certificate | Có thể hỗ trợ cấu hình | Chứng chỉ thật sẽ được cấp theo domain production của khách |

## 7. Khuyến nghị trước khi go-live

1. Đặt production `.env`:
   - `APP_ENV=production`
   - `APP_DEBUG=false`
   - `APP_URL=https://domain-cua-khach`
   - `SESSION_SECURE_COOKIE=true`

2. Bật HTTPS và redirect HTTP sang HTTPS.

3. Chạy migration/test cần thiết trên môi trường staging trước production.

4. Kiểm tra lại phân quyền:
   - Admin
   - Lễ tân
   - Bảo vệ
   - Người tiếp khách/host

5. Kiểm tra audit log cho các thao tác:
   - Tạo lịch
   - Duyệt/từ chối
   - Check-in/check-out
   - Sửa nhân viên/phòng ban
   - Sửa cấu hình email/logo/phân quyền

6. Lưu bằng chứng:
   - Ảnh màn hình HTTPS/domain.
   - Kết quả test chính.
   - Kết quả `composer audit`.
   - Ảnh màn hình audit log.

## 8. Mẫu phản hồi cho khách hàng

Vu Tien có thể cung cấp bộ tài liệu kỹ thuật bảo mật gồm:

- Security self-assessment theo nhóm rủi ro OWASP.
- Checklist SSL/TLS và hướng dẫn triển khai HTTPS.
- Bằng chứng kiểm tra dependency bằng `composer audit`.
- Bằng chứng test tự động nội bộ.
- Mô tả tính năng audit log/event log trong hệ thống.

Hiện tại các chứng chỉ như penetration testing report, OWASP certification hoặc VAPT report cần được thực hiện bởi đơn vị kiểm thử bảo mật độc lập nếu khách hàng yêu cầu bản chính thức.

