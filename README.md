# Visitor Management System - Laravel

Hệ thống quản lý khách ra/vào công ty bằng QR code, hỗ trợ tạo lịch hẹn, phê duyệt, check-in, check-out, kiosk cho khách, báo cáo, thông báo, danh sách cảnh báo và audit log.

Phiên bản hiện tại được xây dựng bằng **PHP Laravel + Blade** để chạy tốt với môi trường XAMPP/MySQL. Đây là bản MVP phục vụ demo và có thể mở rộng tiếp thành hệ thống vận hành thực tế.

## 1. Tổng Quan Luồng Nghiệp Vụ

```text
Tạo lịch hẹn / Khách walk-in tại kiosk
→ Hệ thống tạo mã lịch hẹn và QR token
→ Nhân viên tiếp khách duyệt hoặc từ chối
→ Lễ tân/bảo vệ scan QR hoặc nhập mã lịch hẹn
→ Check-in khách vào công ty
→ Theo dõi khách đang trong công ty, khách quá giờ
→ Check-out khách ra khỏi công ty
→ Xem báo cáo, thông báo, audit log
```

Các vai trò chính:

- **Super Admin/Admin**: quản trị hệ thống, tài khoản, phân quyền, dữ liệu danh mục, báo cáo, cấu hình kiosk.
- **Lễ tân**: tạo lịch, tiếp nhận walk-in, check-in/check-out, theo dõi khách tại quầy.
- **Bảo vệ**: scan QR/badge, check-in/check-out tại cổng.
- **Nhân viên tiếp khách**: xem và phê duyệt/từ chối yêu cầu tiếp khách.
- **Quản lý phòng ban**: theo dõi yêu cầu theo phòng ban.
- **An ninh/Hành chính**: theo dõi cảnh báo, khách quá giờ, watchlist, báo cáo khẩn cấp.
- **Khách**: dùng kiosk public, không cần đăng nhập.

## 2. Công Nghệ Sử Dụng

- PHP >= 8.2
- Laravel 12
- MySQL/XAMPP
- Blade template
- Bootstrap 5 kết hợp CSS riêng `public/css/admin-ui.css`
- Eloquent ORM
- Laravel session authentication
- RBAC theo role/permission
- Simple QR Code để sinh QR thật
- PHPUnit cho test cơ bản

## 3. Cài Đặt Local Bằng XAMPP

Bật Apache và MySQL trong XAMPP, sau đó tạo database trong phpMyAdmin:

```text
visitor_management
```

Cài dependency:

```bash
composer install
```

Tạo file môi trường nếu chưa có:

```bash
copy .env.example .env
php artisan key:generate
```

Cấu hình database trong `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=visitor_management
DB_USERNAME=root
DB_PASSWORD=
```

Chạy migration và seed dữ liệu mẫu:

```bash
php artisan migrate --seed
```

Nếu chỉ muốn nạp lại dữ liệu demo mà không xóa database:

```bash
php artisan db:seed --class=VmsSeeder
```

Lệnh reset toàn bộ database dev, chỉ dùng khi chấp nhận xóa dữ liệu local:

```bash
php artisan migrate:fresh --seed
```

Chạy server:

```bash
php artisan serve
```

Truy cập:

```text
Admin: http://127.0.0.1:8000/login
Kiosk: http://127.0.0.1:8000/kiosk
```

## 4. Demo Online Bằng Ngrok

Khi demo cho sếp bằng ngrok, nên chạy đúng thứ tự này:

```bash
php artisan config:clear
php artisan view:clear
php artisan serve --host=127.0.0.1 --port=8000
```

Mở terminal khác và chạy:

```bash
ngrok htgit add .tp 8000
```

Sau đó gửi cho sếp link dạng:

```text
https://xxxxx.ngrok-free.app
```

Không gửi link `http://127.0.0.1:8000` hoặc `http://localhost:8000` vì máy của sếp không truy cập được máy local của mình.

Nếu mở link ngrok mà bị mất giao diện:

- Kiểm tra link CSS có tải được không: `https://xxxxx.ngrok-free.app/css/admin-ui.css`.
- Chạy lại `php artisan config:clear` và `php artisan view:clear`.
- Đảm bảo đang chạy Laravel bằng `php artisan serve --port=8000`.
- Không dùng link cũ sau khi tắt/mở lại ngrok, vì link miễn phí thường đổi mỗi lần chạy.
- Nếu dùng asset hoặc storage upload, chạy thêm `php artisan storage:link`.

