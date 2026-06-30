@extends('layouts.admin')

@section('title', 'Tổng quan vận hành | Visitor Management')


@section('topbar_meta')
    <div class="db-topbar-time d-none d-xl-inline-flex">
        <i class="bi bi-calendar3"></i>
        <span>{{ now()->isoFormat('dddd, DD/MM/YYYY') }}</span>
        <span class="db-topbar-separator">|</span>
        <i class="bi bi-clock"></i>
        <span data-db-clock>{{ now()->format('H:i:s') }}</span>
    </div>
@endsection

@push('styles')
<style>
/* ===== DASHBOARD HERO ===== */
.db-hero {
    position: relative;
    border-radius: 24px;
    overflow: hidden;
    background: linear-gradient(120deg, #0b1f3a 0%, #0d2c4a 55%, #1565c0 100%);
    padding: 2rem 2rem 1.75rem;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 1.5rem;
    color: #fff;
}

.db-hero::before {
    content: '';
    position: absolute;
    right: -40px; top: -60px;
    width: 320px; height: 320px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(255,255,255,0.06) 0%, transparent 70%);
    pointer-events: none;
}

.db-hero-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: rgba(255,255,255,0.55);
    margin-bottom: 0.6rem;
}

.db-hero h1 {
    font-family: "Plus Jakarta Sans", sans-serif;
    font-weight: 800;
    font-size: clamp(1.6rem, 3vw, 2.1rem);
    letter-spacing: -0.04em;
    color: #fff;
    margin: 0 0 0.45rem;
    line-height: 1.1;
}

.db-hero p {
    font-size: 0.87rem;
    color: rgba(255,255,255,0.55);
    margin: 0;
    max-width: 540px;
    line-height: 1.55;
}

.db-hero-actions {
    display: flex;
    gap: 0.65rem;
    flex-shrink: 0;
    align-items: flex-start;
}

.db-hero-date {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.82rem;
    color: rgba(255,255,255,0.7);
    margin-top: 0.6rem;
    font-weight: 600;
}

.db-hero-date i { color: rgba(255,255,255,0.45); }

.db-btn-create {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    padding: 0.65rem 1.15rem;
    background: rgba(255,255,255,0.12);
    border: 1.5px solid rgba(255,255,255,0.22);
    border-radius: 14px;
    color: #fff;
    font-weight: 700;
    font-size: 0.85rem;
    text-decoration: none;
    transition: background 0.15s;
    backdrop-filter: blur(8px);
}

