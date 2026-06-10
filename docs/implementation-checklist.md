# Implementation Checklist

Tai lieu nay dung `../../1111README.md` lam ban thiet ke goc va chuyen thanh checklist cho ban Laravel.

Quy uoc:

- [DONE] Da co va dang chay duoc o Laravel.
- [PARTIAL] Da co mot phan, can hoan thien.
- [TODO] Chua lam.
- [LATER] De sau MVP hoac can tich hop that.

## 1. Gioi thieu he thong

[DONE] Flow tong quat da duoc bam theo:

```text
Tao lich hen / walk-in -> phe duyet -> QR/badge -> check-in -> theo doi -> canh bao -> check-out -> bao cao/audit log
```

[DONE] Cac role chinh da seed:

- Super Admin
- Admin
- Le tan
- Bao ve
- Nhan vien
- Quan ly phong ban
- An ninh/Hanh chinh
- Khach qua kiosk public

[PARTIAL] Role da co permission co ban, nhung chua co policy/rule sau cho tung action va tung phong ban.

## 2. Cong nghe su dung

[DONE] Da chuyen cong nghe sang Laravel:

- Laravel 12 thay NestJS.
- Blade thay Next.js/React.
- MySQL/IIS cho ban cai dat Windows.
- Eloquent thay Prisma.
- Laravel session auth thay JWT cho ban Blade MVP.

[TODO] Neu sau nay can mobile/API rieng thi bo sung API token/JWT/Sanctum.

## 3. Cau truc thu muc

[DONE] Da co cau truc Laravel chuan.

[PARTIAL] Can tach service layer khi nghiep vu lon hon:

- VisitService
- ApprovalService
- CheckinService
- CheckoutService
- ReportService
- AuditLogger
- NotificationService
- AccessControlService

## 4-6. Moi truong va database

[DONE] Dung `.env` MySQL/XAMPP.

[DONE] Co migration Laravel.

[PARTIAL] README da viet lai cach chay local.

[DONE] Dong goi theo huong IIS cho Windows.

## 7. Migration

[DONE] Co bang loi:

- roles
- permissions
- permission_role
- role_user
- departments
- employees
- visitors
- visits
- approvals
- audit_logs
- badges
- access_control_logs

[TODO] Bang con thieu:

- system_settings
- access_zones
- access_devices
- qr_tokens neu muon quan ly QR rieng
- report_exports neu can lich su export

## 8. Seed data

[DONE] Users/roles/permissions mau.

[DONE] Phong ban mau.

[DONE] Nhan vien mau.

[DONE] Khach mau.

[DONE] Lich hen mau.

[DONE] QR/badge mau.

[DONE] Khach dang trong cong ty.

[DONE] Khach cho phe duyet.

[PARTIAL] Khach qua gio co logic alert, nhung seed nen them case qua gio ro hon.

[TODO] Cau hinh he thong mau.

[PARTIAL] Notification phat sinh tu action thuc te, chua co notification mau rieng trong seed.

[TODO] Watchlist mau.

## 9-11. Cach chay app

[DONE] Laravel server chay bang `php artisan serve`.

[PARTIAL] Chua co docs XAMPP that chi tiet bang hinh/step phpMyAdmin.

[TODO] Chua co build/deploy production.

## 12. Tai khoan mau

[DONE] Da seed tai khoan mau voi password `Admin@123`.

[TODO] Can cap nhat README moi khi them role/user moi.

## 13. Danh sach chuc nang/API chinh

### Auth

[DONE] `GET /login`

[DONE] `POST /login`

[DONE] `POST /logout`

[TODO] `GET /auth/me` dang chua co.

[TODO] Forgot/reset password.

[TODO] Rate limit login.

### Users / RBAC

[DONE] RBAC UI da co o `/rbac` voi user/role/permission CRUD va role-permission matrix.

[DONE] Update role cho user.

[DONE] Update permission cho role.

