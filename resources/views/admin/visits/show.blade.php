@extends('layouts.admin')

@section('title', $visit->code.' | Chi tiết lịch hẹn')
@section('page_title', 'Chi tiết lịch hẹn')
@section('page_subtitle', 'Theo dõi hồ sơ khách, QR và tiến trình xử lý')

@push('styles')
<style>
.visit-app{display:grid;gap:.9rem;color:#10233d}.va-top{display:flex;align-items:center;justify-content:space-between;gap:1rem}.va-back{display:inline-flex;align-items:center;gap:.35rem;color:#526b87;text-decoration:none;font-size:.82rem;font-weight:500}.va-actions{display:flex;align-items:center;justify-content:flex-end;gap:.5rem;flex-wrap:wrap}.va-btn{min-height:40px;display:inline-flex;align-items:center;justify-content:center;gap:.42rem;padding:.55rem .85rem;border:1px solid #dbe7f4;border-radius:13px;background:#fff;color:#2c4967;font-size:.82rem;font-weight:500;text-decoration:none}.va-btn.primary{border:0;color:#fff;background:linear-gradient(135deg,#1976d2,#11a9c7);box-shadow:0 12px 24px rgba(20,107,215,.13)}.va-btn.success{border:0;color:#fff;background:linear-gradient(135deg,#16a34a,#22c55e)}.va-btn.danger{border-color:#fecaca;background:#fff7f7;color:#dc2626}.va-btn.soft{background:#f8fbff}.va-btn:disabled,.va-btn[aria-disabled=true]{opacity:.55;pointer-events:none}.va-hero{display:grid;grid-template-columns:1fr auto;gap:1rem;align-items:center;padding:1rem;border:1px solid #e2edf8;border-radius:22px;background:rgba(255,255,255,.88);box-shadow:0 14px 34px rgba(17,39,68,.05)}.va-person{display:flex;align-items:center;gap:.9rem;min-width:0}.va-avatar{width:58px;height:58px;display:grid;place-items:center;border-radius:18px;background:#e9f2ff;color:#1976d2;font-size:1.25rem;font-weight:600}.va-title{min-width:0}.va-title h1{margin:0;color:#10233d;font-size:1.22rem;font-weight:600;letter-spacing:0}.va-meta{display:flex;align-items:center;gap:.45rem;flex-wrap:wrap;margin-top:.28rem;color:#6f839f;font-size:.82rem}.va-code{color:#1976d2;font-weight:600}.va-summary{display:grid;grid-template-columns:repeat(3,minmax(130px,1fr));gap:.5rem;min-width:460px}.va-summary div{padding:.62rem .75rem;border:1px solid #edf3fb;border-radius:14px;background:#fbfdff}.va-summary span{display:block;color:#7187a3;font-size:.68rem;font-weight:500}.va-summary strong{display:block;margin-top:.1rem;color:#10233d;font-size:.82rem;font-weight:500;overflow-wrap:anywhere}.va-grid{display:grid;grid-template-columns:minmax(0,1fr) 330px;gap:.9rem;align-items:start}.va-main{display:grid;gap:.9rem;min-width:0}.va-panel{border:1px solid #e2edf8;border-radius:20px;background:#fff;box-shadow:0 12px 30px rgba(17,39,68,.045);overflow:hidden;min-width:0}.va-panel-head{display:flex;align-items:center;justify-content:space-between;gap:.8rem;padding:.9rem 1rem;border-bottom:1px solid #eef4fb}.va-panel-head h2{margin:0;color:#10233d;font-size:.96rem;font-weight:600;letter-spacing:0}.va-panel-head p{margin:.16rem 0 0;color:#7187a3;font-size:.76rem}.va-body{padding:1rem}.va-info-sections{display:grid;gap:1rem}.va-section h3{margin:0 0 .65rem;color:#526b87;font-size:.78rem;font-weight:600}.va-info-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:.58rem}.va-info{display:grid;grid-template-columns:30px minmax(0,1fr);gap:.52rem;align-items:center;padding:.62rem;border:1px solid #edf3fb;border-radius:14px;background:#fbfdff}.va-info i{width:30px;height:30px;display:grid;place-items:center;border-radius:10px;background:#edf6ff;color:#1976d2}.va-label{display:block;color:#7187a3;font-size:.68rem;font-weight:500}.va-value{display:block;margin-top:.08rem;color:#10233d;font-size:.82rem;font-weight:500;overflow-wrap:anywhere}.va-flow{display:grid;grid-template-columns:repeat(5,minmax(96px,1fr));gap:.45rem;padding:1rem;overflow-x:auto}.va-step{position:relative;display:grid;justify-items:center;gap:.35rem;text-align:center;color:#7187a3}.va-step:before{content:"";position:absolute;top:15px;left:-50%;right:50%;height:2px;background:#dce8f5}.va-step:first-child:before{display:none}.va-step.done:before{background:#22c55e}.va-dot{width:30px;height:30px;display:grid;place-items:center;border-radius:999px;background:#f3f7fb;color:#8aa0ba;border:1px solid #dce8f5;z-index:1}.va-step.done .va-dot{background:#22c55e;color:#fff;border-color:#22c55e}.va-step strong{color:#253a54;font-size:.72rem;font-weight:600}.va-step span{font-size:.66rem;line-height:1.3}.va-log-table{width:100%;border-collapse:separate;border-spacing:0}.va-log-table th{padding:.7rem;color:#7187a3;font-size:.68rem;font-weight:600;text-transform:none;border-bottom:1px solid #eef4fb}.va-log-table td{padding:.72rem;color:#34506c;font-size:.76rem;border-bottom:1px solid #f1f5fa}.va-side{display:grid;gap:.9rem;position:sticky;top:.9rem}.va-qr-card{text-align:center}.va-qr-visual{display:grid;place-items:center;width:min(210px,100%);margin:0 auto;padding:.75rem;border:1px solid #dbe7f4;border-radius:18px;background:#fff}.va-qr-visual svg{width:100%;height:auto}.va-empty-qr{display:grid;place-items:center;min-height:188px;border:1px dashed #bdd6ee;border-radius:18px;background:#f8fbff;color:#7187a3;font-size:.84rem}.va-token{display:flex;align-items:center;justify-content:space-between;gap:.5rem;margin-top:.7rem;padding:.58rem .7rem;border-radius:13px;background:#edf5ff;color:#1976d2;font-size:.76rem;font-weight:600;word-break:break-all}.va-mini-grid{display:grid;grid-template-columns:1fr 1fr;gap:.5rem;margin-top:.65rem}.va-mini{padding:.55rem;border:1px solid #edf3fb;border-radius:13px;background:#fbfdff;text-align:left}.va-mini span{display:block;color:#7187a3;font-size:.66rem}.va-mini strong{display:block;margin-top:.08rem;color:#10233d;font-size:.74rem;font-weight:500}.va-action-stack,.va-share-actions{display:grid;gap:.5rem}.va-share-actions{grid-template-columns:1fr 1fr}.va-share-actions form{margin:0}.va-note{width:100%;min-height:94px;border:1px solid #dbe7f4;border-radius:14px;padding:.72rem;color:#10233d;font-size:.86rem;resize:vertical}.va-contact{display:grid;gap:.5rem}.va-contact-row{display:flex;align-items:center;gap:.5rem;color:#34506c;font-size:.8rem;overflow-wrap:anywhere}.va-contact-row i{color:#1976d2}.va-attachment{display:flex;align-items:center;justify-content:space-between;gap:.6rem;padding:.65rem;border:1px solid #edf3fb;border-radius:14px;background:#fbfdff;color:#7187a3;font-size:.8rem}.va-status-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:.65rem}.va-status-card{padding:.8rem;border:1px solid #edf3fb;border-radius:16px;background:#fff}.va-status-card h3{margin:0 0 .5rem;color:#10233d;font-size:.86rem;font-weight:600}.va-status-card div{display:flex;justify-content:space-between;gap:.6rem;color:#7187a3;font-size:.75rem}.va-status-card strong{color:#10233d;font-weight:500;text-align:right}@media(max-width:1500px){.va-grid{grid-template-columns:minmax(0,1fr) 300px}.va-summary{min-width:390px}}@media(max-width:1200px){.va-hero{grid-template-columns:1fr}.va-summary{min-width:0}.va-grid{grid-template-columns:1fr}.va-side{position:static;grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:768px){.va-top,.va-person{align-items:flex-start;flex-direction:column}.va-actions{justify-content:flex-start}.va-summary,.va-info-grid,.va-status-grid,.va-side{grid-template-columns:1fr}.va-flow{grid-template-columns:repeat(5,120px)}}
</style>
@endpush

@section('content')
@php
    $stepByStatus = ['pending' => 1, 'approved' => 2, 'checked_in' => 4, 'checked_out' => 5, 'rejected' => 2, 'cancelled' => 2];
    $currentStep = $stepByStatus[$visit->status] ?? 1;
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
    $methodText = [
        'qr' => 'Mã QR',
        'badge' => 'Thẻ tạm',
        'manual' => 'Nhập thủ công',
    ][$visit->checkin_method] ?? strtoupper((string) $visit->checkin_method);
    $printQrSvg = $visit->qr_token
        ? (string) \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(220)->margin(1)->errorCorrection('M')->generate($visit->qr_token)
        : null;
    $printTicket = [
        'code' => $visit->code,
        'qrToken' => $visit->qr_token,
        'qrSvg' => $printQrSvg,
        'visitorName' => $visit->visitor?->full_name ?? '-',
        'visitorCompany' => $visit->visitor?->company ?? '-',
        'hostName' => $visit->hostEmployee?->name ?? '-',
        'scheduledAt' => $visit->scheduled_at?->format('d/m/Y H:i') ?? '-',
        'status' => $statusText,
    ];
    $sharePhone = preg_replace('/\D+/', '', (string) ($visit->visitor?->phone ?? ''));
    $zaloPhone = $sharePhone && str_starts_with($sharePhone, '0') ? '84'.substr($sharePhone, 1) : $sharePhone;
    $shareEmail = trim((string) ($visit->visitor?->email ?? ''));
    $shareStatusUrl = route('kiosk.checkin.status', $visit);
    $shareMessage = implode("\n", array_filter([
        'VMS KIOSK gửi thông tin lịch hẹn của bạn:',
        'Mã lịch hẹn: '.$visit->code,
        $visit->qr_token ? 'Mã QR/check-in: '.$visit->qr_token : null,
        'Khách: '.($visit->visitor?->full_name ?? '-'),
        'Công ty: '.($visit->visitor?->company ?? '-'),
        'Người tiếp: '.($visit->hostEmployee?->name ?? '-'),
        'Giờ hẹn: '.($visit->scheduled_at?->format('d/m/Y H:i') ?? '-'),
        'Link tra cứu/check-in: '.$shareStatusUrl,
        'Vui lòng xuất trình mã này tại quầy lễ tân.',
    ]));
    $zaloUrl = $zaloPhone ? 'https://zalo.me/'.$zaloPhone : null;
    $shareQrPayload = [
        'message' => $shareMessage,
        'zaloUrl' => $zaloUrl,
    ];
@endphp

<div class="visit-app">
    <div class="va-top">
        <a class="va-back" href="{{ route('admin.visits.index') }}"><i class="bi bi-arrow-left"></i>Quay lại danh sách</a>
        <div class="va-actions">
            @if ($canEdit)
                <a class="va-btn primary" href="{{ route('admin.visits.edit', $visit) }}"><i class="bi bi-pencil-square"></i>Sửa lịch hẹn</a>
            @endif
            @if ($canCancel)
                <form action="{{ route('admin.visits.cancel', $visit) }}" method="post">
                    @csrf
                    <input type="hidden" name="reason" value="Hủy lịch hẹn từ trang chi tiết.">
                    <button class="va-btn danger" type="submit"><i class="bi bi-x-circle"></i>Hủy lịch</button>
                </form>
            @endif
        </div>
    </div>

    <section class="va-hero">
        <div class="va-person">
            <div class="va-avatar">{{ strtoupper(mb_substr($visit->visitor?->full_name ?? 'K', 0, 1)) }}</div>
            <div class="va-title">
                <h1>{{ $visit->visitor?->full_name ?? 'Khách' }}</h1>
                <div class="va-meta">
                    <span class="va-code">{{ $visit->code }}</span>
                    <span>·</span>
                    <span>{{ $visit->visitor?->company ?? 'Khách vãng lai' }}</span>
                    <x-status-badge :status="$visit->status" />
                </div>
            </div>
        </div>
        <div class="va-summary">
            <div><span>Người tiếp</span><strong>{{ $visit->hostEmployee?->name ?? '-' }}</strong></div>
            <div><span>Giờ hẹn</span><strong>{{ $visit->scheduled_at?->format('d/m/Y H:i') ?? '-' }}</strong></div>
            <div><span>Phòng ban</span><strong>{{ $visit->hostEmployee?->department?->name ?? '-' }}</strong></div>
        </div>
    </section>

    <div class="va-grid">
        <main class="va-main">
            <section class="va-panel">
                <div class="va-panel-head">
                    <div>
                        <h2>Thông tin chính</h2>
                        <p>Tạo lúc {{ $visit->created_at?->format('d/m/Y H:i') ?? '-' }} bởi {{ auth()->user()?->name ?? 'Hệ thống' }}</p>
                    </div>
                </div>
                <div class="va-body va-info-sections">
                    <div class="va-section">
                        <h3>Khách</h3>
                        <div class="va-info-grid">
                            <div class="va-info"><i class="bi bi-person"></i><div><span class="va-label">Họ tên</span><span class="va-value">{{ $visit->visitor?->full_name ?? '-' }}</span></div></div>
                            <div class="va-info"><i class="bi bi-building"></i><div><span class="va-label">Công ty</span><span class="va-value">{{ $visit->visitor?->company ?? '-' }}</span></div></div>
                            <div class="va-info"><i class="bi bi-telephone"></i><div><span class="va-label">Số điện thoại</span><span class="va-value">{{ $visit->visitor?->phone ?? '-' }}</span></div></div>
                            <div class="va-info"><i class="bi bi-envelope"></i><div><span class="va-label">Email</span><span class="va-value">{{ $visit->visitor?->email ?? '-' }}</span></div></div>
                            <div class="va-info"><i class="bi bi-card-text"></i><div><span class="va-label">CMND/CCCD</span><span class="va-value">{{ $visit->visitor?->identity_no ?? '-' }}</span></div></div>
                            <div class="va-info"><i class="bi bi-chat-text"></i><div><span class="va-label">Ghi chú</span><span class="va-value">{{ $visit->visitor?->note ?? '-' }}</span></div></div>
                        </div>
                    </div>
                    <div class="va-section">
                        <h3>Lịch hẹn</h3>
                        <div class="va-info-grid">
                            <div class="va-info"><i class="bi bi-upc-scan"></i><div><span class="va-label">Mã lịch hẹn</span><span class="va-value">{{ $visit->code }}</span></div></div>
                            <div class="va-info"><i class="bi bi-card-checklist"></i><div><span class="va-label">Loại lịch hẹn</span><span class="va-value">{{ $visit->visitor?->company ? 'Đặt trước' : 'Khách vãng lai' }}</span></div></div>
                            <div class="va-info"><i class="bi bi-calendar-check"></i><div><span class="va-label">Ngày giờ vào</span><span class="va-value">{{ $visit->scheduled_at?->format('d/m/Y H:i') ?? '-' }}</span></div></div>
                            <div class="va-info"><i class="bi bi-clock-history"></i><div><span class="va-label">Dự kiến ra</span><span class="va-value">{{ $visit->expected_checkout_at?->format('d/m/Y H:i') ?? '-' }}</span></div></div>
                            <div class="va-info"><i class="bi bi-geo-alt"></i><div><span class="va-label">Khu vực</span><span class="va-value">{{ $visit->access_zone ?? '-' }}</span></div></div>
                            <div class="va-info"><i class="bi bi-qr-code-scan"></i><div><span class="va-label">Hình thức vào</span><span class="va-value">{{ $methodText }}</span></div></div>
                            <div class="va-info"><i class="bi bi-bullseye"></i><div><span class="va-label">Mục đích đến</span><span class="va-value">{{ $visit->purpose ?? '-' }}</span></div></div>
                            <div class="va-info"><i class="bi bi-person-workspace"></i><div><span class="va-label">Người tiếp</span><span class="va-value">{{ $visit->hostEmployee?->name ?? '-' }}</span></div></div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="va-panel">
                <div class="va-panel-head"><h2>Tiến trình</h2></div>
                <div class="va-flow">
                    <div class="va-step done"><div class="va-dot"><i class="bi bi-check"></i></div><strong>Tạo lịch</strong><span>{{ $visit->created_at?->format('d/m/Y H:i') ?? '-' }}</span></div>
                    <div class="va-step {{ $visit->qr_token ? 'done' : '' }}"><div class="va-dot"><i class="bi bi-qr-code"></i></div><strong>Sinh QR</strong><span>{{ $visit->qr_token ? ($visit->qr_expires_at?->format('d/m/Y H:i') ?? 'Đã cấp') : 'Chưa cấp' }}</span></div>
                    <div class="va-step {{ $currentStep >= 2 ? 'done' : '' }}"><div class="va-dot"><i class="bi bi-person-check"></i></div><strong>Phê duyệt</strong><span>{{ $visit->approval?->acted_at?->format('d/m/Y H:i') ?? 'Chờ xử lý' }}</span></div>
                    <div class="va-step {{ $currentStep >= 4 ? 'done' : '' }}"><div class="va-dot"><i class="bi bi-box-arrow-in-right"></i></div><strong>Khách vào</strong><span>{{ $visit->actual_checkin_at?->format('d/m/Y H:i') ?? 'Chưa vào' }}</span></div>
                    <div class="va-step {{ $currentStep >= 5 ? 'done' : '' }}"><div class="va-dot"><i class="bi bi-box-arrow-left"></i></div><strong>Khách ra</strong><span>{{ $visit->actual_checkout_at?->format('d/m/Y H:i') ?? 'Chưa ra' }}</span></div>
                </div>
            </section>

            <div class="va-status-grid">
                <section class="va-status-card">
                    <h3>Khách vào</h3>
                    <div><span>Thời gian</span><strong>{{ $visit->actual_checkin_at?->format('d/m/Y H:i') ?? 'Chưa vào' }}</strong></div>
                    <div><span>Trạng thái</span><strong>{{ $visit->actual_checkin_at ? 'Đã vào' : 'Chưa vào' }}</strong></div>
                </section>
                <section class="va-status-card">
                    <h3>Khách ra</h3>
                    <div><span>Thời gian</span><strong>{{ $visit->actual_checkout_at?->format('d/m/Y H:i') ?? 'Chưa ra' }}</strong></div>
                    <div><span>Trạng thái</span><strong>{{ $visit->actual_checkout_at ? 'Đã ra' : 'Chưa ra' }}</strong></div>
                </section>
                <section class="va-status-card">
                    <h3>Thẻ ra vào</h3>
                    <div><span>Badge ID</span><strong>{{ $visit->activeBadge?->badge_no ?? $visit->badges->first()?->badge_no ?? '-' }}</strong></div>
                    <div><span>Trạng thái</span><strong>{{ $visit->activeBadge?->status ?? $visit->badges->first()?->status ?? '-' }}</strong></div>
                </section>
            </div>

            <section class="va-panel">
                <div class="va-panel-head"><h2>Nhật ký hoạt động</h2></div>
                <div class="table-responsive">
                    <table class="va-log-table">
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

        <aside class="va-side">
            <section class="va-panel va-qr-card">
                <div class="va-panel-head">
                    <h2>QR & thao tác</h2>
                    @if ($qrIsValid)
                        <span class="status-badge status-approved">Hiệu lực</span>
                    @else
                        <span class="status-badge status-pending">Chưa có QR</span>
                    @endif
                </div>
                <div class="va-body">
                    @if ($visit->qr_token)
                        <div class="va-qr-visual">
                            {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(210)->margin(1)->errorCorrection('M')->generate($visit->qr_token) !!}
                        </div>
                        <div class="va-token"><span>{{ $visit->qr_token }}</span><i class="bi bi-copy"></i></div>
                    @else
                        <div class="va-empty-qr"><div><i class="bi bi-qr-code d-block fs-1 mb-2"></i>QR sẽ được sinh khi lịch sẵn sàng.</div></div>
                    @endif

                    <div class="va-mini-grid">
                        <div class="va-mini"><span>Hiệu lực từ</span><strong>{{ $visit->scheduled_at?->format('d/m/Y H:i') ?? '-' }}</strong></div>
                        <div class="va-mini"><span>Hết hạn</span><strong>{{ $visit->qr_expires_at?->format('d/m/Y H:i') ?? '-' }}</strong></div>
                    </div>

                    <div class="va-action-stack mt-3">
                        <button class="va-btn soft" type="button" onclick="printAdminQrTicket()" @disabled(! $visit->qr_token)><i class="bi bi-printer"></i>In QR</button>
                        @if ($canGenerateQr)
                            <form action="{{ route('admin.visits.generate-qr', $visit) }}" method="post">@csrf<button class="va-btn primary w-100" type="submit"><i class="bi bi-qr-code"></i>Sinh lại QR</button></form>
                        @else
                            <button class="va-btn soft" type="button" disabled><i class="bi bi-qr-code"></i>Chưa thể sinh</button>
                        @endif
                    </div>

                    @if ($visit->qr_token)
                        <div class="va-share-actions mt-2">
                            @if ($shareEmail)
                                <form action="{{ route('admin.visits.send-qr-email', $visit) }}" method="post">@csrf<button class="va-btn danger w-100" type="submit"><i class="bi bi-envelope"></i>Gmail</button></form>
                            @else
                                <span class="va-btn danger" aria-disabled="true"><i class="bi bi-envelope"></i>Gmail</span>
                            @endif
                            <button class="va-btn primary" id="adminQrCopyShareBtn" type="button" onclick="copyAdminQrMessage()"><i class="bi bi-clipboard"></i>Sao chép</button>
                        </div>
                    @endif
                </div>
            </section>

            <section class="va-panel">
                <div class="va-panel-head"><h2>Xử lý</h2></div>
                <div class="va-body va-action-stack">
                    @if ($visit->status === 'pending')
                        <form action="{{ route('admin.approvals.approve', $visit) }}" method="post">@csrf<button class="va-btn success w-100" type="submit"><i class="bi bi-check-circle"></i>Duyệt lịch</button></form>
                        <form action="{{ route('admin.approvals.reject', $visit) }}" method="post">@csrf<input type="hidden" name="reason" value="Từ chối từ trang chi tiết."><button class="va-btn danger w-100" type="submit"><i class="bi bi-x-circle"></i>Từ chối</button></form>
                    @elseif ($visit->status === 'approved')
                        <span class="va-btn soft w-100"><i class="bi bi-check-circle"></i>Đã duyệt</span>
                        <form action="{{ route('admin.approvals.reject', $visit) }}" method="post">@csrf<input type="hidden" name="reason" value="Từ chối từ trang chi tiết."><button class="va-btn danger w-100" type="submit"><i class="bi bi-x-circle"></i>Từ chối</button></form>
                        <form action="{{ route('admin.checkin.confirm', $visit) }}" method="post">@csrf<button class="va-btn primary w-100" type="submit"><i class="bi bi-box-arrow-in-right"></i>Cho khách vào</button></form>
                    @elseif ($visit->status === 'checked_in')
                        <form action="{{ route('admin.checkout.confirm', $visit) }}" method="post">@csrf<button class="va-btn danger w-100" type="submit"><i class="bi bi-box-arrow-left"></i>Cho khách ra</button></form>
                    @else
                        <span class="va-btn soft w-100">{{ $statusText }}</span>
                    @endif
                </div>
            </section>

            <section class="va-panel">
                <div class="va-panel-head"><h2>Ghi chú & liên hệ</h2></div>
                <div class="va-body">
                    <textarea class="va-note" placeholder="Nhập ghi chú...">{{ $visit->approval?->note ?? $visit->rejection_reason }}</textarea>
                    <button class="va-btn primary w-100 mt-2" type="button" disabled><i class="bi bi-save"></i>Lưu ghi chú</button>
                    <div class="va-contact mt-3">
                        <div class="va-contact-row"><i class="bi bi-telephone"></i>{{ $visit->visitor?->phone ?? '-' }}</div>
                        <div class="va-contact-row"><i class="bi bi-envelope"></i>{{ $visit->visitor?->email ?? '-' }}</div>
                    </div>
                </div>
            </section>

            <section class="va-panel">
                <div class="va-panel-head"><h2>Tài liệu</h2></div>
                <div class="va-body">
                    <div class="va-attachment"><span><i class="bi bi-file-earmark"></i> Chưa có tài liệu</span></div>
                    <button class="va-btn soft w-100 mt-2" type="button" disabled><i class="bi bi-plus"></i>Thêm tài liệu</button>
                </div>
            </section>
        </aside>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const adminQrTicket = @json($printTicket, JSON_UNESCAPED_UNICODE);
    const adminQrShare = @json($shareQrPayload, JSON_UNESCAPED_UNICODE);

    async function copyAdminQrMessage() {
        if (!adminQrShare.message) {
            return false;
        }

        try {
            await navigator.clipboard.writeText(adminQrShare.message);
        } catch (error) {
            const helper = document.createElement('textarea');
            helper.value = adminQrShare.message;
            helper.setAttribute('readonly', '');
            helper.style.position = 'fixed';
            helper.style.left = '-9999px';
            document.body.appendChild(helper);
            helper.select();
            document.execCommand('copy');
            helper.remove();
        }

        const button = document.getElementById('adminQrCopyShareBtn');
        if (button) {
            const oldText = button.innerHTML;
            button.innerHTML = '<i class="bi bi-check-circle"></i>Đã sao chép';
            window.setTimeout(() => {
                button.innerHTML = oldText;
            }, 1800);
        }

        return true;
    }

    async function printAdminQrTicket() {
        if (!adminQrTicket.qrSvg) {
            return;
        }

        const bridgeUrl = localStorage.getItem('gatehouse_printer_bridge_url') || 'http://127.0.0.1:9191';

        try {
            const response = await fetch(`${bridgeUrl}/print`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                },
                body: JSON.stringify(adminQrTicket),
            });
            const payload = await response.json();

            if (response.ok && payload.ok) {
                return;
            }
        } catch (error) {
            // Nếu Printer Bridge chưa chạy thì dùng hộp thoại in của trình duyệt.
        }

        const printWindow = window.open('', '_blank', 'width=420,height=640');
        if (!printWindow) {
            alert('Trình duyệt đang chặn cửa sổ in. Vui lòng cho phép popup rồi thử lại.');
            return;
        }

        const safe = (value) => String(value ?? '-')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');

        printWindow.document.write(`
            <!doctype html>
            <html lang="vi">
            <head>
                <meta charset="utf-8">
                <title>In QR ${safe(adminQrTicket.code)}</title>
                <style>
                    @page { size: 80mm auto; margin: 2.5mm; }
                    * { box-sizing: border-box; }
                    html, body { width: 80mm; margin: 0; padding: 0; background: #fff; color: #0b1f3a; font-family: Arial, sans-serif; }
                    .ticket { width: 74mm; margin: 0 auto; text-align: center; }
                    .brand { margin-bottom: 1.2mm; font-size: 11px; font-weight: 700; text-transform: uppercase; }
                    h1 { margin: 0 0 1.2mm; font-size: 14px; line-height: 1.15; }
                    .muted { margin-bottom: 1.8mm; color: #64748b; font-size: 9px; line-height: 1.3; }
                    .qr { display: grid; place-items: center; margin: 1mm auto 1.5mm; }
                    .qr svg { width: 64mm; height: 64mm; display: block; }
                    .code { margin: 2mm 0; padding: 1.6mm; border: 1px dashed #94a3b8; border-radius: 2.5mm; font-size: 13px; font-weight: 700; }
                    .row { display: flex; justify-content: space-between; gap: 3mm; margin: 1.35mm 0; font-size: 10px; text-align: left; }
                    .row span { color: #64748b; }
                    .row strong { max-width: 42mm; text-align: right; word-break: break-word; }
                    .note { margin-top: 2mm; padding-top: 1.5mm; border-top: 1px solid #e2e8f0; color: #64748b; font-size: 8.8px; line-height: 1.3; }
                </style>
            </head>
            <body>
                <section class="ticket">
                    <div class="brand">Gatehouse Pro</div>
                    <h1>Phiếu mã QR</h1>
                    <div class="muted">Vui lòng xuất trình mã này tại quầy lễ tân.</div>
                    <div class="qr">${adminQrTicket.qrSvg}</div>
                    <div class="code">${safe(adminQrTicket.code)}</div>
                    <div class="row"><span>Khách</span><strong>${safe(adminQrTicket.visitorName)}</strong></div>
                    <div class="row"><span>Công ty</span><strong>${safe(adminQrTicket.visitorCompany)}</strong></div>
                    <div class="row"><span>Người tiếp</span><strong>${safe(adminQrTicket.hostName)}</strong></div>
                    <div class="row"><span>Giờ hẹn</span><strong>${safe(adminQrTicket.scheduledAt)}</strong></div>
                    <div class="row"><span>Trạng thái</span><strong>${safe(adminQrTicket.status)}</strong></div>
                    <div class="note">Mã QR chỉ dùng cho lượt khách này. Không chia sẻ mã cho người khác.</div>
                </section>
                <script>
                    window.onload = () => {
                        window.focus();
                        window.print();
                    };
                <\/script>
            </body>
            </html>
        `);
        printWindow.document.close();
    }
</script>
@endpush
