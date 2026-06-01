@extends('layouts.admin')

@section('title', 'Khách ra | Quản lý khách')
@section('page_title', 'Khách ra')
@section('page_subtitle', 'Nhập mã QR, mã lịch hẹn hoặc mã thẻ để làm thủ tục khách ra')

@push('styles')
<style>
.co-page{display:grid;gap:1rem}.co-top{display:grid;grid-template-columns:minmax(300px,34%) minmax(0,66%);gap:1rem;align-items:start}.co-card{background:#fff;border:1px solid #e1ebf6;border-radius:22px;box-shadow:0 14px 34px rgba(17,39,68,.07);overflow:hidden}.co-head{display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;padding:1rem 1.15rem .85rem;border-bottom:1px solid #edf3fb}.co-head h3{margin:0;color:#0b1f3a;font-family:"Plus Jakarta Sans",sans-serif;font-size:1rem;font-weight:900}.co-head p{margin:.2rem 0 0;color:#6e83a0;font-size:.78rem}.co-scan-box{padding:1rem}.co-scan-frame{position:relative;display:grid;place-items:center;height:150px;border:1px dashed #a8c9eb;border-radius:18px;background:linear-gradient(90deg,rgba(20,107,215,.055) 1px,transparent 1px),linear-gradient(rgba(20,107,215,.055) 1px,transparent 1px),#eef7ff;background-size:24px 24px}.co-scan-frame i{color:#b9d5ef;font-size:3.4rem}.co-corners:before,.co-corners:after,.co-corner-tr,.co-corner-bl{content:"";position:absolute;width:26px;height:26px;border:3px solid #146bd7}.co-corners{position:absolute;inset:0}.co-corners:before{top:12px;left:12px;border-right:0;border-bottom:0;border-radius:4px 0 0}.co-corners:after{right:12px;bottom:12px;border-left:0;border-top:0;border-radius:0 0 4px}.co-corner-tr{top:12px;right:12px;border-left:0;border-bottom:0}.co-corner-bl{left:12px;bottom:12px;border-right:0;border-top:0}.co-or{display:flex;align-items:center;gap:.75rem;margin:.85rem 0;color:#7890aa;font-size:.68rem;font-weight:900;text-transform:uppercase}.co-or:before,.co-or:after{content:"";height:1px;flex:1;background:#e7eff8}.co-form{display:grid;gap:.62rem}.co-form+.co-form{margin-top:.9rem}.co-label{margin:0;color:#0b1f3a;font-size:.84rem;font-weight:900}.co-input-wrap{position:relative}.co-input-wrap i{position:absolute;right:.8rem;top:50%;transform:translateY(-50%);color:#9aafca}.co-input-wrap input{width:100%;min-height:43px;padding:.62rem 2.25rem .62rem .82rem;border:1.5px solid #d8e5f2;border-radius:13px;color:#0b1f3a}.co-input-wrap input:focus{outline:0;border-color:#146bd7;box-shadow:0 0 0 4px rgba(20,107,215,.1)}.co-btn-blue,.co-btn-red{display:flex;align-items:center;justify-content:center;gap:.42rem;width:100%;min-height:44px;border:0;border-radius:13px;color:#fff;font-weight:900}.co-btn-blue{background:linear-gradient(135deg,#146bd7,#0cb4d8);box-shadow:0 12px 24px rgba(20,107,215,.18)}.co-btn-red{background:linear-gradient(135deg,#dc2626,#ef4444);box-shadow:0 12px 24px rgba(220,38,38,.18)}
.co-detail{min-height:360px;padding:1rem}.co-state{display:flex;align-items:center;justify-content:space-between;gap:1rem;margin-bottom:1rem;padding:.72rem .85rem;border-radius:16px;background:#ecfdf5;color:#047857;font-size:.82rem;font-weight:900}.co-profile{display:flex;gap:1rem;align-items:center;margin-bottom:1rem}.co-avatar{width:72px;height:72px;border-radius:24px;display:grid;place-items:center;background:#dbeafe;color:#1d4ed8;font-size:1.65rem;font-weight:900}.co-name{margin:0;color:#0b1f3a;font-family:"Plus Jakarta Sans",sans-serif;font-size:1.35rem;font-weight:900}.co-company{color:#5a7a99;font-size:.86rem}.co-detail-grid{display:grid;grid-template-columns:minmax(0,1fr) 280px;gap:1rem}.co-info{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:.65rem}.co-info-row{display:grid;grid-template-columns:34px 1fr;gap:.55rem;align-items:center;padding:.72rem;border:1px solid #edf3fb;border-radius:15px;background:#fbfdff}.co-info-row i{width:34px;height:34px;display:grid;place-items:center;border-radius:12px;background:#eff6ff;color:#146bd7}.co-info-row.wide{grid-column:1/-1}.co-lbl{display:block;color:#7086a1;font-size:.7rem;font-weight:900}.co-val{display:block;margin-top:.12rem;color:#0b1f3a;font-size:.84rem;font-weight:900}.co-side{display:grid;gap:.65rem}.co-side-item{padding:.82rem;border:1px solid #edf3fb;border-radius:15px;background:#fbfdff}.co-side-item span{display:block;color:#7086a1;font-size:.7rem;font-weight:900}.co-side-item strong{display:block;margin-top:.18rem;color:#0b1f3a;font-size:.95rem}.co-side-item.warning{background:#fff7ed;border-color:#fed7aa}.co-side-item.warning strong{color:#c2410c}.co-actions{display:grid;grid-template-columns:1fr 190px;gap:.65rem;margin-top:1rem}.co-btn-cancel{min-height:44px;border:1px solid #d8e5f2;border-radius:13px;background:#fff;color:#334b67;font-weight:900}.co-empty{min-height:360px;display:grid;place-items:center;text-align:center;color:#8aa0ba;padding:1.5rem}.co-empty i{font-size:3rem;color:#c5dbf2}.co-empty strong{display:block;margin:.5rem 0 .25rem;color:#526b87;font-size:1rem}.co-empty p{max-width:540px;margin:0 auto .7rem}.co-empty-hint{display:inline-flex;align-items:center;gap:.38rem;padding:.48rem .75rem;border-radius:999px;background:#eff6ff;color:#146bd7;font-size:.78rem;font-weight:900}
.co-bottom{display:grid;grid-template-columns:minmax(0,65%) minmax(300px,35%);gap:1rem;align-items:start}.co-table-head{display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:1rem 1.15rem;border-bottom:1px solid #edf3fb}.co-filter{display:flex;gap:.55rem}.co-filter .form-control,.co-filter .form-select{min-height:38px;border-color:#d8e5f2;border-radius:11px;font-size:.78rem}.co-list{display:grid}.co-row,.co-row-head{display:grid;grid-template-columns:minmax(150px,1.1fr) minmax(120px,1fr) 100px 82px 120px 118px 88px;gap:.7rem;align-items:center;padding:.74rem 1.15rem;border:0;border-bottom:1px solid #f0f5fb;background:#fff;text-align:left}.co-row-head{color:#7086a1;font-size:.67rem;font-weight:900;text-transform:uppercase}.co-row:hover{background:#f2f8ff}.co-vname{color:#0b1f3a;font-size:.83rem;font-weight:900}.co-muted{color:#7890aa;font-size:.72rem}.co-remain{color:#f97316;font-size:.76rem;font-weight:900}.co-action-btn{display:inline-flex;align-items:center;justify-content:center;padding:.34rem .65rem;border:1px solid #bfd7f3;border-radius:999px;background:#fff;color:#146bd7;font-size:.72rem;font-weight:900}.co-status{display:inline-flex;align-items:center;gap:.3rem;width:max-content;padding:.3rem .58rem;border-radius:999px;font-size:.68rem;font-weight:900}.co-status-ok{background:#dcfce7;color:#047857}.co-status-late{background:#ffedd5;color:#c2410c}.co-stat-grid{display:grid;grid-template-columns:1fr 1fr;gap:.75rem;padding:1rem}.co-stat{min-height:104px;padding:1rem;border:1px solid #edf3fb;border-radius:17px;background:#f8fbff}.co-stat.green{background:#ecfdf5}.co-stat.blue{background:#eff6ff}.co-stat.orange{background:#fff7ed}.co-stat.purple{background:#f5f3ff}.co-stat span{display:block;color:#6b7f98;font-size:.72rem}.co-stat strong{display:block;margin-top:.25rem;color:#0b1f3a;font-size:1.55rem;font-weight:900}.co-stat small{color:#6b7f98}
@media(max-width:1280px){.co-top,.co-bottom{grid-template-columns:1fr}.co-detail-grid{grid-template-columns:1fr}}@media(max-width:768px){.co-info,.co-stat-grid{grid-template-columns:1fr}.co-actions{grid-template-columns:1fr}.co-row,.co-row-head{grid-template-columns:1fr}.co-row-head{display:none}.co-filter{width:100%;flex-direction:column}}
</style>
@endpush

@section('content')
<div class="co-page">
    <div class="co-top">
        <section class="co-card">
            <div class="co-head">
                <div>
                    <h3>Nhập mã khách ra</h3>
                    <p>Camera chưa kích hoạt, vui lòng nhập mã QR, mã lịch hẹn hoặc mã thẻ thủ công.</p>
                </div>
            </div>
            <div class="co-scan-box">
                <div class="co-scan-frame">
                    <div class="co-corners"></div><div class="co-corner-tr"></div><div class="co-corner-bl"></div>
                    <i class="bi bi-qr-code"></i>
                </div>
                <div class="co-or">Nhập thủ công</div>
                <form class="co-form" action="{{ route('admin.checkout.scan-qr') }}" method="post">
                    @csrf
                    <label class="co-label">Nhập mã lịch hẹn / mã QR</label>
                    <div class="co-input-wrap">
                        <input id="checkoutCodeInput" type="text" name="qr_token" value="{{ old('qr_token') }}" placeholder="VD: VO-260529-001">
                        <i class="bi bi-upc-scan"></i>
                    </div>
                    <button class="co-btn-blue" type="submit"><i class="bi bi-search"></i> Kiểm tra thông tin</button>
                </form>
                <form class="co-form d-none" action="{{ route('admin.checkout.scan-badge') }}" method="post">
                    @csrf
                    <label class="co-label">Hoặc nhập mã thẻ</label>
                    <div class="co-input-wrap">
                        <input type="text" name="badge_no" placeholder="VD: B-001">
                        <i class="bi bi-person-badge"></i>
                    </div>
                    <button class="co-btn-blue" type="submit"><i class="bi bi-person-badge"></i> Kiểm tra thẻ</button>
                </form>
            </div>
        </section>

        <section class="co-card">
            @if ($scannedVisit)
                @php
                    $isOverstay = $scannedVisit->expected_checkout_at?->lt(now()) ?? false;
                    $remaining = '-';
                    if ($scannedVisit->expected_checkout_at) {
                        $minutes = max(0, (int) now()->diffInMinutes($scannedVisit->expected_checkout_at));
                        $remaining = $minutes < 60
                            ? $minutes.' phút'
                            : intdiv($minutes, 60).' giờ'.($minutes % 60 > 0 ? ' '.($minutes % 60).' phút' : '');
                    }
                @endphp
                <div class="co-detail">
                    <div class="co-state">
                        <span><i class="bi bi-check-circle-fill"></i> Đã tìm thấy khách đang trong công ty.</span>
                        <x-status-badge :status="$scannedVisit->status" />
                    </div>
                    <div class="co-profile">
                        <div class="co-avatar">{{ strtoupper(mb_substr($scannedVisit->visitor?->full_name ?? 'K', 0, 1)) }}</div>
                        <div>
                            <h3 class="co-name">{{ $scannedVisit->visitor?->full_name ?? '-' }}</h3>
                            <div class="co-company">{{ $scannedVisit->visitor?->company ?? 'Khách vãng lai' }}</div>
                        </div>
                    </div>
                    <div class="co-detail-grid">
                        <div class="co-info">
                            <div class="co-info-row"><i class="bi bi-person"></i><div><span class="co-lbl">Người cần gặp</span><span class="co-val">{{ $scannedVisit->hostEmployee?->name ?? '-' }}</span></div></div>
                            <div class="co-info-row"><i class="bi bi-building"></i><div><span class="co-lbl">Phòng ban</span><span class="co-val">{{ $scannedVisit->hostEmployee?->department?->name ?? '-' }}</span></div></div>
                            <div class="co-info-row"><i class="bi bi-clock"></i><div><span class="co-lbl">Vào lúc</span><span class="co-val">{{ $scannedVisit->actual_checkin_at?->format('H:i - d/m/Y') ?? '-' }}</span></div></div>
                            <div class="co-info-row"><i class="bi bi-calendar-check"></i><div><span class="co-lbl">Giờ hẹn</span><span class="co-val">{{ $scannedVisit->scheduled_at?->format('H:i - d/m/Y') ?? '-' }}</span></div></div>
                            <div class="co-info-row"><i class="bi bi-telephone"></i><div><span class="co-lbl">Số điện thoại</span><span class="co-val">{{ $scannedVisit->visitor?->phone ?? '-' }}</span></div></div>
                            <div class="co-info-row"><i class="bi bi-envelope"></i><div><span class="co-lbl">Email</span><span class="co-val">{{ $scannedVisit->visitor?->email ?? '-' }}</span></div></div>
                            <div class="co-info-row wide"><i class="bi bi-chat-square-text"></i><div><span class="co-lbl">Mục đích đến</span><span class="co-val">{{ $scannedVisit->purpose ?? '-' }}</span></div></div>
                        </div>
                        <div class="co-side">
                            <div class="co-side-item"><span>Mã lịch hẹn</span><strong>{{ $scannedVisit->code }}</strong></div>
                            <div class="co-side-item"><span>Trạng thái hiện tại</span><strong>Đang trong công ty</strong></div>
                            <div class="co-side-item {{ $isOverstay ? 'warning' : '' }}"><span>{{ $isOverstay ? 'Thời gian quá giờ' : 'Thời gian còn lại' }}</span><strong>{{ $remaining }}</strong></div>
                            <div class="co-side-item"><span>Thẻ ra vào</span><strong>{{ $scannedVisit->activeBadge?->badge_no ?? 'Chưa cấp thẻ' }}</strong></div>
                        </div>
                    </div>
                    <div class="co-actions">
                        <form action="{{ route('admin.checkout.confirm', $scannedVisit) }}" method="post">
                            @csrf
                            <button class="co-btn-red" type="submit"><i class="bi bi-box-arrow-left"></i> Xác nhận khách ra</button>
                        </form>
                        <a class="co-btn-cancel d-flex align-items-center justify-content-center text-decoration-none" href="{{ route('admin.checkout.index') }}">Hủy thao tác</a>
                    </div>
                </div>
            @else
                <div class="co-empty">
                    <div>
                        <i class="bi bi-person-bounding-box"></i>
                        <strong>Chưa có khách được chọn</strong>
                        <p>Quét QR, nhập mã lịch hẹn hoặc mã thẻ để hiển thị thông tin khách cần làm thủ tục ra.</p>
                        <span class="co-empty-hint"><i class="bi bi-info-circle"></i> Chỉ khách đang trong công ty mới được làm thủ tục ra.</span>
                    </div>
                </div>
            @endif
        </section>
    </div>

    <div class="co-bottom">
        <section class="co-card">
            <div class="co-table-head">
                <div>
                    <h3>Khách đang trong công ty</h3>
                    <p>Danh sách khách đã vào nhưng chưa làm thủ tục ra.</p>
                </div>
                <div class="co-filter">
                    <input class="form-control" placeholder="Tìm kiếm khách...">
                    <select class="form-select"><option>Tất cả phòng ban</option></select>
                </div>
            </div>
            @if (count($insideVisits) > 0)
                <div class="co-row-head">
                    <span>Khách</span><span>Người cần gặp</span><span>Phòng ban</span><span>Vào lúc</span><span>Thời gian ở lại</span><span>Trạng thái</span><span>Thao tác</span>
                </div>
            @endif
            <div class="co-list">
                @forelse ($insideVisits as $visit)
                    <button class="co-row" type="button" data-checkout-code="{{ $visit['code'] }}">
                        <div><div class="co-vname">{{ $visit['visitor'] }}</div><div class="co-muted">{{ $visit['company'] }}</div></div>
                        <span>{{ $visit['host'] }}</span>
                        <span>{{ $visit['department'] }}</span>
                        <span>{{ $visit['checkin_time'] }}</span>
                        <span class="co-remain">{{ $visit['remaining'] }}</span>
                        <span>
                            @if ($visit['is_overstay'])
                                <span class="co-status co-status-late"><i class="bi bi-exclamation-triangle"></i> Quá giờ</span>
                            @else
                                <span class="co-status co-status-ok"><i class="bi bi-check-circle"></i> Đang trong công ty</span>
                            @endif
                        </span>
                        <span><span class="co-action-btn">Khách ra</span></span>
                    </button>
                @empty
                    <div class="co-empty py-4">Không có khách đang trong công ty.</div>
                @endforelse
            </div>
        </section>

        <section class="co-card">
            <div class="co-table-head">
                <div>
                    <h3>Tổng quan khách ra hôm nay</h3>
                    <p>{{ now()->format('d/m/Y') }}</p>
                </div>
            </div>
            <div class="co-stat-grid">
                <div class="co-stat green"><span>Đã làm thủ tục ra</span><strong>{{ $checkoutStats['checked_out_today'] }}</strong><small>Khách</small></div>
                <div class="co-stat blue"><span>Đang trong công ty</span><strong>{{ $checkoutStats['inside'] }}</strong><small>Khách</small></div>
                <div class="co-stat orange"><span>Quá giờ</span><strong>{{ $checkoutStats['overstay'] }}</strong><small>Khách</small></div>
                <div class="co-stat purple"><span>Tỷ lệ ra đúng giờ</span><strong>{{ $checkoutStats['on_time_rate'] }}%</strong><small>Hôm nay</small></div>
            </div>
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('[data-checkout-code]').forEach((row) => {
        row.addEventListener('click', () => {
            const input = document.getElementById('checkoutCodeInput');
            if (!input) return;
            input.value = row.dataset.checkoutCode || '';
            input.focus();
            input.select();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    });
</script>
@endpush