[DONE] CRUD users day du: list/create/detail/edit/delete.

[DONE] CRUD roles day du: list/create/detail/edit/delete co chan xoa role he thong.

[DONE] CRUD permissions day du: list/create/detail/edit/delete co chan xoa permission he thong.

[TODO] Route API JSON cho users/roles/permissions neu can.

[DONE] Lock/unlock user bang is_active.

### Departments

[DONE] List/create co ban.

[DONE] Detail/edit/delete.

[DONE] Search.

[DONE] Validate xoa phong ban neu dang co nhan vien.

### Employees

[DONE] List/create co ban.

[DONE] Kiosk employee search.

[DONE] Detail/edit/delete.

[DONE] Search trong admin.

[TODO] Lien ket manager/phong ban ro hon.

### Visitors

[DONE] List/create co ban.

[DONE] Detail/edit/delete.

[DONE] Search trong admin.

[TODO] Hash/encrypt identity number.

[TODO] Duplicate detection theo phone/email/identity.

### Visits / Lich hen

[DONE] Create visit.

[DONE] List visits.

[PARTIAL] Filter/search co ban trong UI.

[DONE] Detail visit.

[DONE] Edit visit.

[DONE] Cancel visit.

[DONE] Generate QR route rieng.

[TODO] QR image that.

[TODO] `today/currently-inside/waiting-approval/overstay` API rieng neu can.

### Approvals

[DONE] Approve.

[DONE] Reject.

[DONE] Wait.

[PARTIAL] Department manager scope da co mot phan.

[TODO] My pending.

[TODO] Department pending.

[TODO] Escalate.

[TODO] Notification cho host khi co walk-in/pending.

### Check-in

[DONE] Scan QR token: scan -> hien thong tin -> xac nhan moi check-in.

[DONE] Confirm check-in.

[PARTIAL] Manual check-in co qua kiosk walk-in va form tao visit, nhung admin manual flow chua ro.

[TODO] Camera QR scanner that cho kiosk/le tan/bao ve.

[TODO] Rule khong check-in qua som/qua tre.

[TODO] In/cap badge that sau check-in.

### Check-out

[DONE] Scan QR.

[DONE] Scan badge.

[DONE] Confirm check-out.

[PARTIAL] Tim khach dang trong cong ty co trong UI, nhung chua co API rieng `search-current-visitors`.

[TODO] Revoke access-control provider rieng.

### Dashboard

[DONE] Dashboard UI co card thong ke.

[DONE] Danh sach khach dang trong cong ty.

[DONE] Alerts co ban.

[DONE] API `dashboard/summary`.

[DONE] API `dashboard/live-visitors`.

[DONE] API `dashboard/alerts`.

[PARTIAL] SSE `dashboard/events` co snapshot MVP, chua stream lien tuc/WebSocket.

### Reports

[DONE] Reports page.

[DONE] Export CSV.

[DONE] Visits report day du.

[DONE] By department.

[DONE] By host.

[DONE] Current visitors.

[DONE] Overstay.

[DONE] Rejected.

[DONE] Emergency current visitors.

[DONE] Export Excel `.xlsx`.

### Notifications

[DONE] Table notifications.

[DONE] Notification center UI.

[DONE] Unread count.

[DONE] Mark read/read all.

[DONE] Notification tu cac action chinh: tao/cap nhat lich, kiosk walk-in, approval, check-in/check-out.

[TODO] Email notification.

### Audit Logs

[DONE] Audit log table.

[DONE] Audit log UI.

[PARTIAL] Chua redact du lieu nhay cam that su day du.

[TODO] Filter audit log nang cao.

### Kiosk public

[DONE] `/kiosk` UI.

[DONE] `/kiosk/employees/search`.

[DONE] `/kiosk/checkin/manual`.

[DONE] `/kiosk/checkin/scan-qr`.

[DONE] `/kiosk/checkin/status/{visit}`.