.db-btn-create:hover { background: rgba(255,255,255,0.2); color: #fff; }

.db-btn-export {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    padding: 0.65rem 1.15rem;
    background: rgba(255,255,255,0.08);
    border: 1.5px solid rgba(255,255,255,0.15);
    border-radius: 14px;
    color: rgba(255,255,255,0.8);
    font-weight: 700;
    font-size: 0.85rem;
    text-decoration: none;
    transition: background 0.15s;
}

.db-btn-export:hover { background: rgba(255,255,255,0.14); color: #fff; }

/* ===== METRIC CARDS ===== */
.db-metrics {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 1.1rem;
    margin: 0 1rem 1rem;
}

.db-metric {
    border-radius: 12px;
    padding: 0.5rem 1rem;
    position: relative;
    overflow: hidden;
    color: #10233d;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-top: 3px solid #d40511;
    box-shadow: 0 5px 14px rgba(15, 23, 42, 0.05);
}

.db-metric::after {
    display: none;
}

.db-metric > * {
    position: relative;
    z-index: 1;
}

.db-metric-blue,
.db-metric-cyan,
.db-metric-amber,
.db-metric-red,
.db-metric-slate {
    --db-metric-color: #d40511;
    --db-metric-soft: #ffcc00;
}

.db-metric-label {
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #667085;
    margin-bottom: 0.35rem;
}

.db-metric-value {
    font-family: "Plus Jakarta Sans", sans-serif;
    font-weight: 650;
    font-size: 2.05rem;
    letter-spacing: 0;
    line-height: 1;
    margin-bottom: 0.3rem;
    color: #111827;
}

.db-metric-amber .db-metric-value,
.db-metric-red .db-metric-value {
    color: #d40511;
}

.db-metric-note {
    font-size: 0.75rem;
    color: #667085;
    font-weight: 500;
}

.db-metric-icon {
    position: absolute;
    right: 0.9rem; top: 0.85rem;
    width: 38px; height: 38px;
    background: var(--db-metric-soft);
    color: var(--db-metric-color);
    border: 1px solid #e0b400;
    border-radius: 10px;
    display: grid;
    place-items: center;
    font-size: 1.1rem;
    z-index: 1;
}

/* ===== ACTION SHORTCUTS ===== */
.db-actions {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 1.1rem;
    margin: 0 1rem 1rem;
}

.db-recent-section {
    margin-right: 0.5rem;
    margin-left: 0.5rem;
}

.db-action-btn {
    display: flex;
    align-items: center;
    gap: 0.65rem;
    min-height: 64px;
    padding: 0.65rem 0.85rem;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    text-decoration: none;
    box-shadow: 0 5px 14px rgba(15,23,42,0.04);
    transition: box-shadow 0.15s, border-color 0.15s;
}

.db-action-btn:hover {
    box-shadow: 0 6px 16px rgba(15,23,42,0.07);
    border-color: #d7dee8;
    background: #fff;
}

.db-action-icon {
    width: 36px; height: 36px;
    border: 1px solid #e0b400;
    border-radius: 9px;
    background: #ffcc00;
    display: grid;
    place-items: center;
    font-size: 0.95rem;
    color: #d40511;
    flex-shrink: 0;
}

.db-action-title {
    font-size: 0.82rem;
    font-weight: 700;
    color: #0b1f3a;
    line-height: 1.2;
}

.db-action-sub {
    font-size: 0.69rem;
    color: #7a93b0;
    line-height: 1.2;
}

/* ===== CARDS ===== */
.db-card {
    display: flex;
    flex-direction: column;
    background: #fff;
    border: 1px solid #e4edf8;
    border-radius: 22px;
    overflow: hidden;
    box-shadow: 0 2px 16px rgba(17,39,68,0.05);
}

.db-card-head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 1rem;
    padding: 1rem 1.25rem 0.75rem;
}

.db-card-head h3 {
    font-family: "Plus Jakarta Sans", sans-serif;
    font-weight: 800;
    font-size: 1rem;
    letter-spacing: -0.02em;
    color: #0b1f3a;
    margin: 0;
}

.db-card-head p {
    font-size: 0.75rem;
    color: #7a93b0;
    margin: 0.15rem 0 0;
}

.db-toolbar {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.6rem;
    padding: 0.65rem 1.25rem;
    background: #f8fbff;
    border-bottom: 1px solid #edf3fb;
}

.db-search {
    position: relative;
    flex: 1;
    min-width: 200px;
}

.db-search i {
    position: absolute;
    left: 0.75rem; top: 50%;
    transform: translateY(-50%);
    color: #9aafca;
    font-size: 0.85rem;
    pointer-events: none;
}

.db-search input {
    padding-left: 2.1rem;
    border-radius: 12px;
    border: 1px solid #dde8f5;
    font-size: 0.83rem;
    background: #fff;
    width: 100%;
    height: 36px;
}

.db-filter {
    border-radius: 12px !important;
    border: 1px solid #dde8f5 !important;
    font-size: 0.82rem !important;
    height: 36px !important;
    background: #fff !important;
    padding: 0 0.75rem !important;
    color: #2c4a6e !important;
}

.db-filter-btn {
    height: 34px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .35rem;
    padding: 0 .75rem;
    border: 1px solid color-mix(in srgb, var(--gate-blue) 78%, #ffffff);
    border-radius: 10px;
    color: #fff;
    background: var(--gate-blue);
    font-size: .76rem;
    font-weight: 600;
    white-space: nowrap;
}

.db-reset-link {
    height: 34px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .35rem;
    padding: 0 .7rem;
    border: 1px solid color-mix(in srgb, var(--gate-blue) 18%, #dde8f5);
    border-radius: 10px;
    color: var(--gate-blue);
    background: #fff;
    font-size: .76rem;
    font-weight: 500;
    text-decoration: none;
    white-space: nowrap;
}

.db-see-all {
    min-height: 34px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0 .78rem;
    border: 1px solid color-mix(in srgb, var(--gate-blue) 25%, #dde8f5);
    border-radius: 10px;
    color: var(--gate-blue);
    background: #fff;
    font-size: .76rem;
    font-weight: 500;
    text-decoration: none;
}

.db-see-all:hover,
.db-reset-link:hover {
    border-color: var(--gate-blue);
    color: var(--gate-blue);
    background: color-mix(in srgb, var(--gate-blue) 5%, #ffffff);
}

/* table cells */
.db-visitor-cell { display:block; }


.db-visitor-name { font-weight:700; font-size:0.85rem; color:#0b1f3a; line-height:1.2; }
.db-visitor-company { font-size:0.72rem; color:#7a93b0; }

.db-host-cell { display:block; }


.db-code {
    font-family: "Plus Jakarta Sans", sans-serif;
    font-weight: 800;
    font-size: 0.85rem;
    color: #0b1f3a;
    display: flex;
    align-items: center;
    gap: 0.35rem;
}

.db-code-qr {
    width: 22px; height: 22px;
    background: #eff6ff;
    border-radius: 6px;
    display: grid;
    place-items: center;
    font-size: 0.65rem;
    color: #146bd7;
}

.db-time { font-weight:700; font-size:0.85rem; color:#0b1f3a; }

/* ===== ALERTS ===== */
.db-alert-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.28rem 0.75rem;
    background: #fff7e6;
    border: 1px solid #fdd89a;
    border-radius: 999px;
    font-size: 0.75rem;
    font-weight: 800;
    color: #92400e;
}

.db-alert-item {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 0.75rem;
    padding: 0.85rem 1.5rem;
    border-bottom: 1px solid #f0f5fc;
    transition: background 0.12s;
}

.db-alert-item:last-child { border-bottom: none; }
.db-alert-item:hover { background: #f9fbff; }

.db-alert-dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    margin-top: 5px;
    flex-shrink: 0;
}

.db-alert-dot.danger  { background:#ef4444; box-shadow:0 0 0 3px rgba(239,68,68,0.15); }
.db-alert-dot.warning { background:#f59e0b; box-shadow:0 0 0 3px rgba(245,158,11,0.15); }
.db-alert-dot.info    { background:#3b82f6; box-shadow:0 0 0 3px rgba(59,130,246,0.15); }

.db-alert-title { font-size:0.83rem; font-weight:700; color:#0b1f3a; line-height:1.3; }
.db-alert-title.danger  { color:#dc2626; }
.db-alert-title.warning { color:#d97706; }

.db-alert-msg { font-size:0.75rem; color:#7a93b0; margin-top:0.15rem; line-height:1.4; }
.db-alert-time { font-size:0.72rem; font-weight:700; color:#9aafca; flex-shrink:0; white-space:nowrap; }

.db-view-all {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.4rem;
    padding: 0.85rem;
    font-size: 0.82rem;
    font-weight: 700;
    color: #146bd7;
    text-decoration: none;
    border-top: 1px solid #f0f5fc;
    transition: background 0.12s;
}

.db-view-all:hover { background:#f0f7ff; }

/* Compact enterprise dashboard header: the page topbar already owns the title and primary actions. */
.db-hero {
    display: none;
    min-height: 0;
    justify-content: flex-end;
    align-items: center;
    margin: 0;
    padding: 0;
    border-radius: 0;
    overflow: visible;
    background: transparent;
    color: #7187a3;
}

.db-hero::before,
.db-hero-eyebrow,
.db-hero h1,
.db-hero p,
.db-hero-actions {
    display: none;
}

.db-hero-date {
    justify-content: flex-end;
    margin: 0;
    color: #7187a3;
    font-size: .76rem;
    font-weight: 500;
}

.db-hero-date i {
    color: var(--gate-blue);
}

.db-topbar-time {
    align-items: center;
    gap: .45rem;
    min-height: 38px;
    padding: 0 .72rem;
    border: 1px solid #edf3fb;
    border-radius: 12px;
    background: #fff;
    color: #7187a3;
    font-size: .76rem;
    font-weight: 500;
    white-space: nowrap;
}

.db-topbar-time i {
    color: #d40511;
}

.db-topbar-separator {
    color: #c4d2e1;
}

.dashboard-recent-card {
    min-height: calc(100vh - 500px);
}

.dashboard-recent-table {
    flex: 1 1 auto;
    min-height: 205px;
}

.dashboard-recent-table .modern-table {
    height: 100%;
}

.db-pagination {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    margin-top: auto;
    padding: .78rem 1.25rem;
    border-top: 1px solid #edf3fb;
    color: #7187a3;
    font-size: .76rem;
}

.db-pagination nav,
.db-pagination .pagination {
    margin: 0;
}

.db-pagination .pagination {
    gap: .28rem;
}

.db-pagination .page-link {
    min-width: 32px;
    min-height: 32px;
    display: grid;
    place-items: center;
    padding: .3rem .58rem;
    border-color: #dbe7f4;
    border-radius: 9px;
    color: #315b89;
    font-size: .76rem;
    box-shadow: none;
}

.db-pagination .page-item.active .page-link {
    border-color: var(--gate-blue);
    background: var(--gate-blue);
}

.db-pagination svg {
    width: 13px;
    height: 13px;
}

/* Responsive */
@media (max-width: 1200px) {
    .db-metrics { grid-template-columns: repeat(3, 1fr); }
    .db-actions  { grid-template-columns: repeat(3, 1fr); }
}
@media (max-width: 768px) {
    .db-metrics { grid-template-columns: repeat(2, 1fr); }
    .db-actions  { grid-template-columns: repeat(2, 1fr); }
    .db-metrics,
    .db-actions {
        margin-right: 0;
        margin-left: 0;
    }
    .db-recent-section {
        margin-right: -0.5rem;
        margin-left: -0.5rem;
    }
    .db-hero     { flex-direction: column; }
    .db-hero-actions { flex-direction: column; width: 100%; }
}
</style>
@endpush

@section('content')

{{-- HERO --}}
<div class="db-hero">
    <div>
        <div class="db-hero-eyebrow"><i class="bi bi-shield-check"></i> Gatehouse Pro</div>
        <h1>Tổng quan vận hành</h1>
        <p>Theo dõi khách ra/vào, phê duyệt, check-in/check-out và cảnh báo trong thời gian thực.</p>
        <div class="db-hero-date">
            <i class="bi bi-calendar3"></i>
            {{ now()->isoFormat('dddd, DD/MM/YYYY') }}
            <span style="opacity:.4;margin:0 .2rem">|</span>
            <i class="bi bi-clock"></i>
            <span id="dbClock">{{ now()->format('H:i:s') }}</span>
        </div>
    </div>
    <div class="db-hero-actions">
        <a href="{{ route('admin.visits.create') }}" class="db-btn-create">
            <i class="bi bi-plus-circle"></i> Tạo lịch hẹn
        </a>
        <a href="{{ route('admin.reports.index') }}" class="db-btn-export">
            <i class="bi bi-file-earmark-spreadsheet"></i> Xuất báo cáo
        </a>
    </div>
</div>

{{-- METRICS --}}
<div class="db-metrics">
    <div class="db-metric db-metric-blue">
        <div class="db-metric-icon"><i class="bi bi-people"></i></div>
        <p class="db-metric-label">Khách hôm nay</p>
        <p class="db-metric-value" data-dashboard-stat="today">{{ $stats['today'] }}</p>
        <span class="db-metric-note">Tổng lịch trong ngày</span>
    </div>
    <div class="db-metric db-metric-cyan">
        <div class="db-metric-icon"><i class="bi bi-person-walking"></i></div>
        <p class="db-metric-label">Đang trong công ty</p>
        <p class="db-metric-value" data-dashboard-stat="in_company">{{ $stats['in_company'] }}</p>
        <span class="db-metric-note">Khách đang hiện diện</span>
    </div>
    <div class="db-metric db-metric-amber">
        <div class="db-metric-icon"><i class="bi bi-hourglass-split"></i></div>
        <p class="db-metric-label">Chờ phê duyệt</p>
        <p class="db-metric-value" data-dashboard-stat="pending">{{ $stats['pending'] }}</p>
        <span class="db-metric-note">Cần người tiếp xử lý</span>
    </div>
    <div class="db-metric db-metric-red">
        <div class="db-metric-icon"><i class="bi bi-alarm"></i></div>
        <p class="db-metric-label">Khách quá giờ</p>
        <p class="db-metric-value" data-dashboard-stat="overstay">{{ $stats['overstay'] }}</p>
        <span class="db-metric-note">Quá 15 phút</span>
    </div>
    <div class="db-metric db-metric-slate">
        <div class="db-metric-icon"><i class="bi bi-box-arrow-left"></i></div>
        <p class="db-metric-label">Đã check-out</p>
        <p class="db-metric-value" data-dashboard-stat="checked_out">{{ $stats['checked_out'] }}</p>
        <span class="db-metric-note">Hôm nay</span>
    </div>
</div>

{{-- ACTIONS --}}
<div class="db-actions">
    <a href="{{ route('admin.visits.create') }}" class="db-action-btn">
        <div class="db-action-icon"><i class="bi bi-plus-circle"></i></div>
        <div><div class="db-action-title">Tạo lịch hẹn</div><div class="db-action-sub">Đăng ký khách mới</div></div>
    </a>
    <a href="{{ route('admin.approvals.index') }}" class="db-action-btn">
        <div class="db-action-icon"><i class="bi bi-check2-square"></i></div>
        <div><div class="db-action-title">Duyệt lịch</div><div class="db-action-sub">Xử lý yêu cầu chờ</div></div>
    </a>
    <a href="{{ route('admin.access.index', ['mode' => 'checkin']) }}" class="db-action-btn">
        <div class="db-action-icon"><i class="bi bi-qr-code-scan"></i></div>
        <div><div class="db-action-title">Check-in</div><div class="db-action-sub">Quét QR, cấp thẻ</div></div>
    </a>
    <a href="{{ route('admin.access.index', ['mode' => 'checkout']) }}" class="db-action-btn">
        <div class="db-action-icon"><i class="bi bi-box-arrow-left"></i></div>
        <div><div class="db-action-title">Check-out</div><div class="db-action-sub">Thu hồi quyền ra/vào</div></div>
    </a>
    <a href="{{ route('admin.reports.index') }}" class="db-action-btn">
        <div class="db-action-icon"><i class="bi bi-file-earmark-arrow-down"></i></div>
        <div><div class="db-action-title">Xuất báo cáo</div><div class="db-action-sub">CSV, Excel, in ấn</div></div>
    </a>
</div>

{{-- RECENT VISITS --}}
<div class="row g-3 db-recent-section">

    <div class="col-12">
        <div class="db-card dashboard-recent-card">
            <div class="db-card-head">
                <div>
                    <h3>Lịch hẹn gần đây</h3>
                    <p>Mặc định hiển thị lịch mới nhất trong ngày, có thể lọc theo ngày và trạng thái.</p>
                </div>
                <a class="db-see-all" href="{{ route('admin.visits.index') }}">Xem tất cả</a>
            </div>

            @php
                $recentStatusOptions = [
                    'all' => 'Tất cả trạng thái',
                    'pending' => 'Chờ duyệt',
                    'approved' => 'Đã duyệt',
                    'checked_in' => 'Đang trong công ty',
                    'checked_out' => 'Đã check-out',
                    'rejected' => 'Từ chối',
                    'cancelled' => 'Đã hủy',
                ];
            @endphp

            <form class="db-toolbar" id="dashboardRecentFilter" method="get" action="{{ route('admin.dashboard') }}">
                <div class="db-search">
                    <i class="bi bi-search"></i>
                    <input class="form-control" id="dashboardRecentSearch" name="recent_q" value="{{ $recentFilters['q'] ?? '' }}" placeholder="Tìm mã lịch, khách, người tiếp..." autocomplete="off">
                </div>
                <input class="form-control db-filter" name="recent_date" type="date" value="{{ $recentFilters['date'] ?? now()->toDateString() }}" style="width:160px;">
                <select class="form-select db-filter" name="recent_status" style="width:180px;">
                    @foreach($recentStatusOptions as $value => $label)
                        <option value="{{ $value }}" @selected(($recentFilters['status'] ?? 'all') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <button class="db-filter-btn" type="submit"><i class="bi bi-funnel"></i>Lọc</button>
                <a class="db-reset-link" href="{{ route('admin.dashboard') }}"><i class="bi bi-arrow-clockwise"></i>Mới nhất</a>
            </form>

            <div class="table-responsive dashboard-recent-table">
                <table class="table modern-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Mã lịch</th>
                            <th>Khách</th>
                            <th>Người tiếp</th>
                            <th>Giờ</th>
                            <th>Trạng thái</th>
                            <th class="text-end">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody id="dashboardRecentRows">
                    @forelse ($visits as $visit)
                        <tr>
                            <td>
                                <div class="db-code">
                                    {{ $visit['code'] }}
                                    <span class="db-code-qr"><i class="bi bi-upc-scan"></i></span>
                                </div>
                            </td>
                            <td>
                                <div class="db-visitor-cell">

                                    <div>
                                        <div class="db-visitor-name">{{ $visit['visitor'] }}</div>
                                        <div class="db-visitor-company">{{ $visit['department'] }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="db-host-cell">

                                    <span style="font-size:.83rem;font-weight:600;color:#2c4a6e">{{ $visit['host'] }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="db-time">{{ $visit['time'] }}</span>
                                <div class="db-visitor-company">{{ $visit['date'] }}</div>
                            </td>
                            <td><x-status-badge :status="$visit['status']" /></td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-light" href="{{ route('admin.visits.show', $visit['id']) }}"
                                   style="border-radius:10px;font-weight:700;font-size:.78rem;">Chi tiết</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="gate-empty">
                                    <i class="bi bi-calendar2-check"></i>
                                    <strong>Không có lịch phù hợp</strong>
                                    <span>Thử đổi ngày, trạng thái hoặc từ khóa để xem lịch khác.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="db-pagination" id="dashboardRecentPagination">
                <span>
                    Hiển thị {{ $recentVisits->firstItem() ?? 0 }} - {{ $recentVisits->lastItem() ?? 0 }} / {{ $recentVisits->total() }} lịch
                </span>
                @if ($recentVisits->hasPages())
                    {{ $recentVisits->links() }}
                @endif
            </div>
        </div>
    </div>

</div>

<script>
(function() {
    const clocks = document.querySelectorAll('[data-db-clock], #dbClock');
    if (clocks.length === 0) return;
    setInterval(function() {
        const n = new Date();
        clocks.forEach((el) => {
            el.textContent = n.toLocaleTimeString('vi-VN', {hour:'2-digit',minute:'2-digit',second:'2-digit'});
        });
    }, 1000);
})();

(function() {
    const form = document.getElementById('dashboardRecentFilter');
    const search = document.getElementById('dashboardRecentSearch');
    if (!form || !search) return;

    let searchTimer = null;
    search.addEventListener('input', function() {
        window.clearTimeout(searchTimer);
        searchTimer = window.setTimeout(function() {
            form.requestSubmit();
        }, 450);
    });

    form.querySelectorAll('input[type="date"], select').forEach(function(field) {
        field.addEventListener('change', function() {
            form.requestSubmit();
        });
    });
})();

(function() {
    const rows = document.getElementById('dashboardRecentRows');
    const pagination = document.getElementById('dashboardRecentPagination');
    if (!rows || !pagination) return;

    let syncing = false;

    async function syncDashboard() {
        if (syncing || document.hidden) return;
        if (document.activeElement?.matches('#dashboardRecentFilter input, #dashboardRecentFilter select')) return;

        syncing = true;

        try {
            const response = await fetch(window.location.href, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                cache: 'no-store',
            });

            if (!response.ok) return;

            const html = await response.text();
            const nextDocument = new DOMParser().parseFromString(html, 'text/html');
            const nextRows = nextDocument.getElementById('dashboardRecentRows');
            const nextPagination = nextDocument.getElementById('dashboardRecentPagination');

            if (nextRows && nextPagination) {
                rows.innerHTML = nextRows.innerHTML;
                pagination.innerHTML = nextPagination.innerHTML;
            }

            document.querySelectorAll('[data-dashboard-stat]').forEach(function(stat) {
                const key = stat.dataset.dashboardStat;
                const nextStat = nextDocument.querySelector('[data-dashboard-stat="' + key + '"]');
                if (nextStat && stat.textContent !== nextStat.textContent) {
                    stat.textContent = nextStat.textContent;
                }
            });
        } catch (error) {
            // Giữ nguyên dữ liệu đang hiển thị nếu kết nối tạm thời bị gián đoạn.
        } finally {
            syncing = false;
        }
    }

    window.setInterval(syncDashboard, 5000);
})();
</script>
@endsection
