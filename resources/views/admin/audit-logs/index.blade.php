@extends('layouts.admin')

@section('title', 'Nhật ký hệ thống | VMS')
@section('page_title', 'Nhật ký hệ thống')
@section('page_subtitle', 'Theo dõi các thao tác quan trọng đã xảy ra trong hệ thống')

@push('styles')
    <style>
        .audit-shell {
            display: grid;
            gap: 1rem;
        }

        .audit-filter {
            display: grid;
            grid-template-columns: minmax(240px, 1fr) minmax(240px, .8fr) 150px 150px 120px;
            gap: .85rem;
            align-items: end;
        }

        .audit-field label {
            display: block;
            margin-bottom: .38rem;
            color: #526b87;
            font-size: .78rem;
            font-weight: 500;
        }

        .audit-field .form-control,
        .audit-field .form-select {
            min-height: 46px;
            border-color: #d8e5f2;
            border-radius: 14px;
            color: #10233d;
            font-size: .86rem;
            box-shadow: none;
        }

        .audit-field .form-control:focus,
        .audit-field .form-select:focus {
            border-color: #8cc6ff;
            box-shadow: 0 0 0 .22rem rgba(20, 107, 215, .09);
        }

        .audit-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .audit-table th {
            padding: .85rem 1rem;
            color: #7187a3;
            background: #fbfdff;
            border-bottom: 1px solid #edf3fb;
            font-size: .72rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .audit-table td {
            padding: .9rem 1rem;
            border-bottom: 1px solid #edf3fb;
            color: #10233d;
            font-size: .84rem;
            vertical-align: top;
        }

        .audit-table tbody tr:hover td {
            background: #f8fcff;
        }

        .audit-id {
            color: #7f93ad;
            font-size: .78rem;
        }

        .audit-time strong,
        .audit-time span,
        .audit-user strong,
        .audit-user span,
        .audit-action strong,
        .audit-action span {
            display: block;
        }

        .audit-time strong,
        .audit-user strong,
        .audit-action strong {
            color: #10233d;
            font-weight: 500;
        }

        .audit-time span,
        .audit-user span,
        .audit-action span {
            margin-top: .12rem;
            color: #7187a3;
            font-size: .72rem;
        }

        .audit-context {
            display: grid;
            gap: .18rem;
            margin-top: .38rem;
            color: #7187a3;
            font-size: .7rem;
        }

        .audit-context span {
            display: flex;
            align-items: center;
            gap: .32rem;
            min-width: 0;
        }

        .audit-context .audit-url {
            max-width: 260px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .audit-action i {
            width: 30px;
            height: 30px;
            display: inline-grid;
            place-items: center;
            margin-right: .45rem;
            border-radius: 11px;
            color: #146bd7;
            background: #eaf4ff;
        }

        .audit-entity {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .3rem .62rem;
            border-radius: 999px;
            color: #315b89;
            background: #eef7ff;
            font-size: .76rem;
            font-weight: 500;
            white-space: nowrap;
        }

        .audit-meta {
            display: flex;
            flex-wrap: wrap;
            gap: .38rem;
            max-width: 460px;
        }

        .audit-meta span {
            display: inline-flex;
            align-items: center;
            gap: .25rem;
            max-width: 100%;
            padding: .28rem .55rem;
            border: 1px solid #e2edf8;
            border-radius: 999px;
            color: #526b87;
            background: #fff;
            font-size: .72rem;
        }

        .audit-meta span b {
            color: #10233d;
            font-weight: 500;
        }

        .audit-empty {
            padding: 3rem 1rem;
            color: #7187a3;
            text-align: center;
        }

        .audit-pagination {
            display: flex;
            justify-content: flex-end;
            padding: .85rem 1rem;
        }

        .audit-pagination nav {
            margin: 0;
        }

        .audit-pagination .pagination {
            margin: 0;
            gap: .3rem;
        }

        .audit-pagination .page-link {
            min-width: 34px;
            min-height: 34px;
            display: grid;
            place-items: center;
            padding: .35rem .65rem;
            border-color: #dbe7f4;
            border-radius: 9px;
            color: #315b89;
            font-size: .78rem;
            box-shadow: none;
        }

        .audit-pagination .page-item.active .page-link {
            border-color: var(--gate-blue);
            background: var(--gate-blue);
        }

        .audit-pagination svg {
            width: 14px;
            height: 14px;
        }

        @media (max-width: 992px) {
            .audit-filter {
                grid-template-columns: 1fr;
            }

            .audit-table {
                min-width: 980px;
            }
        }
    </style>
@endpush

@section('content')
    @php
        $actionLabels = [
            'settings.kiosk_updated' => 'Cập nhật cài đặt kiosk',
            'settings.printer_updated' => 'Cập nhật máy in',
            'settings.mail_updated' => 'Cập nhật cấu hình email',
            'settings.mail_tested' => 'Gửi thử email',
            'settings.admin_theme_updated' => 'Cập nhật màu giao diện',
            'settings.access_updated' => 'Cập nhật cấu hình ra/vào',
            'settings.logos_updated' => 'Cập nhật logo hệ thống',
            'visit.created' => 'Tạo lịch hẹn',
            'visit.updated' => 'Cập nhật lịch hẹn',
            'visit.deleted' => 'Xóa lịch hẹn',
            'visit.cancelled' => 'Hủy lịch hẹn',
            'visit.qr_generated' => 'Tạo mã QR cho lịch hẹn',
            'visit.qr_emailed' => 'Gửi mã QR cho khách',
            'visit.qr_scanned_for_checkin' => 'Quét mã QR để làm thủ tục vào',
            'visit.qr_scanned_for_checkout' => 'Quét mã QR để làm thủ tục ra',
            'visit.badge_scanned_for_checkout' => 'Quét thẻ để làm thủ tục ra',
            'visit.checked_in' => 'Xác nhận khách vào',
            'visit.checked_out' => 'Xác nhận khách ra',
            'visit.host_checkin_email_sent' => 'Gửi email báo người tiếp thành công',
            'visit.host_checkin_email_failed' => 'Gửi email báo người tiếp thất bại',
            'approval.approved' => 'Duyệt lịch hẹn',
            'approval.rejected' => 'Từ chối lịch hẹn',
            'approval.wait' => 'Chuyển lịch hẹn về chờ duyệt',
            'checkin.checked_in' => 'Cho khách vào',
            'checkout.checked_out' => 'Cho khách ra',
            'kiosk.walk_in_created' => 'Khách đăng ký tại kiosk',
            'kiosk.checked_in' => 'Khách check-in tại kiosk',
            'kiosk.checked_out' => 'Khách check-out tại kiosk',
            'kiosk.host_checkin_email_sent' => 'Gửi email báo host khách đã tới',
            'kiosk.host_checkin_email_failed' => 'Gửi email báo host thất bại',
            'watchlist.matched' => 'Khách trùng danh sách cảnh báo',
            'watchlist.created' => 'Tạo cảnh báo an ninh',
            'watchlist.updated' => 'Cập nhật cảnh báo an ninh',
            'watchlist.deleted' => 'Xóa cảnh báo an ninh',
            'rbac.user_role_updated' => 'Cập nhật vai trò tài khoản',
            'rbac.role_permissions_updated' => 'Cập nhật quyền vai trò',
            'rbac.permission_matrix_updated' => 'Cập nhật ma trận phân quyền',
            'user.created' => 'Tạo tài khoản',
            'user.updated' => 'Cập nhật tài khoản',
            'user.deleted' => 'Xóa tài khoản',
            'role.created' => 'Tạo vai trò',
            'role.updated' => 'Cập nhật vai trò',
            'role.deleted' => 'Xóa vai trò',
            'permission.created' => 'Tạo quyền',
            'permission.updated' => 'Cập nhật quyền',
            'permission.deleted' => 'Xóa quyền',
            'department.created' => 'Tạo phòng ban',
            'department.updated' => 'Cập nhật phòng ban',
            'department.deleted' => 'Xóa phòng ban',
            'employee.created' => 'Tạo nhân viên',
            'employee.imported' => 'Nhập danh sách nhân viên',
            'employee.updated' => 'Cập nhật nhân viên',
            'employee.deleted' => 'Xóa nhân viên',
            'visitor.created' => 'Tạo khách',
            'visitor.updated' => 'Cập nhật khách',
            'visitor.deleted' => 'Xóa khách',
            'report.export_csv' => 'Xuất báo cáo CSV',
            'report.export_xlsx' => 'Xuất báo cáo Excel',
        ];

        $entityLabels = [
            'visit' => 'Lịch hẹn',
            'visitor' => 'Khách',
            'employee' => 'Nhân viên',
            'department' => 'Phòng ban',
            'user' => 'Tài khoản',
            'role' => 'Vai trò',
            'permission' => 'Quyền',
            'report' => 'Báo cáo',
            'badge' => 'Thẻ ra vào',
            'system_setting' => 'Cài đặt',
            'watchlist' => 'Cảnh báo',
            'printer' => 'Máy in',
            'kiosk' => 'Máy kiosk',
        ];

        $metaLabels = [
            'code' => 'Mã lịch',
            'visit_code' => 'Mã lịch',
            'email' => 'Email',
            'host_email' => 'Email người tiếp',
            'host_name' => 'Người tiếp',
            'visitor_name' => 'Khách',
            'company_name' => 'Tên công ty',
            'company' => 'Công ty',
            'phone' => 'SĐT',
            'status' => 'Trạng thái',
            'old_status' => 'Trạng thái cũ',
            'new_status' => 'Trạng thái mới',
            'approved_by' => 'Người duyệt',
            'approved_at' => 'Thời gian duyệt',
            'qr_expires_at' => 'QR hết hạn',
            'action' => 'Thao tác',
            'name' => 'Tên',
            'role' => 'Vai trò',
            'user_id' => 'Tài khoản',
            'department' => 'Phòng ban',
            'reason' => 'Lý do',
            'error' => 'Lỗi',
            'navbar_color' => 'Màu thanh điều hướng',
            'content_background' => 'Màu nền nội dung',
            'primary_color' => 'Màu nhấn chính',
            'secondary_color' => 'Màu nhấn phụ',
            'kiosk_background_color' => 'Màu nền kiosk',
            'system_name' => 'Tên hệ thống',
            'login_title' => 'Tiêu đề đăng nhập',
            'recipient' => 'Người nhận',
            'host' => 'Máy chủ email',
            'port' => 'Cổng kết nối',
            'scheme' => 'Kiểu bảo mật',
            'auth_mode' => 'Kiểu xác thực',
            'from_address' => 'Email người gửi',
            'from_name' => 'Tên người gửi',
            'trigger_qr_approved' => 'Gửi QR khi duyệt',
            'trigger_host_checkin' => 'Báo người tiếp khi khách đến',
            'count' => 'Số lượng',
            'imported' => 'Đã nhập',
            'skipped' => 'Bỏ qua',
            'failed' => 'Thất bại',
            'type' => 'Loại',
        ];

        $statusLabels = [
            'pending' => 'Chờ duyệt',
            'approved' => 'Đã duyệt',
            'rejected' => 'Từ chối',
            'checked_in' => 'Đang trong công ty',
            'checked_out' => 'Đã rời công ty',
            'cancelled' => 'Đã hủy',
            'active' => 'Đang hoạt động',
            'inactive' => 'Ngừng hoạt động',
            'warning' => 'Cảnh báo',
            'critical' => 'Nghiêm trọng',
            'login' => 'Đăng nhập bằng tài khoản',
            'none' => 'Không bảo mật',
            'tls' => 'TLS / STARTTLS',
            'ssl' => 'SSL',
        ];

        $methodLabels = [
            'GET' => 'Xem dữ liệu',
            'POST' => 'Tạo mới / gửi dữ liệu',
            'PUT' => 'Cập nhật toàn bộ',
            'PATCH' => 'Cập nhật',
            'DELETE' => 'Xóa',
        ];

        $formatMetaValue = function ($key, $value) use ($statusLabels): string {
            if (is_bool($value)) {
                return $value ? 'Có' : 'Không';
            }

            if (is_array($value)) {
                return json_encode($value, JSON_UNESCAPED_UNICODE);
            }

            if ($value === null || $value === '') {
                return '-';
            }

            $text = (string) $value;

            if (in_array((string) $key, ['status', 'old_status', 'new_status', 'scheme', 'auth_mode'], true)) {
                return $statusLabels[strtolower($text)] ?? $text;
            }

            return $text;
        };
    @endphp

    <div class="audit-shell">
        <section class="panel-card">
            <form class="audit-filter" method="get" action="{{ route('admin.audit-logs.index') }}">
                <div class="audit-field">
                    <label>Lọc theo thao tác</label>
                    <input type="text" class="form-control" name="action" value="{{ $filters['action'] }}" placeholder="Ví dụ: duyệt lịch, gửi QR, kiosk...">
                </div>

                <div class="audit-field">
                    <label>Lọc theo người thao tác</label>
                    <select name="user_id" class="form-select">
                        <option value="">Tất cả người dùng</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" @selected($filters['user_id'] === (string) $user->id)>
                                {{ $user->name }} - {{ $user->email }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="audit-field">
                    <label>Từ ngày</label>
                    <input type="date" class="form-control" name="from_date" value="{{ $filters['from_date'] }}">
                </div>

                <div class="audit-field">
                    <label>Đến ngày</label>
                    <input type="date" class="form-control" name="to_date" value="{{ $filters['to_date'] }}">
                </div>

                <button class="btn btn-brand w-100" type="submit">
                    <i class="bi bi-funnel"></i>
                    Lọc
                </button>
            </form>
        </section>

        <section class="panel-card">
            <div class="table-responsive">
                <table class="audit-table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Thời gian</th>
                        <th>Người thao tác</th>
                        <th>Thao tác</th>
                        <th>Đối tượng</th>
                        <th>Dữ liệu liên quan</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($logs as $log)
                        @php
                            $actionLabel = $actionLabels[$log->action] ?? 'Thao tác hệ thống';
                            $entityLabel = $entityLabels[$log->entity_type] ?? 'Dữ liệu hệ thống';
                            $metaItems = collect($log->meta ?? [])->take(6);
                            $requestMethodLabel = $methodLabels[strtoupper((string) $log->request_method)] ?? null;
                        @endphp
                        <tr>
                            <td class="audit-id">{{ $log->id }}</td>
                            <td>
                                <div class="audit-time">
                                    <strong>{{ $log->created_at?->format('H:i:s') }}</strong>
                                    <span>{{ $log->created_at?->format('d/m/Y') }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="audit-user">
                                    <strong>{{ $log->actor_name ?: ($log->user?->name ?? 'Hệ thống') }}</strong>
                                    <span>{{ $log->actor_email ?: ($log->user?->email ?? 'Tự động') }}</span>
                                    <div class="audit-context">
                                        <span>
                                            <i class="bi bi-globe2"></i>
                                            {{ $log->ip_address ?: 'Không ghi nhận IP' }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="audit-action">
                                    <strong><i class="bi bi-activity"></i>{{ $actionLabel }}</strong>
                                    @if ($log->request_method || $log->request_url)
                                        <div class="audit-context">
                                            @if ($requestMethodLabel)
                                                <span>
                                                    <i class="bi bi-arrow-right-circle"></i>
                                                    {{ $requestMethodLabel }} ({{ strtoupper((string) $log->request_method) }})
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="audit-entity">{{ $entityLabel }} #{{ $log->entity_id }}</span>
                            </td>
                            <td>
                                @if ($metaItems->isNotEmpty())
                                    <div class="audit-meta">
                                        @foreach ($metaItems as $key => $value)
                                            <span>
                                                <b>{{ $metaLabels[$key] ?? 'Thông tin khác' }}:</b>
                                                {{ \Illuminate\Support\Str::limit($formatMetaValue($key, $value), 80) }}
                                            </span>
                                        @endforeach
                                        @if (count($log->meta ?? []) > 6)
                                            <span>+{{ count($log->meta ?? []) - 6 }} dữ liệu khác</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-secondary">Không có dữ liệu thêm</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="audit-empty">Chưa có nhật ký hệ thống nào.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="audit-pagination">
                {{ $logs->links() }}
            </div>
        </section>
    </div>
@endsection