[DONE] `/kiosk/checkin/{visit}/confirm`.

[PARTIAL] Confirm duoc khoa bang session kiosk, nhung can them rate limit.

[TODO] Camera scanner that.

[TODO] UI kiosk lon hon, de dung tren tablet/man hinh cam ung.

### Mock Access Control Integration

[PARTIAL] Co `access_control_logs` va badge lifecycle.

[TODO] `GET /integrations/access-control/visits/{visit}/logs`.

[TODO] `POST /integrations/access-control/badges/{badge}/sync`.

[TODO] `POST /integrations/access-control/visits/{visit}/revoke`.

[TODO] Access zone/device model.

## 14. Luong demo

[DONE] Tao lich hen.

[PARTIAL] Sinh QR: co token tu khi tao visit, chua co nut generate QR/image rieng.

[DONE] Scan QR bang token.

[DONE] Phe duyet.

[DONE] Check-in.

[DONE] Check-out.

[DONE] Xem dashboard.

[DONE] Xuat bao cao khan cap qua `/reports/emergency-current-visitors` va CSV export.

## 15. Test

[DONE] PHPUnit dang chay pass.

[PARTIAL] Test hien tai con rat it.

[TODO] Test auth.

[TODO] Test RBAC permission.

[TODO] Test create visit.

[TODO] Test approval approve/reject/wait.

[TODO] Test check-in/check-out.

[TODO] Test kiosk public.

[DONE] Test reports/export.

[TODO] Test audit logs.

## 16. Loi thuong gap

[PARTIAL] README da co huong dan local co ban.

[TODO] Them muc loi MySQL/XAMPP.

[TODO] Them muc loi migrate/seed.

[TODO] Them muc loi 403 permission.

[TODO] Them muc loi QR het han/khong hop le.

## 17. Bao mat co ban

[DONE] Password hash bang Laravel.

[DONE] RBAC middleware co ban.

[DONE] Kiosk public route rieng.

[PARTIAL] Khong an QR token tren status page kiosk nua.

[TODO] Rate limit login/kiosk.

[TODO] Password policy.

[TODO] Forgot/reset password.

[TODO] Hash/encrypt identity number.

[TODO] Redact audit log data nhay cam.

[TODO] Data retention policy.

## 18. Roadmap phat trien

### Giai doan 1: Hoan thien MVP van hanh thuc te

[DONE] Visits detail/edit/cancel/generate QR.

[TODO] QR image + camera scanner.

[DONE] CRUD catalog day du cho departments/employees/visitors.

[DONE] CRUD roles/permissions cho RBAC.

[DONE] Notification center.

[DONE] Watchlist UI.

[DONE] Export Excel.

[TODO] Test coverage nghiep vu.

### Giai doan 2: Bao mat va production

[TODO] Rate limiting.

[TODO] Password reset/policy.

[TODO] Sensitive data protection.

[TODO] Backup/restore.

[TODO] Logging/monitoring.

### Giai doan 3: Realtime va notification nang cao

[TODO] SSE/WebSocket.

[TODO] Email/Zalo/Slack/Teams/SMS.

[TODO] Notification preferences.

### Giai doan 4: Tich hop kiem soat cua that

[LATER] Device provider.

[LATER] Sync badge/QR voi thiet bi.

[LATER] Device event realtime.

[LATER] Access zone rules.

### Giai doan 5: Tich hop doanh nghiep

[LATER] SSO.

[LATER] HRM sync.

[LATER] Calendar integration.

[LATER] Kiosk in badge.

[LATER] OCR giay to.

[LATER] Mobile app.

## Thu tu code tiep theo

Lam theo dung thu tu nay de tranh lech kien truc:

1. Visits detail/edit/cancel/generate QR.
2. QR image + scanner camera.
3. CRUD users/departments/employees/visitors day du.
4. Mock access-control endpoints.
5. Test nghiep vu.
6. Security hardening.
