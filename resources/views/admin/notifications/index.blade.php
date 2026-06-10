@extends('layouts.admin')

@section('title', 'Thông báo | Quản lý khách')
@section('page_title', 'Trung tâm thông báo')
@section('page_subtitle', 'Theo dõi thông báo từ lịch hẹn, phê duyệt, check-in/check-out và cảnh báo')

@section('content')
    <section class="panel-card mb-3">
        <div class="panel-header">
            <div>
                <h3>Hộp thông báo</h3>
                <p>{{ $unreadCount }} thông báo chưa đọc.</p>
            </div>
            <form method="post" action="{{ route('admin.notifications.read-all') }}">
                @csrf
                @method('PATCH')
                <button class="btn btn-outline-primary btn-sm" type="submit">
                    <i class="bi bi-check2-all"></i>
                    Đánh dấu đã đọc tất cả
                </button>
            </form>
        </div>

        <form class="row g-2" method="get" action="{{ route('admin.notifications.index') }}">
            <div class="col-md-4">
                <select class="form-select" name="status">
                    <option value="all" @selected($filters['status'] === 'all')>Tất cả thông báo</option>
                    <option value="unread" @selected($filters['status'] === 'unread')>Chưa đọc</option>
                    <option value="read" @selected($filters['status'] === 'read')>Đã đọc</option>
                </select>
            </div>
            <div class="col-md-2 d-grid">
                <button class="btn btn-brand" type="submit">Lọc</button>
            </div>
        </form>
    </section>

    <section class="panel-card">
        <div class="d-grid gap-2">
            @forelse ($notifications as $notification)
                @php
                    $alertClass = match ($notification->level) {
                        'danger' => 'border-danger-subtle bg-danger-subtle',
                        'warning' => 'border-warning-subtle bg-warning-subtle',
                        'success' => 'border-success-subtle bg-success-subtle',
                        default => 'border-light bg-white',
                    };
                    $iconClass = match ($notification->level) {
                        'danger' => 'bi-exclamation-octagon-fill text-danger',
                        'warning' => 'bi-exclamation-triangle-fill text-warning',
                        'success' => 'bi-check-circle-fill text-success',
                        default => 'bi-info-circle-fill text-primary',
                    };
                    $typeLabel = match ($notification->type) {
                        'approval.approved' => 'Lịch đã được duyệt',
                        'approval.rejected' => 'Lịch bị từ chối',
                        'kiosk.walk_in_created' => 'Khách đăng ký tại kiosk',
                        'kiosk.checked_in' => 'Khách đã vào',
                        'kiosk.checked_out' => 'Khách đã ra',
                        'visit.pending' => 'Lịch chờ duyệt',
                        'visit.created' => 'Lịch mới',
                        'visit.updated' => 'Lịch đã cập nhật',
                        'visit.qr_generated' => 'Đã sinh mã QR',
                        'visit.qr_emailed' => 'Đã gửi QR qua email',
                        'visit.qr_scanned_for_checkin' => 'Quét QR check-in',
                        'visit.qr_scanned_for_checkout' => 'Quét QR check-out',
                        'visit.checked_in' => 'Khách đã check-in',
                        'visit.checked_out' => 'Khách đã check-out',
                        'visit.host_checkin_email_sent' => 'Đã gửi email báo host',
                        'visit.host_checkin_email_failed' => 'Lỗi gửi email báo host',
                        'watchlist.matched' => 'Trùng danh sách cảnh báo',
                        'settings.kiosk_updated' => 'Cập nhật cấu hình kiosk',
                        'settings.printer_updated' => 'Cập nhật máy in',
                        'settings.mail_updated' => 'Cập nhật Gmail',
                        default => $notification->type,
                    };
                @endphp
                <div class="border rounded-4 p-3 {{ $alertClass }}">
                    <div class="d-flex flex-wrap justify-content-between gap-3">
                        <div class="d-flex gap-3">
                            <i class="bi {{ $iconClass }} fs-4"></i>
                            <div>
                                <div class="d-flex flex-wrap align-items-center gap-2">
                                    <h3 class="h6 mb-0">{{ $notification->title }}</h3>
                                    @if ($notification->read_at === null)
                                        <span class="status-badge status-pending">Chưa đọc</span>
                                    @else
                                        <span class="status-badge status-checked-out">Đã đọc</span>
                                    @endif
                                </div>
                                <p class="mb-1 mt-1">{{ $notification->message }}</p>
                                <small class="text-secondary">
                                    {{ $notification->created_at?->format('d/m/Y H:i') }}
                                    @if ($typeLabel)
                                        - {{ $typeLabel }}
                                    @endif
                                </small>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap gap-2 align-items-start">
                            @if ($notification->action_url)
                                @if ($notification->read_at === null)
                                    <form method="post" action="{{ route('admin.notifications.read', $notification) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn-sm btn-brand" type="submit">Mở và đánh dấu đã đọc</button>
                                    </form>
                                @else
                                    <a class="btn btn-sm btn-light" href="{{ $notification->action_url }}">Mở chi tiết</a>
                                @endif
                            @elseif ($notification->read_at === null)
                                <form method="post" action="{{ route('admin.notifications.read', $notification) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="btn btn-sm btn-outline-primary" type="submit">Đánh dấu đã đọc</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="alert alert-success mb-0">
                    Không có thông báo nào theo bộ lọc hiện tại.
                </div>
            @endforelse
        </div>

        <div class="mt-3">
            {{ $notifications->links() }}
        </div>
    </section>
@endsection
