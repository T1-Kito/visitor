# Gatehouse Printer Bridge

Printer Bridge là tool local chạy trên máy có cắm máy in nhiệt. Laravel chạy trên server/hosting nên không thể tự nhìn thấy máy in USB ở máy lễ tân. Bridge sẽ chạy nền tại máy lễ tân và nhận lệnh in từ trang admin.

Địa chỉ mặc định:

```text
http://127.0.0.1:9191
```

## Cách dùng khi demo hoặc triển khai cho khách

### 1. Cài Node.js LTS

Máy lễ tân/admin cần cài Node.js LTS:

```text
https://nodejs.org/
```

Sau khi cài xong, mở Command Prompt kiểm tra:

```bat
node -v
```

### 2. Cài Printer Bridge tự chạy nền

Vào thư mục `printer-bridge`, bấm đúp:

```text
install-printer-bridge.bat
```

File này sẽ:

- Tạo `config.json` nếu chưa có.
- Tạo shortcut `Gatehouse Printer Bridge` trong Startup của Windows.
- Tự khởi động Bridge ở chế độ nền.
- Lần sau mở máy, Bridge tự chạy lại, không cần mở PowerShell thủ công.

### 3. Cấu hình máy in trong admin

Mở hệ thống Laravel, vào:

```text
/settings/printer
```

Thao tác:

```text
Kiểm tra kết nối
→ Chọn máy in nhiệt
→ Chọn khổ giấy 58mm hoặc 80mm
→ Chọn chế độ in
→ Lưu cấu hình
→ In thử QR
```

### 4. In QR thật

Admin vào chi tiết lịch hẹn:

```text
/visits/{id}
```

Bấm:

```text
In QR
```

Bridge sẽ nhận lệnh và in theo cấu hình đã chọn.

## Chế độ in

### Xem trước rồi in

Chế độ `preview` tạo phiếu HTML rồi mở bằng trình duyệt. Phù hợp khi chưa chắc driver máy in nhiệt hỗ trợ ESC/POS.

Ưu điểm:

- Dễ test.
- Ít lỗi driver.
- Hợp với demo nhanh.

Nhược điểm:

- Người dùng vẫn cần bấm in trong trình duyệt.

### In trực tiếp ESC/POS

Chế độ `escpos` gửi lệnh RAW tới máy in nhiệt Windows.

Ưu điểm:

- Gần giống vận hành thật.
- Ít thao tác hơn.

Nhược điểm:

- Phụ thuộc driver/máy in.
- Một số máy in nhiệt không hỗ trợ QR native, khi đó nên quay về `preview`.

## Kiểm tra nhanh

Khi Bridge đang chạy, mở trình duyệt:

```text
http://127.0.0.1:9191/health
```

Hoặc kiểm tra danh sách máy in:

```text
http://127.0.0.1:9191/printers
```

## Gỡ cài đặt

Nếu không muốn Bridge tự chạy nữa, bấm đúp:

```text
uninstall-printer-bridge.bat
```

File này sẽ:

- Xóa shortcut trong Startup.
- Dừng Bridge đang chạy.

## Lưu ý khi chạy qua hosting hoặc ngrok

Nếu web chạy HTTPS nhưng Bridge chạy local HTTP, một số trình duyệt có thể chặn gọi từ web HTTPS sang `http://127.0.0.1:9191`.

Khi demo nhanh:

- Ưu tiên mở hệ thống bằng local `http://127.0.0.1:8000`.
- Nếu dùng domain/hosting HTTPS, cần test trước trang `/settings/printer`.
- Giai đoạn production có thể nâng Bridge lên HTTPS local hoặc dùng ứng dụng desktop/native.

## Checklist đem qua khách

- Máy đã cài Node.js LTS.
- Máy in nhiệt đã cài driver Windows.
- In thử được từ Windows trước.
- Chạy `install-printer-bridge.bat` một lần.
- Vào `/settings/printer` chọn đúng máy in.
- Bấm `In thử QR`.
- Vào chi tiết lịch hẹn bấm `In QR`.
