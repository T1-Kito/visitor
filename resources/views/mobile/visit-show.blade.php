@extends('layouts.mobile')

@section('title', 'Chi tiết lịch hẹn')

@push('styles')
<style>
.mv-detail{display:grid;gap:14px}.mv-summary{overflow:hidden;border:1px solid #e0e9f2;border-radius:22px;background:#fff;box-shadow:0 10px 28px rgba(21,34,54,.05)}.mv-summary-top{display:flex;align-items:flex-start;justify-content:space-between;gap:12px;padding:16px;border-bottom:1px solid #edf2f7}.mv-summary-code{margin:0;color:#71839b;font-size:.74rem}.mv-summary-code strong{display:block;margin-top:3px;color:#14263d;font-size:1rem;font-weight:600}.mv-status{display:inline-flex;align-items:center;gap:6px;padding:7px 10px;border-radius:999px;font-size:.72rem;font-weight:500;white-space:nowrap}.mv-status.pending{color:#a86600;background:#fff3d6}.mv-status.approved{color:#087d52;background:#e7f9ef}.mv-status.checked_in{color:#0868b9;background:#e8f4ff}.mv-status.checked_out{color:#56677c;background:#edf2f7}.mv-status.rejected,.mv-status.cancelled{color:#c03645;background:#fff0f1}.mv-person{display:flex;align-items:center;gap:12px;padding:16px}.mv-person .m-avatar{width:54px;height:54px;flex:0 0 54px;font-size:1.1rem}.mv-person h2{margin:0;color:#14263d;font-size:1.05rem;font-weight:600}.mv-person p{margin:4px 0 0;color:#71839b;font-size:.76rem}.mv-qr{display:grid;grid-template-columns:132px minmax(0,1fr);align-items:center;gap:14px;padding:15px;border:1px solid #dce8f5;border-radius:20px;background:linear-gradient(145deg,#fafdff,#f0f7ff)}.mv-qr-image{width:132px;height:132px;display:grid;place-items:center;padding:8px;border:1px solid #dce8f5;border-radius:16px;background:#fff}.mv-qr-image svg{width:100%;height:100%;display:block}.mv-qr-copy h2{margin:0;color:#14263d;font-size:.9rem;font-weight:600}.mv-qr-copy p{margin:5px 0 10px;color:#71839b;font-size:.72rem;line-height:1.45}.mv-token{width:100%;display:flex;align-items:center;justify-content:space-between;gap:8px;padding:9px 10px;border:1px solid #cfe0f2;border-radius:12px;background:#fff;color:#156bd6;font-size:.76rem}.mv-token strong{overflow:hidden;text-overflow:ellipsis;font-weight:500}.mv-qr-note{grid-column:1/-1;display:flex;align-items:flex-start;gap:7px;color:#71839b;font-size:.7rem;line-height:1.4}.mv-qr-note i{color:#1672d3}.mv-qr-empty{display:flex;align-items:center;gap:12px;padding:15px;border:1px solid #f0dfb0;border-radius:18px;background:#fff9e9;color:#7d6222}.mv-qr-empty i{font-size:1.45rem}.mv-qr-empty strong{display:block;font-size:.84rem;font-weight:600}.mv-qr-empty span{display:block;margin-top:3px;font-size:.72rem}.mv-panel{overflow:hidden;border:1px solid #e0e9f2;border-radius:20px;background:#fff}.mv-panel-title{display:flex;align-items:center;gap:8px;padding:13px 15px;border-bottom:1px solid #edf2f7;color:#14263d}.mv-panel-title i{color:#1672d3}.mv-panel-title h2{margin:0;font-size:.86rem;font-weight:600}.mv-list{display:grid}.mv-row{display:grid;grid-template-columns:minmax(105px,.8fr) minmax(0,1.45fr);align-items:start;gap:12px;padding:11px 15px;border-bottom:1px solid #f0f3f7;font-size:.76rem}.mv-row:last-child{border-bottom:0}.mv-row span{color:#7a8ca4}.mv-row strong{color:#192b43;font-weight:500;text-align:right;overflow-wrap:anywhere}.mv-progress{display:grid;grid-template-columns:repeat(4,1fr);padding:16px 10px 14px}.mv-step{position:relative;display:grid;justify-items:center;gap:6px;text-align:center}.mv-step:not(:last-child)::after{content:"";position:absolute;top:15px;left:calc(50% + 16px);right:calc(-50% + 16px);height:2px;background:#dce6f0}.mv-step.done:not(:last-child)::after{background:#3dbb72}.mv-step i{position:relative;z-index:1;width:31px;height:31px;display:grid;place-items:center;border-radius:50%;background:#edf2f7;color:#91a1b5;font-size:.76rem}.mv-step.done i{background:#e4f8ec;color:#139b55}.mv-step.current i{box-shadow:0 0 0 4px rgba(22,114,211,.12);background:#e6f2ff;color:#1672d3}.mv-step strong{font-size:.66rem;font-weight:500;color:#43566f}.mv-step small{color:#8a9ab0;font-size:.58rem;line-height:1.3}.mv-history{display:grid;padding:6px 15px 12px}.mv-history-item{display:grid;grid-template-columns:28px minmax(0,1fr);gap:10px;padding:9px 0}.mv-history-item i{width:28px;height:28px;display:grid;place-items:center;border-radius:9px;background:#eef5fd;color:#1672d3;font-size:.72rem}.mv-history-item strong{display:block;color:#263950;font-size:.75rem;font-weight:500}.mv-history-item small{display:block;margin-top:3px;color:#8a9ab0;font-size:.66rem}.mv-back{width:42px;height:42px;display:grid;place-items:center;border:1px solid #dce6f0;border-radius:14px;background:#fff;color:#263950}.mv-head{display:flex;align-items:center;gap:11px}.mv-head h1{margin:0;color:#14263d;font-size:1.1rem;font-weight:600}.mv-head p{margin:3px 0 0;color:#71839b;font-size:.74rem}@media(max-width:390px){.mv-qr{grid-template-columns:112px minmax(0,1fr)}.mv-qr-image{width:112px;height:112px}.mv-row{grid-template-columns:95px minmax(0,1fr);padding-inline:12px}.mv-progress{padding-inline:4px}}
</style>
@endpush

@section('content')
@php
    $statusLabels = [
        'pending' => 'Chờ duyệt',
        'approved' => 'Đã duyệt',
        'checked_in' => 'Đang trong công ty',
        'checked_out' => 'Đã rời công ty',
        'rejected' => 'Đã từ chối',
        'cancelled' => 'Đã hủy',
    ];
    $statusIcons = [
        'pending' => 'bi-hourglass-split',
        'approved' => 'bi-check-circle',
        'checked_in' => 'bi-box-arrow-in-right',
        'checked_out' => 'bi-box-arrow-left',
        'rejected' => 'bi-x-circle',
        'cancelled' => 'bi-slash-circle',
    ];
    $actionLabels = [
        'visit.created' => 'Tạo lịch hẹn',
        'approval.approved' => 'Duyệt lịch hẹn',
        'approval.rejected' => 'Từ chối lịch hẹn',
        'visit.qr_generated' => 'Cấp mã QR',
        'visit.qr_emailed' => 'Gửi QR qua email',
        'visit.checked_in' => 'Khách check-in',
        'visit.checked_out' => 'Khách check-out',
        'visit.updated' => 'Cập nhật lịch hẹn',
    ];
    $qrIsValid = $visit->qr_token && (! $visit->qr_expires_at || $visit->qr_expires_at->isFuture());
    $approvalDone = in_array($visit->status, ['approved', 'checked_in', 'checked_out'], true);
    $checkinDone = in_array($visit->status, ['checked_in', 'checked_out'], true);
    $checkoutDone = $visit->status === 'checked_out';
    $badgeNumber = $visit->activeBadge?->badge_no;
@endphp

<div class="mv-detail">
    <section class="mv-head">
        <a class="mv-back" href="{{ url()->previous() }}" aria-label="Quay lại"><i class="bi bi-chevron-left"></i></a>
        <div>
            <h1>Chi tiết lịch hẹn</h1>
            <p>Theo dõi thông tin và tiến trình khách ra/vào.</p>
        </div>
    </section>

    <section class="mv-summary">
        <div class="mv-summary-top">
            <p class="mv-summary-code">Mã lịch hẹn<strong>{{ $visit->code }}</strong></p>
            <span class="mv-status {{ $visit->status }}">
                <i class="bi {{ $statusIcons[$visit->status] ?? 'bi-info-circle' }}"></i>
                {{ $statusLabels[$visit->status] ?? $visit->status }}
            </span>
        </div>
        <div class="mv-person">
            <span class="m-avatar">{{ mb_strtoupper(mb_substr($visit->visitor?->full_name ?? '-', 0, 1)) }}</span>
            <div>
                <h2>{{ $visit->visitor?->full_name ?? '-' }}</h2>
                <p>{{ $visit->visitor?->company ?? 'Chưa có thông tin công ty' }}</p>
            </div>
        </div>
    </section>

    @if ($visit->qr_token)
        <section class="mv-qr">
            <div class="mv-qr-image">
                {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(150)->margin(1)->errorCorrection('M')->generate($visit->qr_token) !!}
            </div>
            <div class="mv-qr-copy">
                <h2>Mã QR ra/vào</h2>
                <p>{{ $qrIsValid ? 'Sẵn sàng sử dụng tại kiosk hoặc quầy lễ tân.' : 'Mã QR đã hết hạn, cần cấp lại trước khi sử dụng.' }}</p>
                <button class="mv-token" type="button" data-copy-token="{{ $visit->qr_token }}">
                    <strong>{{ $visit->qr_token }}</strong>
                    <i class="bi bi-copy"></i>
                </button>
            </div>
            <div class="mv-qr-note">
                <i class="bi bi-clock"></i>
                <span>Hiệu lực đến: {{ $visit->qr_expires_at?->format('H:i - d/m/Y') ?? 'Không giới hạn' }}</span>
            </div>
        </section>
    @else
        <section class="mv-qr-empty">
            <i class="bi bi-qr-code"></i>
            <div><strong>Chưa có mã QR</strong><span>Mã QR sẽ sẵn sàng theo trạng thái xử lý của lịch hẹn.</span></div>
        </section>
    @endif

    <section class="mv-panel">
        <div class="mv-panel-title"><i class="bi bi-diagram-3"></i><h2>Tiến trình ra/vào</h2></div>
        <div class="mv-progress">
            <div class="mv-step done"><i class="bi bi-calendar-check"></i><strong>Tạo lịch</strong><small>{{ $visit->created_at?->format('d/m H:i') }}</small></div>
            <div class="mv-step {{ $approvalDone ? 'done' : ($visit->status === 'pending' ? 'current' : '') }}"><i class="bi bi-person-check"></i><strong>Phê duyệt</strong><small>{{ $visit->approval?->acted_at?->format('d/m H:i') ?? 'Đang chờ' }}</small></div>
            <div class="mv-step {{ $checkinDone ? 'done' : ($visit->status === 'approved' ? 'current' : '') }}"><i class="bi bi-box-arrow-in-right"></i><strong>Check-in</strong><small>{{ $visit->actual_checkin_at?->format('d/m H:i') ?? 'Chưa vào' }}</small></div>
            <div class="mv-step {{ $checkoutDone ? 'done' : ($visit->status === 'checked_in' ? 'current' : '') }}"><i class="bi bi-box-arrow-left"></i><strong>Check-out</strong><small>{{ $visit->actual_checkout_at?->format('d/m H:i') ?? 'Chưa ra' }}</small></div>
        </div>
    </section>

    <section class="mv-panel">
        <div class="mv-panel-title"><i class="bi bi-person"></i><h2>Thông tin khách</h2></div>
        <div class="mv-list">
            <div class="mv-row"><span>Số điện thoại</span><strong>{{ $visit->visitor?->phone ?? '-' }}</strong></div>
            <div class="mv-row"><span>Email</span><strong>{{ $visit->visitor?->email ?? '-' }}</strong></div>
            <div class="mv-row"><span>Công ty</span><strong>{{ $visit->visitor?->company ?? '-' }}</strong></div>
            @if ($visit->visitor?->identity_no)
                <div class="mv-row"><span>CCCD</span><strong>{{ $visit->visitor->identity_no }}</strong></div>
                <div class="mv-row"><span>Ngày cấp</span><strong>{{ $visit->visitor->identity_issued_date?->format('d/m/Y') ?? '-' }}</strong></div>
                <div class="mv-row"><span>Nơi cấp</span><strong>{{ $visit->visitor->identity_issued_place ?? '-' }}</strong></div>
            @endif
        </div>
    </section>

    <section class="mv-panel">
        <div class="mv-panel-title"><i class="bi bi-calendar2-week"></i><h2>Thông tin lịch hẹn</h2></div>
        <div class="mv-list">
            <div class="mv-row"><span>Người cần gặp</span><strong>{{ $visit->hostEmployee?->name ?? '-' }}</strong></div>
            <div class="mv-row"><span>Phòng ban</span><strong>{{ $visit->hostEmployee?->department?->name ?? '-' }}</strong></div>
            <div class="mv-row"><span>Giờ hẹn</span><strong>{{ $visit->scheduled_at?->format('H:i - d/m/Y') ?? '-' }}</strong></div>
            <div class="mv-row"><span>Dự kiến ra</span><strong>{{ $visit->expected_checkout_at?->format('H:i - d/m/Y') ?? '-' }}</strong></div>
            <div class="mv-row"><span>Mục đích</span><strong>{{ $visit->purpose ?: '-' }}</strong></div>
            <div class="mv-row"><span>Khu vực</span><strong>{{ $visit->access_zone ?: '-' }}</strong></div>
            @if ($badgeNumber)
                <div class="mv-row"><span>Thẻ ra/vào</span><strong>{{ $badgeNumber }}</strong></div>
            @endif
            @if ($visit->rejection_reason)
                <div class="mv-row"><span>Lý do từ chối</span><strong>{{ $visit->rejection_reason }}</strong></div>
            @endif
        </div>
    </section>

    <section class="mv-panel">
        <div class="mv-panel-title"><i class="bi bi-clock-history"></i><h2>Lịch sử xử lý</h2></div>
        <div class="mv-history">
            @forelse ($activityLogs->take(8) as $log)
                <div class="mv-history-item">
                    <i class="bi bi-check2"></i>
                    <div>
                        <strong>{{ $actionLabels[$log->action] ?? str_replace(['.', '_'], [' - ', ' '], $log->action) }}</strong>
                        <small>{{ $log->created_at?->format('H:i - d/m/Y') }}</small>
                    </div>
                </div>
            @empty
                <div class="m-empty"><i class="bi bi-clock-history"></i><span>Chưa có lịch sử xử lý.</span></div>
            @endforelse
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('[data-copy-token]').forEach((button) => {
    button.addEventListener('click', async () => {
        try {
            await navigator.clipboard.writeText(button.dataset.copyToken);
            const icon = button.querySelector('i');
            icon.className = 'bi bi-check2';
            window.setTimeout(() => {
                icon.className = 'bi bi-copy';
            }, 1400);
        } catch (_) {
            // Clipboard may be unavailable in embedded browsers.
        }
    });
});
</script>
@endpush