## 5. Tài Khoản Demo

Mật khẩu chung:

```text
Admin@123
```

| Vai trò | Email |
|---|---|
| Super Admin | `superadmin@company.local` |
| Admin | `admin@company.local` |
| Lễ tân | `reception1@company.local` |
| Bảo vệ | `guard1@company.local` |
| Nhân viên 1 | `employee1@company.local` |
| Nhân viên 2 | `employee2@company.local` |
| Nhân viên 3 | `employee3@company.local` |
| Nhân viên 4 | `employee4@company.local` |
| Quản lý phòng ban | `manager1@company.local` |
| An ninh/Hành chính | `security.admin@company.local` |

## 6. Dữ Liệu Mẫu Đã Seed

Seeder [VmsSeeder.php](/c:/Users/Admin/Downloads/visitor/app-laravel/database/seeders/VmsSeeder.php) tạo sẵn:

- Role, permission và user mẫu.
- Phòng ban: Sales, Operations, IT, Finance.
- Nhân viên mẫu có phòng ban và tài khoản đăng nhập.
- Khách mẫu để demo danh sách khách, lịch hẹn, kiosk.
- Lịch hẹn hôm nay với đủ trạng thái: chờ duyệt, đã duyệt, từ chối, đang trong công ty, đã rời công ty.
- Dữ liệu lịch sử nhiều ngày để màn báo cáo có biểu đồ và thống kê.
- QR token thật để hiển thị QR trên chi tiết lịch hẹn.
- Badge và access log mẫu.
- Watchlist cho màn danh sách cảnh báo.
- Notification mẫu cho admin/lễ tân/an ninh.
- Cấu hình kiosk mẫu: tên công ty, hotline, giờ làm việc, màu chủ đạo.

Một số QR token test nhanh:

```text
demo-qr-token-001: khách đang trong công ty, dùng để test check-out
demo-qr-token-002: lịch đã duyệt, dùng để test check-in
demo-qr-token-003: lịch chờ duyệt, dùng để test cảnh báo chưa được duyệt
demo-WK-260529-001-token: khách đang trong công ty từ dữ liệu demo mở rộng
```

## 7. Các Màn Hình Chính

```text
/dashboard        Tổng quan vận hành
/visits           Quản lý lịch hẹn
/visits/create    Tạo lịch hẹn mới
/approvals        Phê duyệt yêu cầu tiếp khách
/checkin          Check-in khách
/checkout         Check-out khách
/visitors         Quản lý khách
/employees        Quản lý nhân viên
/departments      Quản lý phòng ban
/reports          Báo cáo & thống kê
/watchlists       Danh sách cảnh báo
/alerts           Cảnh báo vận hành
/badges           Thẻ ra vào
/notifications    Trung tâm thông báo
/settings/kiosk   Cấu hình kiosk
/rbac             Phân quyền
/audit-logs       Nhật ký hệ thống
/kiosk            Kiosk public cho khách
```

## 8. Luồng Demo Đề Xuất Cho Sếp

### 8.1. Dashboard

1. Đăng nhập `superadmin@company.local / Admin@123`.
2. Vào `/dashboard`.
3. Giới thiệu các chỉ số: khách hôm nay, đang trong công ty, chờ duyệt, đã check-out, quá giờ.
4. Mở nhanh các thao tác: tạo lịch, phê duyệt, check-in, check-out, báo cáo.

### 8.2. Tạo Lịch Hẹn

1. Vào `/visits/create`.
2. Nhập thông tin khách, người tiếp, phòng ban, giờ hẹn, mục đích.
3. Lưu lịch hẹn.
4. Hệ thống tự sinh mã lịch hẹn và QR token.
5. Mở trang chi tiết lịch hẹn để xem QR thật.

### 8.3. Phê Duyệt

1. Vào `/approvals`.
2. Xem danh sách theo dạng ngang: khách, người tiếp, phòng ban, giờ hẹn, trạng thái.
3. Chọn duyệt hoặc từ chối.
4. Khi duyệt, QR đã có sẵn và khách có thể dùng để check-in.

### 8.4. Check-in

1. Vào `/checkin`.
2. Nhập mã QR token hoặc mã lịch hẹn.
3. Dùng token mẫu `demo-qr-token-002` để test lịch đã duyệt.
4. Kiểm tra thông tin khách.
5. Bấm xác nhận check-in.
6. Dashboard và danh sách khách đang trong công ty được cập nhật.

