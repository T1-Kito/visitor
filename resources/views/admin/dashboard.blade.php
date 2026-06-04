@extends('layouts.admin')

@section('title', 'Tổng quan vận hành | Visitor Management')
@section('page_title', 'Tổng quan vận hành')
@section('page_subtitle', 'Theo dõi khách ra/vào, phê duyệt, check-in/check-out và cảnh báo trong thời gian thực')

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
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.db-metric {
    border-radius: 20px;
    padding: 1.25rem;
    position: relative;
    overflow: hidden;
    color: #10233d;
    background: rgba(255, 255, 255, 0.92);
    border: 1px solid #e1ebf6;
    box-shadow: 0 10px 28px rgba(17, 39, 68, 0.045);
}

.db-metric::after {
    content: '';
    position: absolute;
    right: -34px; bottom: -34px;
    width: 104px; height: 104px;
    border-radius: 50%;
    background: var(--db-metric-soft);
    z-index: 0;
}

.db-metric > * {
    position: relative;
    z-index: 1;
}

.db-metric-blue   { --db-metric-color: #146bd7; --db-metric-soft: #eaf4ff; }
.db-metric-cyan   { --db-metric-color: #078b82; --db-metric-soft: #e8fbf8; }
.db-metric-amber  { --db-metric-color: #d97706; --db-metric-soft: #fff4e5; }
.db-metric-red    { --db-metric-color: #c83245; --db-metric-soft: #fff0f2; }
.db-metric-slate  { --db-metric-color: #526b7a; --db-metric-soft: #eef4f7; }

.db-metric-label {
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #7187a3;
    margin-bottom: 0.55rem;
}

.db-metric-value {
    font-family: "Plus Jakarta Sans", sans-serif;
    font-weight: 650;
    font-size: 2.4rem;
    letter-spacing: 0;
    line-height: 1;
    margin-bottom: 0.4rem;
    color: var(--db-metric-color);
}

.db-metric-note {
    font-size: 0.75rem;
    color: #7187a3;
    font-weight: 500;
}

.db-metric-icon {
    position: absolute;
    right: 1.1rem; top: 1.1rem;
    width: 42px; height: 42px;
    background: var(--db-metric-soft);
    color: var(--db-metric-color);
    border-radius: 14px;
    display: grid;
    place-items: center;
    font-size: 1.25rem;
    z-index: 1;
}

/* ===== ACTION SHORTCUTS ===== */
.db-actions {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 0.85rem;
    margin-bottom: 1.5rem;
}

.db-action-btn {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.9rem 1rem;
    background: #fff;
    border: 1px solid #e4edf8;
    border-radius: 18px;
    text-decoration: none;
    box-shadow: 0 2px 12px rgba(17,39,68,0.06);
    transition: box-shadow 0.15s, border-color 0.15s, transform 0.12s;
}

.db-action-btn:hover {
    box-shadow: 0 6px 24px rgba(20,107,215,0.13);
    border-color: #c2d9f8;
    transform: translateY(-1px);
}

.db-action-icon {
    width: 40px; height: 40px;
    border-radius: 13px;
    background: linear-gradient(135deg, #146bd7, #0cb4d8);
    display: grid;
    place-items: center;
    font-size: 1.05rem;
    color: #fff;
    flex-shrink: 0;
}

.db-action-title {
    font-size: 0.85rem;
    font-weight: 800;
    color: #0b1f3a;
    line-height: 1.2;
}

.db-action-sub {
    font-size: 0.72rem;
    color: #7a93b0;
    line-height: 1.2;
}

/* ===== CARDS ===== */
.db-card {
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
    padding: 1.25rem 1.5rem 1rem;
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
    padding: 0.75rem 1.5rem;
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
    height: 36px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .35rem;
    padding: 0 .85rem;
    border: 0;
    border-radius: 12px;
    color: #fff;
    background: linear-gradient(135deg, #146bd7, #0cb4d8);
    font-size: .8rem;
    font-weight: 700;
    white-space: nowrap;
}

.db-reset-link {
    height: 36px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .35rem;
    padding: 0 .75rem;
    border: 1px solid #dde8f5;
    border-radius: 12px;
    color: #526b87;
    background: #fff;
    font-size: .78rem;
    font-weight: 600;
    text-decoration: none;
    white-space: nowrap;
}

/* table cells */
.db-visitor-cell { display:flex; align-items:center; gap:0.65rem; }

.db-avatar {
    width: 34px; height: 34px;
    border-radius: 50%;
    background: linear-gradient(135deg, #e8f0fe, #c7d7fc);
    display: grid;
    place-items: center;
    font-size: 0.75rem;
    font-weight: 800;
    color: #1d4ed8;
    flex-shrink: 0;
}

.db-visitor-name { font-weight:700; font-size:0.85rem; color:#0b1f3a; line-height:1.2; }
.db-visitor-company { font-size:0.72rem; color:#7a93b0; }

.db-host-cell { display:flex; align-items:center; gap:0.55rem; }

.db-host-avatar {
    width: 30px; height: 30px;
    border-radius: 50%;
    background: linear-gradient(135deg, #fce4ec, #f8bbd9);
    display: grid;
    place-items: center;
    font-size: 0.7rem;
    font-weight: 800;
    color: #ad1457;
    flex-shrink: 0;
}

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

/* Responsive */
@media (max-width: 1200px) {
    .db-metrics { grid-template-columns: repeat(3, 1fr); }
    .db-actions  { grid-template-columns: repeat(3, 1fr); }
}
@media (max-width: 768px) {
    .db-metrics { grid-template-columns: repeat(2, 1fr); }
    .db-actions  { grid-template-columns: repeat(2, 1fr); }
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
        <p class="db-metric-value">{{ $stats['today'] }}</p>
        <span class="db-metric-note">Tổng lịch trong ngày</span>
    </div>
    <div class="db-metric db-metric-cyan">
        <div class="db-metric-icon"><i class="bi bi-person-walking"></i></div>
        <p class="db-metric-label">Đang trong công ty</p>
        <p class="db-metric-value">{{ $stats['in_company'] }}</p>
        <span class="db-metric-note">Live visitors</span>
    </div>
    <div class="db-metric db-metric-amber">
        <div class="db-metric-icon"><i class="bi bi-hourglass-split"></i></div>
        <p class="db-metric-label">Chờ phê duyệt</p>
        <p class="db-metric-value">{{ $stats['pending'] }}</p>
        <span class="db-metric-note">Cần host xử lý</span>
    </div>
    <div class="db-metric db-metric-red">
        <div class="db-metric-icon"><i class="bi bi-alarm"></i></div>
        <p class="db-metric-label">Khách quá giờ</p>
        <p class="db-metric-value">{{ $stats['overstay'] }}</p>
        <span class="db-metric-note">Quá 15 phút</span>
    </div>
    <div class="db-metric db-metric-slate">
        <div class="db-metric-icon"><i class="bi bi-box-arrow-left"></i></div>
        <p class="db-metric-label">Đã check-out</p>
        <p class="db-metric-value">{{ $stats['checked_out'] }}</p>
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
        <div class="db-action-icon" style="background:linear-gradient(135deg,#059669,#10b981)"><i class="bi bi-qr-code-scan"></i></div>
        <div><div class="db-action-title">Check-in</div><div class="db-action-sub">Scan QR, cấp badge</div></div>
    </a>
    <a href="{{ route('admin.access.index', ['mode' => 'checkout']) }}" class="db-action-btn">
        <div class="db-action-icon" style="background:linear-gradient(135deg,#7c3aed,#8b5cf6)"><i class="bi bi-box-arrow-left"></i></div>
        <div><div class="db-action-title">Check-out</div><div class="db-action-sub">Thu hồi quyền ra/vào</div></div>
    </a>
    <a href="{{ route('admin.reports.index') }}" class="db-action-btn">
        <div class="db-action-icon" style="background:linear-gradient(135deg,#0891b2,#06b6d4)"><i class="bi bi-file-earmark-arrow-down"></i></div>
        <div><div class="db-action-title">Xuất báo cáo</div><div class="db-action-sub">CSV, Excel, in ấn</div></div>
    </a>
</div>

{{-- RECENT VISITS --}}
<div class="row g-3">

    <div class="col-12">
        <div class="db-card">
            <div class="db-card-head">
                <div>
                    <h3>Lịch hẹn gần đây</h3>
                    <p>Mặc định hiển thị lịch mới nhất trong ngày, có thể lọc theo ngày và trạng thái.</p>
                </div>
                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.visits.index') }}"
                   style="border-radius:10px;font-weight:700;font-size:.78rem;">Xem tất cả</a>
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

            <form class="db-toolbar" method="get" action="{{ route('admin.dashboard') }}">
                <div class="db-search">
                    <i class="bi bi-search"></i>
                    <input class="form-control" name="recent_q" value="{{ $recentFilters['q'] ?? '' }}" placeholder="Tìm mã lịch, khách, người tiếp...">
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

            <div class="table-responsive">
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
                    <tbody>
                    @forelse (array_slice($visits, 0, 8) as $visit)
                        <tr>
                            <td>
                                <div class="db-code">
                                    {{ $visit['code'] }}
                                    <span class="db-code-qr"><i class="bi bi-upc-scan"></i></span>
                                </div>
                            </td>
                            <td>
                                <div class="db-visitor-cell">
                                    <div class="db-avatar">{{ strtoupper(mb_substr($visit['visitor'], 0, 1)) }}</div>
                                    <div>
                                        <div class="db-visitor-name">{{ $visit['visitor'] }}</div>
                                        <div class="db-visitor-company">{{ $visit['department'] }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="db-host-cell">
                                    <div class="db-host-avatar">{{ strtoupper(mb_substr($visit['host'], 0, 1)) }}</div>
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
        </div>
    </div>

</div>

<script>
(function() {
    const el = document.getElementById('dbClock');
    if (!el) return;
    setInterval(function() {
        const n = new Date();
        el.textContent = n.toLocaleTimeString('vi-VN', {hour:'2-digit',minute:'2-digit',second:'2-digit'});
    }, 1000);
})();
</script>
@endsection
