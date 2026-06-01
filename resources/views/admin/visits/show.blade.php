@extends('layouts.admin')

@section('title', $visit->code.' | Chi tiết lịch hẹn')
@section('page_title', 'Chi tiết lịch hẹn')
@section('page_subtitle', 'Theo dõi thông tin khách, trạng thái phê duyệt, QR và lịch sử xử lý')

@push('styles')
<style>
.vd-page{display:grid;gap:1rem}.vd-top{display:flex;align-items:center;justify-content:space-between;gap:1rem}.vd-back{display:inline-flex;align-items:center;gap:.4rem;color:#29435f;text-decoration:none;font-size:.82rem;font-weight:900}.vd-title{display:flex;align-items:center;gap:.7rem;flex-wrap:wrap;margin-top:.45rem}.vd-title h1{margin:0;color:#0b1f3a;font-size:1.45rem;font-weight:900}.vd-code{color:#146bd7;font-size:1.18rem;font-weight:900}.vd-actions{display:flex;flex-wrap:wrap;gap:.55rem}.vd-btn{display:inline-flex;align-items:center;justify-content:center;gap:.4rem;min-height:38px;padding:.5rem .85rem;border-radius:12px;font-size:.78rem;font-weight:900;text-decoration:none;border:1px solid #d8e5f2;background:#fff;color:#29435f}.vd-btn.primary{border:0;background:linear-gradient(135deg,#146bd7,#0cb4d8);color:#fff}.vd-btn.success{border:0;background:linear-gradient(135deg,#16a34a,#22c55e);color:#fff}.vd-btn.danger{border-color:#fecaca;background:#fff7f7;color:#dc2626}.vd-btn:disabled{opacity:.55}.vd-layout{display:grid;grid-template-columns:300px minmax(0,1fr) 280px;gap:1rem;align-items:start}.vd-card{background:#fff;border:1px solid #e3edf8;border-radius:20px;box-shadow:0 14px 34px rgba(17,39,68,.05);overflow:hidden}.vd-card-head{display:flex;align-items:center;justify-content:space-between;gap:.8rem;padding:.9rem 1rem;border-bottom:1px solid #edf3fb;background:#fbfdff}.vd-card-head h3{margin:0;color:#0b1f3a;font-size:.82rem;font-weight:900;text-transform:uppercase}.vd-card-body{padding:1rem}.vd-qr-box{display:grid;gap:.8rem}.vd-qr-visual{display:grid;grid-template-columns:repeat(9,1fr);gap:3px;width:196px;height:196px;margin:auto;padding:12px;border:1px solid #d8e5f2;border-radius:16px;background:#fff}.vd-qr-cell{border-radius:2px;background:#eaf3ff}.vd-qr-cell.on{background:#0b1f3a}.vd-qr-token{display:flex;align-items:center;justify-content:space-between;gap:.6rem;padding:.65rem .75rem;border-radius:12px;background:#edf5ff;color:#146bd7;font-size:.72rem;font-weight:900;word-break:break-all}.vd-qr-meta{display:grid;grid-template-columns:1fr 1fr;gap:.55rem}.vd-mini{padding:.6rem;border:1px solid #edf3fb;border-radius:12px;background:#fbfdff}.vd-mini span{display:block;color:#7a93b0;font-size:.68rem;font-weight:800}.vd-mini strong{display:block;margin-top:.15rem;color:#0b1f3a;font-size:.76rem}.vd-qr-actions{display:grid;grid-template-columns:1fr 1fr;gap:.5rem}.vd-info-grid{display:grid;grid-template-columns:1fr 1fr;gap:1rem}.vd-info-list{display:grid;gap:.68rem}.vd-info-row{display:grid;grid-template-columns:28px minmax(105px,.62fr) minmax(130px,1fr);gap:.55rem;align-items:center}.vd-info-row i{width:28px;height:28px;display:grid;place-items:center;border-radius:9px;background:#f0f6ff;color:#146bd7}.vd-info-row span{color:#7a93b0;font-size:.73rem;font-weight:800}.vd-info-row strong{color:#0b1f3a;font-size:.8rem}.vd-timeline{display:flex;align-items:flex-start;justify-content:space-between;gap:.4rem;padding:1rem;overflow:auto}.vd-step{position:relative;display:grid;justify-items:center;gap:.45rem;min-width:112px;text-align:center}.vd-step:before{content:"";position:absolute;top:16px;left:-50%;right:50%;height:3px;background:#d8e5f2}.vd-step:first-child:before{display:none}.vd-step.done:before{background:#22c55e}.vd-step.active:before{background:#146bd7}.vd-step-dot{width:34px;height:34px;display:grid;place-items:center;border-radius:50%;background:#f1f5f9;color:#94a3b8;border:3px solid #fff;box-shadow:0 0 0 1px #d8e5f2;font-weight:900}.vd-step.done .vd-step-dot{background:#22c55e;color:#fff}.vd-step.active .vd-step-dot{background:#146bd7;color:#fff}.vd-step strong{color:#0b1f3a;font-size:.72rem}.vd-step span{color:#7a93b0;font-size:.68rem}.vd-status-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:1rem}.vd-status-card{padding:1rem;border:1px solid #e3edf8;border-radius:18px;background:#fff}.vd-status-card h4{margin:0 0 .65rem;color:#0b1f3a;font-size:.82rem;font-weight:900}.vd-status-line{display:flex;align-items:center;justify-content:space-between;gap:.8rem;color:#7a93b0;font-size:.75rem}.vd-status-line strong{color:#0b1f3a}.vd-log-table{width:100%;border-collapse:separate;border-spacing:0}.vd-log-table th{padding:.75rem;color:#6f88a4;font-size:.68rem;font-weight:900;text-transform:uppercase;border-bottom:1px solid #edf3fb}.vd-log-table td{padding:.75rem;color:#29435f;font-size:.76rem;border-bottom:1px solid #edf3fb}.vd-action-stack{display:grid;gap:.55rem}.vd-note-box{width:100%;min-height:96px;border:1px solid #d8e5f2;border-radius:14px;padding:.75rem}.vd-empty-qr{display:grid;place-items:center;min-height:196px;border:1px dashed #bfd7f3;border-radius:16px;background:#f8fbff;color:#7a93b0;text-align:center}.vd-emergency{display:grid;gap:.65rem}.vd-emergency-row{display:flex;align-items:center;gap:.55rem;color:#29435f;font-size:.78rem}.vd-emergency-row i{color:#146bd7}.vd-attachment{display:flex;align-items:center;justify-content:space-between;gap:.6rem;padding:.7rem;border:1px solid #edf3fb;border-radius:14px;background:#fbfdff;color:#7a93b0;font-size:.78rem}
@media(max-width:1400px){.vd-layout{grid-template-columns:1fr}.vd-status-grid,.vd-info-grid{grid-template-columns:1fr}}@media(max-width:768px){.vd-top{align-items:flex-start;flex-direction:column}.vd-info-row{grid-template-columns:28px 1fr}.vd-info-row strong{grid-column:2}.vd-qr-actions{grid-template-columns:1fr}}
</style>
@endpush

@section('content')
@php
    $stepByStatus = ['pending' => 1, 'approved' => 2, 'checked_in' => 4, 'checked_out' => 5, 'rejected' => 2, 'cancelled' => 2];
    $currentStep = $stepByStatus[$visit->status] ?? 1;
    $qrSeed = md5((string) ($visit->qr_token ?? $visit->code));
    $qrCells = range(0, 80);
    $qrIsValid = $visit->qr_token && (! $visit->qr_expires_at || $visit->qr_expires_at->isFuture());
    $statusText = [
        'pending' => 'Chờ duyệt',
        'approved' => 'Đã duyệt',
        'rejected' => 'Từ chối',
        'checked_in' => 'Đang trong công ty',
        'checked_out' => 'Đã rời công ty',
        'cancelled' => 'Đã hủy',
        'waiting' => 'Yêu cầu chờ',
    ][$visit->status] ?? $visit->status;
@endphp

<div class="vd-page">
    <div class="vd-top">
        <div>
            <a class="vd-back" href="{{ route('admin.visits.index') }}"><i class="bi bi-arrow-left"></i> Quay lại danh sách</a>
            <div class="vd-title">
                <h1>Chi tiết lịch hẹn</h1>
                <span class="vd-code">{{ $visit->code }}</span>
                <x-status-badge :status="$visit->status" />
            </div>
            <div class="text-secondary small mt-1">
                Tạo lúc {{ $visit->created_at?->format('d/m/Y H:i') ?? '-' }} bởi {{ auth()->user()?->name ?? 'Hệ thống' }}
            </div>
        </div>
        <div class="vd-actions">
            @if ($canEdit)
                <a class="vd-btn primary" href="{{ route('admin.visits.edit', $visit) }}"><i class="bi bi-pencil-square"></i> Sửa lịch hẹn</a>
            @endif
            @if ($canCancel)
                <form action="{{ route('admin.visits.cancel', $visit) }}" method="post">
                    @csrf
                    <input type="hidden" name="reason" value="Hủy lịch hẹn từ trang chi tiết.">
                    <button class="vd-btn danger" type="submit"><i class="bi bi-x-circle"></i> Hủy lịch hẹn</button>
                </form>
            @endif
        </div>
    </div>

    <div class="vd-layout">
        <aside class="vd-card">
            <div class="vd-card-head">
                <h3>QR check-in</h3>
                @if ($qrIsValid)
                    <span class="status-badge status-approved">Hiệu lực</span>
                @else
                    <span class="status-badge status-pending">Chưa có QR</span>
                @endif
            </div>
            <div class="vd-card-body vd-qr-box">
                @if ($visit->qr_token)
                    <div class="vd-qr-visual" aria-label="QR preview">
                        {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(196)->margin(1)->errorCorrection('M')->generate($visit->qr_token) !!}
                    </div>
                    <div class="vd-qr-token">
                        <span>{{ $visit->qr_token }}</span>
                        <i class="bi bi-copy"></i>
                    </div>
                @else
                    <div class="vd-empty-qr">
                        <div>
                            <i class="bi bi-qr-code fs-1 d-block mb-2"></i>
                            QR sẽ được sinh tự động ngay khi tạo lịch hẹn.
                        </div>
                    </div>
                @endif

                <div class="vd-qr-meta">
                    <div class="vd-mini"><span>Hiệu lực từ</span><strong>{{ $visit->scheduled_at?->format('d/m/Y H:i') ?? '-' }}</strong></div>
                    <div class="vd-mini"><span>Hết hạn</span><strong>{{ $visit->qr_expires_at?->format('d/m/Y H:i') ?? '-' }}</strong></div>
                </div>

                <div class="vd-qr-actions">
                    <button class="vd-btn" type="button" onclick="window.print()"><i class="bi bi-printer"></i> In QR</button>
                    @if ($canGenerateQr)
                        <form action="{{ route('admin.visits.generate-qr', $visit) }}" method="post">
                            @csrf
                            <button class="vd-btn primary" type="submit"><i class="bi bi-qr-code"></i> Sinh lại QR</button>
                        </form>
                    @else
                        <button class="vd-btn" type="button" disabled><i class="bi bi-qr-code"></i> Chưa thể sinh</button>
                    @endif
                </div>
            </div>
        </aside>

        <main class="vd-page">
            <div class="vd-info-grid">
                <section class="vd-card">
                    <div class="vd-card-head"><h3>Thông tin khách</h3></div>
                    <div class="vd-card-body vd-info-list">
                        <div class="vd-info-row"><i class="bi bi-person"></i><span>Họ tên</span><strong>{{ $visit->visitor?->full_name ?? '-' }}</strong></div>
                        <div class="vd-info-row"><i class="bi bi-building"></i><span>Công ty</span><strong>{{ $visit->visitor?->company ?? '-' }}</strong></div>
                        <div class="vd-info-row"><i class="bi bi-telephone"></i><span>Số điện thoại</span><strong>{{ $visit->visitor?->phone ?? '-' }}</strong></div>
                        <div class="vd-info-row"><i class="bi bi-envelope"></i><span>Email</span><strong>{{ $visit->visitor?->email ?? '-' }}</strong></div>
                        <div class="vd-info-row"><i class="bi bi-card-text"></i><span>CMND/CCCD</span><strong>{{ $visit->visitor?->identity_no ?? '-' }}</strong></div>
                        <div class="vd-info-row"><i class="bi bi-chat-text"></i><span>Ghi chú</span><strong>{{ $visit->visitor?->note ?? '-' }}</strong></div>
                    </div>
                </section>

                <section class="vd-card">
                    <div class="vd-card-head"><h3>Thông tin lịch hẹn</h3></div>
                    <div class="vd-card-body vd-info-list">
                        <div class="vd-info-row"><i class="bi bi-upc-scan"></i><span>Mã lịch hẹn</span><strong>{{ $visit->code }}</strong></div>
                        <div class="vd-info-row"><i class="bi bi-card-checklist"></i><span>Loại lịch hẹn</span><strong>{{ $visit->visitor?->company ? 'Đặt trước' : 'Khách vãng lai' }}</strong></div>
                        <div class="vd-info-row"><i class="bi bi-calendar-check"></i><span>Ngày giờ vào</span><strong>{{ $visit->scheduled_at?->format('d/m/Y H:i') ?? '-' }}</strong></div>
                        <div class="vd-info-row"><i class="bi bi-clock-history"></i><span>Ngày giờ ra dự kiến</span><strong>{{ $visit->expected_checkout_at?->format('d/m/Y H:i') ?? '-' }}</strong></div>
                        <div class="vd-info-row"><i class="bi bi-geo-alt"></i><span>Khu vực ra vào</span><strong>{{ $visit->access_zone ?? '-' }}</strong></div>
                        <div class="vd-info-row"><i class="bi bi-qr-code-scan"></i><span>Hình thức vào</span><strong>{{ $visit->checkin_method === 'qr' ? 'Mã QR' : strtoupper((string) $visit->checkin_method) }}</strong></div>
                        <div class="vd-info-row"><i class="bi bi-bullseye"></i><span>Mục đích đến</span><strong>{{ $visit->purpose ?? '-' }}</strong></div>
                        <div class="vd-info-row"><i class="bi bi-person-workspace"></i><span>Người tiếp</span><strong>{{ $visit->hostEmployee?->name ?? '-' }}</strong></div>
                        <div class="vd-info-row"><i class="bi bi-building"></i><span>Phòng ban</span><strong>{{ $visit->hostEmployee?->department?->name ?? '-' }}</strong></div>
                    </div>
                </section>
            </div>

            <section class="vd-card">
                <div class="vd-card-head"><h3>Lịch sử xử lý</h3></div>
                <div class="vd-timeline">
                    <div class="vd-step done"><div class="vd-step-dot"><i class="bi bi-check"></i></div><strong>Tạo lịch hẹn</strong><span>{{ $visit->created_at?->format('d/m/Y H:i') ?? '-' }}</span></div>
                    <div class="vd-step {{ $currentStep >= 2 ? 'done' : '' }}"><div class="vd-step-dot"><i class="bi bi-person-check"></i></div><strong>Phê duyệt</strong><span>{{ $visit->approval?->acted_at?->format('d/m/Y H:i') ?? 'Chờ xử lý' }}</span></div>
                    <div class="vd-step {{ $visit->qr_token ? 'done' : '' }}"><div class="vd-step-dot"><i class="bi bi-qr-code"></i></div><strong>Sinh QR</strong><span>{{ $visit->qr_token ? ($visit->qr_expires_at?->format('d/m/Y H:i') ?? 'Đã cấp') : 'Chưa cấp' }}</span></div>
                    <div class="vd-step {{ $currentStep >= 4 ? 'done' : '' }}"><div class="vd-step-dot"><i class="bi bi-box-arrow-in-right"></i></div><strong>Khách vào</strong><span>{{ $visit->actual_checkin_at?->format('d/m/Y H:i') ?? 'Chưa vào' }}</span></div>
                    <div class="vd-step {{ $currentStep >= 5 ? 'done' : '' }}"><div class="vd-step-dot"><i class="bi bi-box-arrow-left"></i></div><strong>Khách ra</strong><span>{{ $visit->actual_checkout_at?->format('d/m/Y H:i') ?? 'Chưa ra' }}</span></div>
                </div>
            </section>

            <div class="vd-status-grid">
                <section class="vd-status-card">
                    <h4>Khách vào</h4>
                    <div class="vd-status-line"><span>Thời gian</span><strong>{{ $visit->actual_checkin_at?->format('d/m/Y H:i') ?? 'Chưa vào' }}</strong></div>
                    <div class="vd-status-line"><span>Trạng thái</span><strong>{{ $visit->actual_checkin_at ? 'Đã vào' : 'Chưa vào' }}</strong></div>
                </section>
                <section class="vd-status-card">
                    <h4>Khách ra</h4>
                    <div class="vd-status-line"><span>Thời gian</span><strong>{{ $visit->actual_checkout_at?->format('d/m/Y H:i') ?? 'Chưa ra' }}</strong></div>
                    <div class="vd-status-line"><span>Trạng thái</span><strong>{{ $visit->actual_checkout_at ? 'Đã ra' : 'Chưa ra' }}</strong></div>
                </section>
                <section class="vd-status-card">
                    <h4>Thẻ ra vào</h4>
                    <div class="vd-status-line"><span>Badge ID</span><strong>{{ $visit->activeBadge?->badge_no ?? $visit->badges->first()?->badge_no ?? '-' }}</strong></div>
                    <div class="vd-status-line"><span>Trạng thái</span><strong>{{ $visit->activeBadge?->status ?? $visit->badges->first()?->status ?? '-' }}</strong></div>
                </section>
            </div>

            <section class="vd-card">
                <div class="vd-card-head"><h3>Nhật ký hoạt động</h3></div>
                <div class="table-responsive">
                    <table class="vd-log-table">
                        <thead><tr><th>Thời gian</th><th>Người thực hiện</th><th>Hành động</th><th>Mô tả</th></tr></thead>
                        <tbody>
                        @forelse ($activityLogs as $log)
                            <tr>
                                <td>{{ $log->created_at?->format('d/m/Y H:i:s') }}</td>
                                <td>{{ $log->user?->name ?? 'Hệ thống' }}</td>
                                <td>{{ $log->action }}</td>
                                <td>{{ $log->meta['code'] ?? $log->meta['reason'] ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-secondary">Chưa có nhật ký hoạt động.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </main>

        <aside class="vd-page">
            <section class="vd-card">
                <div class="vd-card-head"><h3>Thao tác</h3></div>
                <div class="vd-card-body vd-action-stack">
                    @if ($visit->status === 'pending')
                        <form action="{{ route('admin.approvals.approve', $visit) }}" method="post">@csrf<button class="vd-btn success w-100" type="submit"><i class="bi bi-check-circle"></i> Duyệt lịch</button></form>
                        <form action="{{ route('admin.approvals.reject', $visit) }}" method="post">@csrf<input type="hidden" name="reason" value="Từ chối từ trang chi tiết."><button class="vd-btn danger w-100" type="submit"><i class="bi bi-x-circle"></i> Từ chối</button></form>
                    @elseif ($visit->status === 'approved')
                        <span class="vd-btn w-100"><i class="bi bi-check-circle"></i> Đã duyệt</span>
                        <form action="{{ route('admin.approvals.reject', $visit) }}" method="post">@csrf<input type="hidden" name="reason" value="Từ chối từ trang chi tiết."><button class="vd-btn danger w-100" type="submit"><i class="bi bi-x-circle"></i> Từ chối</button></form>
                        <form action="{{ route('admin.checkin.confirm', $visit) }}" method="post">@csrf<button class="vd-btn primary w-100" type="submit"><i class="bi bi-box-arrow-in-right"></i> Cho khách vào</button></form>
                    @elseif ($visit->status === 'checked_in')
                        <form action="{{ route('admin.checkout.confirm', $visit) }}" method="post">@csrf<button class="vd-btn danger w-100" type="submit"><i class="bi bi-box-arrow-left"></i> Cho khách ra</button></form>
                    @else
                        <span class="vd-btn w-100">{{ $statusText }}</span>
                    @endif
                </div>
            </section>

            <section class="vd-card">
                <div class="vd-card-head"><h3>Ghi chú nội bộ</h3></div>
                <div class="vd-card-body">
                    <textarea class="vd-note-box" placeholder="Nhập ghi chú...">{{ $visit->approval?->note ?? $visit->rejection_reason }}</textarea>
                    <button class="vd-btn primary w-100 mt-2" type="button" disabled><i class="bi bi-save"></i> Lưu ghi chú</button>
                </div>
            </section>

            <section class="vd-card">
                <div class="vd-card-head"><h3>Liên hệ khẩn cấp</h3></div>
                <div class="vd-card-body vd-emergency">
                    <div class="vd-emergency-row"><i class="bi bi-telephone"></i>{{ $visit->visitor?->phone ?? '-' }}</div>
                    <div class="vd-emergency-row"><i class="bi bi-envelope"></i>{{ $visit->visitor?->email ?? '-' }}</div>
                </div>
            </section>

            <section class="vd-card">
                <div class="vd-card-head"><h3>Tài liệu đính kèm</h3></div>
                <div class="vd-card-body">
                    <div class="vd-attachment">
                        <span><i class="bi bi-file-earmark"></i> Chưa có tài liệu</span>
                    </div>
                    <button class="vd-btn w-100 mt-2" type="button" disabled><i class="bi bi-plus"></i> Thêm tài liệu</button>
                </div>
            </section>
        </aside>
    </div>
</div>
@endsection