### 8.5. Check-out

1. Vào `/checkout`.
2. Nhập QR token, mã lịch hẹn hoặc mã thẻ.
3. Dùng token mẫu `demo-qr-token-001` để test khách đang trong công ty.
4. Kiểm tra thông tin khách, trạng thái, thời gian ở lại.
5. Bấm xác nhận check-out.
6. Hệ thống ghi access log và cập nhật báo cáo.

### 8.6. Kiosk Public

1. Mở `/kiosk`.
2. Khách đăng ký walk-in bằng form bên giữa.
3. Hoặc nhập QR/mã lịch hẹn ở cột phải.
4. Kiosk lấy cấu hình từ `/settings/kiosk`, gồm tên công ty, hotline, giờ làm việc, logo và ảnh nền.

### 8.7. Báo Cáo

1. Vào `/reports`.
2. Lọc theo khoảng thời gian, phòng ban, trạng thái, loại khách.
3. Xem KPI, biểu đồ theo ngày, top nhân viên tiếp khách, top phòng ban.
4. Xuất file theo nhu cầu demo.

## 9. Trạng Thái Nghiệp Vụ

| Mã trạng thái | Hiển thị tiếng Việt | Ý nghĩa |
|---|---|---|
| `pending` | Chờ duyệt | Lịch đang chờ nhân viên tiếp khách xử lý |
| `approved` | Đã duyệt | Khách được phép check-in |
| `rejected` | Từ chối | Lịch bị từ chối, không được check-in |
| `checked_in` | Đang trong công ty | Khách đã vào công ty, chưa check-out |
| `checked_out` | Đã rời công ty | Khách đã check-out |
| `cancelled` | Đã hủy | Lịch bị hủy |

## 10. QR Và Mã Lịch Hẹn

- Khi tạo lịch hẹn, hệ thống sinh mã lịch hẹn không trùng.
- Hệ thống cũng sinh `qr_token` để tạo QR thật trên trang chi tiết lịch hẹn.
- Khi nhân viên duyệt lịch, QR đã sẵn sàng để dùng check-in.
- Check-in/check-out có thể dùng QR token hoặc mã lịch hẹn.
- QR không chứa thông tin cá nhân, chỉ chứa token để hệ thống tra cứu.

## 11. Cấu Hình Kiosk

Admin có thể chỉnh tại:

```text
/settings/kiosk
```

Các thông tin có thể cấu hình:

- Tên công ty.
- Tên hệ thống kiosk.
- Subtitle kiosk.
- Câu chào mừng.
- Mô tả ngắn.
- Hotline lễ tân/bảo vệ.
- Giờ làm việc.
- Logo công ty.
- Ảnh nền kiosk.
- Màu chủ đạo.

Nếu có ảnh nền kiosk, panel trái sẽ dùng ảnh đó với overlay navy để chữ dễ đọc. Nếu chưa có ảnh, hệ thống dùng nền navy gradient mặc định.

## 12. Lệnh Kiểm Tra Khi Phát Triển

Xem danh sách route:

```bash
php artisan route:list
```

Kiểm tra route kiosk:

```bash
php artisan route:list --path=kiosk
```

Kiểm tra Blade có lỗi không:

```bash
php artisan view:cache
php artisan view:clear
```

Chạy test:

```bash
php artisan test
```

Quét cảnh báo quá giờ:

```bash
php artisan vms:scan-overstay
```

## 13. Ghi Chú Hiện Tại

Đã có:

- Gatehouse Pro UI cho các màn demo chính.
- QR thật trên trang chi tiết lịch hẹn.
- Tạo lịch sinh QR ngay.
- Phê duyệt/từ chối.
- Check-in/check-out theo QR token, mã lịch hẹn hoặc badge.
- Kiosk public có cấu hình từ admin.
- Báo cáo có dữ liệu mẫu và thống kê.
- Watchlist, notification, audit log.

Cần làm tiếp nếu muốn gần production hơn:

- Camera QR scanner thật bằng thư viện frontend.
- Export PDF/Excel hoàn chỉnh hơn nếu cần mẫu file đẹp.
- Rate limit cho login/kiosk.
- Reset password/forgot password.
- Mã hóa hoặc hash dữ liệu nhạy cảm.
- Chính sách lưu trữ/xóa dữ liệu khách.
- Tích hợp thiết bị kiểm soát cửa thật.
- Test nghiệp vụ sâu hơn cho từng role.
