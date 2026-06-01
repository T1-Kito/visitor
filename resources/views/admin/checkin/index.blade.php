@extends('layouts.admin')

@section('title', 'Check-in khách | Quản lý khách')
@section('page_title', 'Check-in khách')
@section('page_subtitle', 'Nhập mã QR hoặc mã lịch hẹn để làm thủ tục khách vào')

@push('styles')
<style>
.ci-grid {
    display: grid;
    grid-template-columns: minmax(400px, 0.82fr) minmax(620px, 1fr);
    grid-template-rows: auto auto;
    gap: 1rem;
    align-items: start;
}

/* ===== SCAN CARD ===== */
.ci-scan-card {
    background: #fff;
    border: 1px solid #e4edf8;
    border-radius: 22px;
    overflow: hidden;
    box-shadow: 0 2px 16px rgba(17,39,68,.05);
    grid-row: span 1;
}

.ci-card-head {
    padding: 1.15rem 1.35rem 0.95rem;
    border-bottom: 1px solid #f0f5fc;
}

.ci-card-head h3 {
    font-family: "Plus Jakarta Sans", sans-serif;
    font-weight: 800; font-size: .98rem; color: #0b1f3a; margin: 0;
}

.ci-card-head p { font-size: .74rem; color: #7a93b0; margin: .12rem 0 0; }

.ci-qr-frame {
    position: relative;
    margin: 1.1rem 1.35rem .75rem;
    border-radius: 18px;
    overflow: hidden;
    background: linear-gradient(135deg, #f0f6ff, #e8f4ff);
    min-height: 300px;
    display: flex; align-items: center; justify-content: center;
}

.ci-qr-corners::before, .ci-qr-corners::after,
.ci-qr-corner-tr, .ci-qr-corner-bl {
    content: ''; position: absolute;
    width: 28px; height: 28px;
    border: 3px solid #146bd7;
}

.ci-qr-corners { position: absolute; inset: 0; }
.ci-qr-corners::before { top:10px; left:10px; border-right:0; border-bottom:0; border-radius:4px 0 0 0; }
.ci-qr-corners::after  { bottom:10px; right:10px; border-left:0; border-top:0; border-radius:0 0 4px 0; }
.ci-qr-corner-tr { position:absolute; top:10px; right:10px; border-left:0; border-bottom:0; border-radius:0 4px 0 0; }
.ci-qr-corner-bl { position:absolute; bottom:10px; left:10px; border-right:0; border-top:0; border-radius:0 0 0 4px; }

.ci-qr-icon { font-size: 4.2rem; color: #c2d9f0; position: relative; z-index: 1; }

.ci-qr-hint { text-align: center; font-size: .76rem; color: #7a93b0; margin: 0 1.4rem .6rem; }

.ci-manual { padding: 0 1.4rem 1.4rem; display: flex; flex-direction: column; gap: .6rem; }

.ci-input-wrap { position: relative; }
.ci-input-wrap i { position: absolute; right: .85rem; top: 50%; transform: translateY(-50%); color: #9aafca; pointer-events: none; }
.ci-input-wrap input {
    width: 100%; padding: .7rem 2.4rem .7rem .9rem;
    border: 1.5px solid #dde8f5; border-radius: 13px;
    font-size: .85rem; transition: border-color .15s;
}
.ci-input-wrap input:focus { outline: none; border-color: #146bd7; box-shadow: 0 0 0 3px rgba(20,107,213,.1); }

.ci-btn-check {
    width: 100%; padding: .82rem; border: none; border-radius: 13px;
    background: linear-gradient(135deg, #146bd7, #0cb4d8);
    color: #fff; font-weight: 800; font-size: .9rem;
    cursor: pointer; display: flex; align-items: center; justify-content: center; gap: .45rem;
    transition: opacity .15s;
}
.ci-btn-check:hover { opacity: .9; }

/* ===== DETAIL CARD ===== */
.ci-detail-card {
    background: #fff; border: 1px solid #e4edf8;
    border-radius: 22px; overflow: hidden;
    box-shadow: 0 2px 16px rgba(17,39,68,.05);
    min-height: 470px;
}

.ci-visitor-profile {
    display: flex; align-items: center; gap: 1.1rem;
    padding: 1.1rem 1.4rem; border-bottom: 1px solid #f0f5fc;
}

.ci-visitor-avatar {
    width: 64px; height: 64px; border-radius: 50%;
    background: linear-gradient(135deg, #e8f0fe, #c7d7fc);
    display: grid; place-items: center;
    font-family: "Plus Jakarta Sans", sans-serif;
    font-weight: 800; font-size: 1.5rem; color: #1d4ed8; flex-shrink: 0;
}

.ci-visitor-name { font-family: "Plus Jakarta Sans", sans-serif; font-weight: 800; font-size: 1.1rem; color: #0b1f3a; margin: 0 0 .18rem; }
.ci-visitor-co   { font-size: .8rem; color: #7a93b0; }

.ci-info-grid {
    display: grid; grid-template-columns: 1fr 1fr; gap: 0;
}

.ci-info-row {
    display: flex; align-items: center; gap: .7rem;
    padding: .7rem 1.4rem; border-bottom: 1px solid #f6f9fd;
}
.ci-info-row:nth-child(odd) { border-right: 1px solid #f6f9fd; }

.ci-info-ico {
    width: 28px; height: 28px; border-radius: 8px;
    background: #f0f6ff; display: grid; place-items: center;
    font-size: .78rem; color: #146bd7; flex-shrink: 0;
}

.ci-info-lbl { font-size: .68rem; color: #9aafca; line-height: 1; }
.ci-info-val { font-size: .82rem; font-weight: 700; color: #0b1f3a; line-height: 1.25; }

.ci-meta-row {
    display: flex; align-items: center; gap: 1rem;
    padding: .8rem 1.4rem; background: #f8fbff; border-top: 1px solid #f0f5fc;
}

.ci-qr-ok  { display: inline-flex; align-items: center; gap: .35rem; font-size: .78rem; font-weight: 700; color: #059669; }
.ci-qr-bad { display: inline-flex; align-items: center; gap: .35rem; font-size: .78rem; font-weight: 700; color: #dc2626; }

.ci-code-val { font-family: "Plus Jakarta Sans", sans-serif; font-weight: 800; font-size: .88rem; color: #0b1f3a; }
.ci-copy { background: none; border: none; color: #9aafca; cursor: pointer; font-size: .88rem; padding: .15rem; transition: color .12s; }
.ci-copy:hover { color: #146bd7; }

.ci-btn-checkin {
    display: flex; align-items: center; justify-content: center; gap: .55rem;
    width: calc(100% - 2.8rem); margin: .9rem 1.4rem 1.4rem;
    padding: .95rem; border: none; border-radius: 15px;
    background: linear-gradient(135deg, #059669, #10b981);
    color: #fff; font-weight: 800; font-size: .95rem;
    cursor: pointer; letter-spacing: .01em; transition: opacity .15s;
}
.ci-btn-checkin:hover { opacity: .9; }

.ci-no-scan {
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    min-height: 550px;
    gap: .7rem; padding: 2.75rem 1.5rem; text-align: center; color: #9aafca;
}
.ci-no-scan i { font-size: 2.8rem; color: #c8daf0; }
.ci-no-scan strong { font-size: .9rem; color: #5a7a99; font-weight: 700; }
.ci-no-scan p { font-size: .78rem; margin: 0; line-height: 1.55; max-width: 300px; }

/* ===== BOTTOM 2 CARDS ===== */
.ci-bottom {
    grid-column: 1 / -1;
    display: grid;
    grid-template-columns: minmax(560px, 1.35fr) minmax(420px, 1fr);
    gap: 1rem;
}

.ci-btm-card {
    background: #fff; border: 1px solid #e4edf8;
    border-radius: 22px; overflow: hidden;
    box-shadow: 0 2px 16px rgba(17,39,68,.05);
}

.ci-btm-head {
    display: flex; align-items: center; justify-content: space-between;
    padding: 1.05rem 1.25rem .85rem; border-bottom: 1px solid #f0f5fc;
}
.ci-btm-head h3 { font-family: "Plus Jakarta Sans", sans-serif; font-weight: 800; font-size: .9rem; color: #0b1f3a; margin: 0; }

.ci-pill { display: inline-flex; align-items: center; gap: .28rem; padding: .2rem .6rem; border-radius: 999px; font-size: .7rem; font-weight: 800; }
.ci-pill-blue  { background: #dbeafe; color: #1d4ed8; }
.ci-pill-green { background: #d1fae5; color: #065f46; }

.ci-qrow {
    display: grid;
    grid-template-columns: 120px 72px minmax(130px, 1fr) minmax(130px, 1fr) 120px 96px;
    align-items: center;
    gap: .7rem;
    padding: .65rem 1.25rem; border-bottom: 1px solid #f6f9fd; transition: background .12s;
    border: 0;
    border-bottom: 1px solid #f6f9fd;
    width: 100%;
    background: transparent;
    text-align: left;
    cursor: pointer;
}
.ci-qrow:last-child { border-bottom: none; }
.ci-qrow:hover { background: #eef7ff; }

.ci-qhead {
    display: grid;
    grid-template-columns: 120px 72px minmax(130px, 1fr) minmax(130px, 1fr) 120px 96px;
    gap: .7rem;
    padding: .85rem 1.25rem .45rem;
    color: #7a93b0;
    font-size: .72rem;
    font-weight: 700;
}
.ci-qtime { font-weight: 800; font-size: .8rem; color: #0b1f3a; }
.ci-qcode {
    color: #146bd7;
    font-family: "Plus Jakarta Sans", sans-serif;
    font-size: .76rem;
    font-weight: 800;
}
.ci-qname { font-weight: 700; font-size: .8rem; color: #0b1f3a; line-height: 1.2; }
.ci-qhost { font-size: .7rem; color: #7a93b0; }
.ci-qdept { font-size: .7rem; font-weight: 600; color: #5a7a99; background: #f0f6ff; padding: .15rem .5rem; border-radius: 999px; }
.ci-qstatus { font-size: .7rem; font-weight: 700; color: #d97706; display: flex; align-items: center; gap: .25rem; white-space: nowrap; }

.ci-viewall {
    display: flex; align-items: center; justify-content: center; gap: .35rem;
    padding: .7rem; font-size: .78rem; font-weight: 700; color: #146bd7;
    text-decoration: none; border-top: 1px solid #f0f5fc; transition: background .12s;
}
.ci-viewall:hover { background: #f0f7ff; }

.ci-empty { text-align: center; padding: 1.75rem 1rem; color: #9aafca; font-size: .8rem; }

/* Donut */
.ci-donut-body { display: flex; align-items: center; gap: 1.1rem; padding: 1.1rem 1.25rem; }

.ci-donut-rel { position: relative; width: 130px; height: 130px; flex-shrink: 0; }

.ci-donut-center {
    position: absolute; inset: 0;
    display: flex; flex-direction: column; align-items: center; justify-content: center;
}
.ci-donut-num { font-family: "Plus Jakarta Sans", sans-serif; font-weight: 800; font-size: 1.4rem; color: #0b1f3a; line-height: 1; }
.ci-donut-sub { font-size: .65rem; color: #7a93b0; font-weight: 600; }

.ci-legend { display: flex; flex-direction: column; gap: .55rem; }
.ci-legend-row { display: flex; align-items: center; gap: .5rem; }
.ci-ldot { width: 9px; height: 9px; border-radius: 50%; flex-shrink: 0; }
.ci-llbl { font-size: .75rem; color: #7a93b0; }
.ci-lval { font-size: .78rem; font-weight: 800; color: #0b1f3a; }

@media (max-width: 1100px) {
    .ci-grid   { grid-template-columns: 1fr; }
    .ci-scan-card { grid-row: span 1; }
    .ci-bottom { grid-column: auto; grid-template-columns: 1fr; }
    .ci-qhead { display: none; }
    .ci-qrow { display: flex; }
    .ci-info-grid { grid-template-columns: 1fr; }
    .ci-info-row:nth-child(odd) { border-right: none; }
}
</style>
@endpush

@section('content')
<div class="ci-grid">

    {{-- LEFT: SCAN --}}
    <div class="ci-scan-card">
        <div class="ci-card-head">
            <h3>Nhập mã check-in</h3>
            <p>Camera chưa kích hoạt, vui lòng nhập mã QR hoặc mã lịch hẹn thủ công.</p>
        </div>

        <div class="ci-qr-frame">
            <div class="ci-qr-corners"></div>
            <div class="ci-qr-corner-tr"></div>
            <div class="ci-qr-corner-bl"></div>
            <i class="bi bi-qr-code ci-qr-icon"></i>
        </div>

        <p class="ci-qr-hint">Nhập mã QR hoặc mã lịch hẹn</p>

        <form class="ci-manual" action="{{ route('admin.checkin.scan-qr') }}" method="post">
            @csrf
            <div class="ci-input-wrap">
                <input type="text" name="qr_token"
                       id="checkinCodeInput"
                       placeholder="Nhập mã lịch hẹn (VD: WK-260529-001)"
                       value="{{ old('qr_token') }}">
                <i class="bi bi-upc-scan"></i>
            </div>
            <button class="ci-btn-check" type="submit">
                <i class="bi bi-search"></i> Kiểm tra mã
            </button>
        </form>
    </div>

    {{-- RIGHT TOP: DETAIL --}}
    <div class="ci-detail-card">
        @if ($scannedVisit)
            <div class="ci-card-head">
                <div style="display:flex;align-items:center;justify-content:space-between;gap:1rem;">
                    <div>
                        <h3>Thông tin khách</h3>
                        <p>Kiểm tra trước khi xác nhận khách vào công ty</p>
                    </div>
                    <x-status-badge :status="$scannedVisit->status" />
                </div>
            </div>

            <div class="ci-visitor-profile">
                <div class="ci-visitor-avatar">
                    {{ strtoupper(mb_substr($scannedVisit->visitor?->full_name ?? 'K', 0, 1)) }}
                </div>
                <div>
                    <p class="ci-visitor-name">{{ $scannedVisit->visitor?->full_name ?? '-' }}</p>
                    <p class="ci-visitor-co">{{ $scannedVisit->visitor?->company ?? 'Khách vãng lai' }}</p>
                </div>
            </div>

            <div class="ci-info-grid">
                <div class="ci-info-row">
                    <div class="ci-info-ico"><i class="bi bi-person-badge"></i></div>
                    <div><div class="ci-info-lbl">Người cần gặp</div><div class="ci-info-val">{{ $scannedVisit->hostEmployee?->name ?? '-' }}</div></div>
                </div>
                <div class="ci-info-row">
                    <div class="ci-info-ico"><i class="bi bi-building"></i></div>
                    <div><div class="ci-info-lbl">Phòng ban</div><div class="ci-info-val">{{ $scannedVisit->hostEmployee?->department?->name ?? '-' }}</div></div>
                </div>
                <div class="ci-info-row">
                    <div class="ci-info-ico"><i class="bi bi-clock"></i></div>
                    <div><div class="ci-info-lbl">Giờ hẹn</div><div class="ci-info-val">{{ $scannedVisit->scheduled_at?->format('H:i - d/m/Y') ?? '-' }}</div></div>
                </div>
                <div class="ci-info-row">
                    <div class="ci-info-ico"><i class="bi bi-telephone"></i></div>
                    <div><div class="ci-info-lbl">Số điện thoại</div><div class="ci-info-val">{{ $scannedVisit->visitor?->phone ?? '-' }}</div></div>
                </div>
                <div class="ci-info-row">
                    <div class="ci-info-ico"><i class="bi bi-envelope"></i></div>
                    <div><div class="ci-info-lbl">Email</div><div class="ci-info-val">{{ $scannedVisit->visitor?->email ?? '-' }}</div></div>
                </div>
                <div class="ci-info-row">
                    <div class="ci-info-ico"><i class="bi bi-chat-square-text"></i></div>
                    <div><div class="ci-info-lbl">Mục đích đến</div><div class="ci-info-val">{{ $scannedVisit->purpose ?? '-' }}</div></div>
                </div>
            </div>

            <div class="ci-meta-row">
                <div style="flex:1">
                    @if ($scannedVisit->qr_token && !$scannedQrExpired)
                        <span class="ci-qr-ok"><i class="bi bi-check-circle-fill"></i> Mã hợp lệ</span>
                    @else
                        <span class="ci-qr-bad"><i class="bi bi-x-circle-fill"></i> QR đã hết hạn</span>
                    @endif
                </div>
                <div style="display:flex;align-items:center;gap:.4rem;">
                    <span style="font-size:.68rem;color:#9aafca;">Mã lịch hẹn</span>
                    <span class="ci-code-val">{{ $scannedVisit->code }}</span>
                    <button class="ci-copy" onclick="navigator.clipboard.writeText('{{ $scannedVisit->code }}')">
                        <i class="bi bi-copy"></i>
                    </button>
                </div>
            </div>

            @if ($scannedVisit->status === 'approved' && !$scannedQrExpired)
                <form action="{{ route('admin.checkin.confirm', $scannedVisit) }}" method="post">
                    @csrf
                    <button class="ci-btn-checkin" type="submit">
                        <i class="bi bi-check-circle-fill"></i> Xác nhận khách vào
                    </button>
                </form>
            @else
                <div style="padding:.7rem 1.4rem 1.2rem">
                    <div class="alert alert-warning mb-0" style="border-radius:13px;font-size:.82rem;font-weight:600;">
                        @if ($scannedVisit->status !== 'approved')
                        <i class="bi bi-info-circle me-1"></i> Lịch chưa được phê duyệt ({{ $scannedVisit->status }}).
                        @else
                            <i class="bi bi-exclamation-triangle me-1"></i> Mã QR đã hết hạn hoặc không hợp lệ.
                        @endif
                    </div>
                </div>
            @endif
        @else
            <div class="ci-no-scan">
                <i class="bi bi-person-bounding-box"></i>
                <strong>Chưa có khách được chọn</strong>
                <p>Nhập mã QR hoặc mã lịch hẹn bên trái để hiển thị thông tin khách cần làm thủ tục vào.</p>
            </div>
        @endif
    </div>

    {{-- RIGHT BOTTOM --}}
    <div class="ci-bottom">

        {{-- Upcoming --}}
        <div class="ci-btm-card">
            <div class="ci-btm-head">
                <h3>Khách đã duyệt, chờ làm thủ tục vào</h3>
                <span class="ci-pill ci-pill-blue">{{ count($upcomingToday) }} khách</span>
            </div>
            @if (count($upcomingToday) > 0)
                <div class="ci-qhead">
                    <span>Mã lịch</span>
                    <span>Lịch hẹn</span>
                    <span>Khách</span>
                    <span>Người cần gặp</span>
                    <span>Phòng ban</span>
                    <span>Trạng thái</span>
                </div>
            @endif
            @forelse ($upcomingToday as $v)
                <button class="ci-qrow" type="button" data-checkin-code="{{ $v['code'] }}">
                    <span class="ci-qcode">{{ $v['code'] }}</span>
                    <span class="ci-qtime">{{ $v['time'] }}<br><small>{{ $v['date'] ?? '-' }}</small></span>
                    <div style="min-width:0;">
                        <div class="ci-qname">{{ $v['visitor'] }}</div>
                    </div>
                    <div class="ci-qhost">{{ $v['host'] }}</div>
                    <span class="ci-qdept">{{ $v['department'] }}</span>
                    <span class="ci-qstatus"><i class="bi bi-check-circle"></i> Đã duyệt</span>
                </button>
            @empty
                <div class="ci-empty">Không có khách đã duyệt đang chờ làm thủ tục vào.</div>
            @endforelse
            <a class="ci-viewall" href="{{ route('admin.visits.index') }}">Xem tất cả <i class="bi bi-arrow-right"></i></a>
        </div>

        {{-- Stats donut --}}
        <div class="ci-btm-card">
            <div class="ci-btm-head">
                <h3>Check-in hôm nay</h3>
                <span class="ci-pill ci-pill-green">{{ $todayStats['total'] }} khách</span>
            </div>
            <div class="ci-donut-body">
                @php
                    $r  = 48; $cx = 65; $cy = 65;
                    $c  = 2 * M_PI * $r;
                    $dIn  = ($todayStats['pct_in']  / 100) * $c;
                    $dOut = ($todayStats['pct_out'] / 100) * $c;
                @endphp
                <div class="ci-donut-rel">
                    <svg width="130" height="130" viewBox="0 0 130 130">
                        <circle cx="{{ $cx }}" cy="{{ $cy }}" r="{{ $r }}" fill="none" stroke="#f0f5fc" stroke-width="13"/>
                        @if ($dOut > 0)
                        <circle cx="{{ $cx }}" cy="{{ $cy }}" r="{{ $r }}" fill="none" stroke="#94a3b8" stroke-width="13"
                                stroke-dasharray="{{ $dOut }} {{ $c - $dOut }}"
                                stroke-linecap="round" transform="rotate(-90 {{ $cx }} {{ $cy }})"/>
                        @endif
                        @if ($dIn > 0)
                        <circle cx="{{ $cx }}" cy="{{ $cy }}" r="{{ $r }}" fill="none" stroke="#10b981" stroke-width="13"
                                stroke-dasharray="{{ $dIn }} {{ $c - $dIn }}"
                                stroke-linecap="round" transform="rotate(-90 {{ $cx }} {{ $cy }})"/>
                        @endif
                    </svg>
                    <div class="ci-donut-center">
                        <span class="ci-donut-num">{{ $todayStats['total'] }}</span>
                        <span class="ci-donut-sub">Tổng số</span>
                    </div>
                </div>
                <div class="ci-legend">
                    <div class="ci-legend-row">
                        <div class="ci-ldot" style="background:#10b981"></div>
                        <div><div class="ci-llbl">Đã check-in</div><div class="ci-lval">{{ $todayStats['in_company'] }} ({{ $todayStats['pct_in'] }}%)</div></div>
                    </div>
                    <div class="ci-legend-row">
                        <div class="ci-ldot" style="background:#3b82f6"></div>
                        <div><div class="ci-llbl">Đang trong công ty</div><div class="ci-lval">{{ $todayStats['in_company'] }} ({{ $todayStats['pct_in'] }}%)</div></div>
                    </div>
                    <div class="ci-legend-row">
                        <div class="ci-ldot" style="background:#94a3b8"></div>
                        <div><div class="ci-llbl">Đã check-out</div><div class="ci-lval">{{ $todayStats['checked_out'] }} ({{ $todayStats['pct_out'] }}%)</div></div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('[data-checkin-code]').forEach((row) => {
        row.addEventListener('click', () => {
            const input = document.getElementById('checkinCodeInput');
            if (!input) return;

            input.value = row.dataset.checkinCode || '';
            input.focus();
            input.select();
        });
    });
</script>
@endpush
