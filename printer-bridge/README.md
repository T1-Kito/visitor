# Gatehouse Printer Bridge

Tool local để máy lễ tân hoặc máy admin nhận lệnh in QR từ Laravel.

## Vì sao cần bridge?

Laravel chạy trên server nên không tự nhìn thấy máy in đang cắm ở máy người dùng. Printer Bridge chạy tại máy lễ tân/admin, expose API local:

```text
http://127.0.0.1:9191
```

Trang admin Laravel sẽ gọi API này để:

- Kiểm tra bridge đang chạy.
- Liệt kê máy in Windows.
- Chọn máy in nhiệt.
- Lưu cấu hình giấy 58mm/80mm.
- In thử QR.
- In QR thật từ trang chi tiết lịch hẹn.

## Chạy bridge

Yêu cầu máy có Node.js 18+.

```powershell
cd printer-bridge
powershell -ExecutionPolicy Bypass -File .\start-printer-bridge.ps1
```

Kiểm tra nhanh:

```powershell
curl http://127.0.0.1:9191/health
curl http://127.0.0.1:9191/printers
curl http://127.0.0.1:9191/config
```

## Cấu hình bằng trang admin

Trong Laravel vào:

```text
/settings/printer
```

Thao tác:

```text
Kiểm tra kết nối
→ Chọn máy in
→ Chọn khổ giấy 58mm hoặc 80mm
→ Chọn chế độ in
→ Lưu cấu hình
→ In thử QR
```

Bridge sẽ lưu cấu hình vào:

```text
printer-bridge/config.json
```

## Chế độ in

### `preview`

Bridge tạo phiếu HTML trong `printer-bridge/jobs` và mở lên để admin in qua trình duyệt. Đây là chế độ an toàn nhất khi chưa rõ driver máy in.

### `escpos`

Bridge gửi lệnh RAW ESC/POS trực tiếp tới máy in nhiệt Windows. Chế độ này phù hợp máy in hóa đơn có hỗ trợ ESC/POS QR native.

Nếu máy in không in được QR, đổi lại `preview`.

## API nội bộ

```text
GET  /health
GET  /printers
GET  /config
POST /config
POST /print
```

`/printers` trả về danh sách máy in Windows bằng `Get-Printer`.

`/config` cho phép trang admin lưu:

```json
{
  "paper": "80mm",
  "mode": "preview",
  "printerName": "TEN MAY IN",
  "openAfterPrint": true
}
```

## Lưu ý khi demo bằng ngrok

Bridge đã cho phép origin dạng `https://*.ngrok-free.app` để tiện demo. Nếu trình duyệt vẫn chặn mixed content khi web HTTPS gọi HTTP local, demo trên URL local `http://127.0.0.1:8000` hoặc nâng cấp bridge sang HTTPS local ở giai đoạn production.
