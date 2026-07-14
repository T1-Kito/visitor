@extends('layouts.admin')

@section('title', 'Thông báo | Quản lý khách')
@section('page_title', 'Trung tâm thông báo')
@section('page_subtitle', 'Theo dõi thông báo từ lịch hẹn, phê duyệt, check-in/check-out và cảnh báo')

@push('styles')
    <style>
        .notification-toolbar {
            padding: 14px 18px;
        }

        .notification-toolbar-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 12px;
        }

        .notification-toolbar h3 {
            margin: 0 0 2px;
            font-size: 17px;
            font-weight: 600;
        }

        .notification-toolbar p {
            margin: 0;
            color: #718096;
            font-size: 13px;
        }

        .notification-toolbar-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .notification-filter {
            display: flex;
            align-items: center;
            gap: 8px;
            width: min(100%, 520px);
        }

        .notification-filter .form-select {
            min-height: 38px;
            padding-top: 6px;
            padding-bottom: 6px;
            font-size: 14px;
        }

        .notification-toolbar .btn,
        .notification-row-action .btn {
            display: inline-flex;
            min-height: 34px;
            align-items: center;
            justify-content: center;
            gap: 5px;
            padding: 6px 11px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            white-space: nowrap;
        }

        .notification-filter .btn {
            min-width: 76px;
        }

        .notification-list-panel {
            padding: 0;
            overflow: hidden;
        }

        .notification-list {
            display: grid;
            gap: 8px;
            padding: 12px;
        }

        .notification-row {
            display: grid;
            min-height: 72px;
            grid-template-columns: 28px minmax(0, 1fr) auto;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border-width: 1px;
            border-style: solid;
            border-radius: 10px;
        }

        .notification-row-icon {
            align-self: start;
            padding-top: 2px;
            font-size: 18px;
            line-height: 1;
            text-align: center;
        }

        .notification-row-content {
            min-width: 0;
        }

        .notification-row-title {
            display: flex;
            min-width: 0;
            align-items: center;
            gap: 8px;
        }

        .notification-row-title h3 {
            overflow: hidden;
            margin: 0;
            font-size: 14px;
            font-weight: 600;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .notification-row .status-badge {
            flex: 0 0 auto;
            padding: 3px 8px;
            font-size: 11px;
        }

        .notification-row-message {
            overflow: hidden;
            margin: 3px 0 1px;
            font-size: 13px;
            line-height: 1.35;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .notification-row-meta {
            display: block;
            overflow: hidden;
            color: #718096;
            font-size: 12px;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .notification-pagination {
            padding: 0 12px 12px;
        }

        @media (max-width: 767.98px) {
            .notification-toolbar-head,
            .notification-toolbar-actions,
            .notification-filter {
                align-items: stretch;
                flex-direction: column;
            }

            .notification-toolbar-actions,
            .notification-filter {
                width: 100%;
            }

            .notification-row {
                grid-template-columns: 24px minmax(0, 1fr);
            }

            .notification-row-action {
                grid-column: 2;
            }

            .notification-row-message,
            .notification-row-meta {
                white-space: normal;
            }
        }
    </style>
@endpush

@section('content')
    <section class="panel-card notification-toolbar mb-3">
        <div class="notification-toolbar-head">
            <div>
                <h3>Hộp thông báo</h3>
                <p>{{ $unreadCount }} thông báo chưa đọc.</p>
            </div>

            <div class="notification-toolbar-actions">
                <form class="notification-filter" method="get" action="{{ route('admin.notifications.index') }}">
                    <select class="form-select" name="status" aria-label="Lọc trạng thái thông báo">
                        <option value="all" @selected($filters['status'] === 'all')>Tất cả thông báo</option>
                        <option value="unread" @selected($filters['status'] === 'unread')>Chưa đọc</option>
                        <option value="read" @selected($filters['status'] === 'read')>Đã đọc</option>
                    </select>
                    <button class="btn btn-brand" type="submit">
                        <i class="bi bi-funnel"></i>
                        Lọc
                    </button>
                </form>

                <form method="post" action="{{ route('admin.notifications.read-all') }}">
                    @csrf
                    @method('PATCH')
                    <button class="btn btn-outline-primary" type="submit">
                        <i class="bi bi-check2-all"></i>
                        Đánh dấu đã đọc tất cả
                    </button>
                </form>
            </div>
        </div>
    </section>

    <section class="panel-card notification-list-panel">
        <div class="notification-list">
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
                        'visit.host_checkin_email_sent' => 'Đã gửi email báo người tiếp',
                        'visit.host_checkin_email_failed' => 'Lỗi gửi email báo người tiếp',
                        'watchlist.matched' => 'Trùng danh sách cảnh báo',
                        'settings.kiosk_updated' => 'Cập nhật cấu hình kiosk',
                        'settings.printer_updated' => 'Cập nhật máy in',
                        'settings.mail_updated' => 'Cập nhật email',
                        default => $notification->type,
                    };
                @endphp

                <article class="notification-row {{ $alertClass }}">
                    <i class="bi {{ $iconClass }} notification-row-icon"></i>

                    <div class="notification-row-content">
                        <div class="notification-row-title">
                            <h3>{{ $notification->title }}</h3>
                            @if ($notification->read_at === null)
                                <span class="status-badge status-pending">Chưa đọc</span>
                            @else
                                <span class="status-badge status-checked-out">Đã đọc</span>
                            @endif
                        </div>
                        <p class="notification-row-message">{{ $notification->message }}</p>
                        <small class="notification-row-meta">
                            {{ $notification->created_at?->format('d/m/Y H:i') }}
                            @if ($typeLabel)
                                - {{ $typeLabel }}
                            @endif
                        </small>
                    </div>

                    <div class="notification-row-action">
                        @if ($notification->localActionUrl())
                            @if ($notification->read_at === null)
                                <form method="post" action="{{ route('admin.notifications.read', $notification) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="btn btn-brand" type="submit">
                                        <i class="bi bi-box-arrow-up-right"></i>
                                        Mở và đánh dấu đã đọc
                                    </button>
                                </form>
                            @else
                                <a class="btn btn-light" href="{{ $notification->localActionUrl() }}">
                                    <i class="bi bi-box-arrow-up-right"></i>
                                    Mở chi tiết
                                </a>
                            @endif
                        @elseif ($notification->read_at === null)
                            <form method="post" action="{{ route('admin.notifications.read', $notification) }}">
                                @csrf
                                @method('PATCH')
                                <button class="btn btn-outline-primary" type="submit">
                                    <i class="bi bi-check2"></i>
                                    Đánh dấu đã đọc
                                </button>
                            </form>
                        @endif
                    
                        @if (auth()->user()?->hasPermission('notifications.delete'))
                        <form method="post" action="{{ route('admin.notifications.destroy', $notification) }}" onsubmit="return confirm('Xóa thông báo này?')" data-disable-on-submit>
                            @csrf
                            @method('delete')
                            <button class="btn btn-outline-danger" type="submit">
                                <i class="bi bi-trash"></i>
                                Xóa
                            </button>
                        </form>
                        @endif
                    </div>
                </article>
            @empty
                <div class="alert alert-success mb-0">
                    Không có thông báo nào theo bộ lọc hiện tại.
                </div>
            @endforelse
        </div>

        @if ($notifications->hasPages())
            <div class="notification-pagination">
                {{ $notifications->links() }}
            </div>
        @endif
    </section>
@endsection
