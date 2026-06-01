@extends('layouts.admin')

@section('title', 'Báo cáo & Thống kê | Gatehouse Pro')
@section('page_title', 'Báo cáo & Thống kê')
@section('page_subtitle', 'Tổng hợp và phân tích dữ liệu khách ra vào')

@push('styles')
<style>
.report-shell{display:grid;gap:1rem}.report-actions{display:flex;justify-content:flex-end;gap:.65rem;margin-top:-.25rem}.report-btn{min-height:40px;display:inline-flex;align-items:center;gap:.45rem;border-radius:12px;padding:.5rem .85rem;font-size:.8rem;font-weight:900;text-decoration:none;border:1px solid #d8e5f2;background:#fff;color:#29435f}.report-btn.excel{border-color:#bbf7d0;background:#ecfdf5;color:#059669}.report-btn.pdf{border-color:#fecaca;background:#fff1f2;color:#dc2626}.report-btn.print{border:0;background:linear-gradient(135deg,#146bd7,#0cb4d8);color:#fff}
.report-filter{display:grid;grid-template-columns:1.2fr 1fr 1fr 1fr auto auto;gap:.85rem;align-items:end;padding:1rem;border:1px solid #e3edf8;border-radius:20px;background:#fff;box-shadow:0 12px 32px rgba(17,39,68,.05)}.report-field label{display:block;margin-bottom:.35rem;color:#29435f;font-size:.72rem;font-weight:900}.report-field .form-control,.report-field .form-select{min-height:42px;border-color:#d8e5f2;border-radius:12px;font-size:.82rem}.report-filter .btn{min-height:42px;border-radius:12px;font-weight:900}
.report-kpis{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:1rem}.report-kpi{display:flex;align-items:center;justify-content:space-between;gap:.8rem;padding:1rem;border:1px solid #e3edf8;border-radius:20px;background:#fff;box-shadow:0 12px 32px rgba(17,39,68,.05)}.report-kpi span{display:block;color:#5f7895;font-size:.72rem;font-weight:900}.report-kpi strong{display:block;margin:.25rem 0;color:#0b1f3a;font-size:1.65rem;font-weight:900}.report-kpi small{color:#059669;font-size:.68rem;font-weight:800}.report-kpi small.down{color:#dc2626}.report-kpi-icon{width:52px;height:52px;display:grid;place-items:center;border-radius:18px;font-size:1.3rem}.rk-blue{background:#eff6ff;color:#146bd7}.rk-green{background:#ecfdf5;color:#059669}.rk-purple{background:#f0edff;color:#6d5dfc}.rk-amber{background:#fff7ed;color:#d97706}.rk-red{background:#fff1f2;color:#e11d48}
.report-grid{display:grid;grid-template-columns:1.35fr .9fr 1fr;gap:1rem}.report-card{background:#fff;border:1px solid #e3edf8;border-radius:20px;box-shadow:0 14px 36px rgba(17,39,68,.05);overflow:hidden}.report-card-head{display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:1rem;border-bottom:1px solid #edf3fb}.report-card-head h3{margin:0;color:#0b1f3a;font-size:.92rem;font-weight:900}.report-card-head .form-select{width:auto;min-height:34px;border-radius:10px;font-size:.75rem}.report-card-body{padding:1rem}
.chart{height:240px;display:flex;align-items:end;gap:.42rem;padding:.5rem .25rem 0;border-bottom:1px solid #edf3fb}.chart-col{flex:1;min-width:14px;display:flex;align-items:end;justify-content:center;gap:2px;height:100%;position:relative}.chart-bar{width:7px;border-radius:999px 999px 0 0}.chart-in{background:linear-gradient(180deg,#146bd7,#8ec5ff)}.chart-out{background:linear-gradient(180deg,#10b981,#bbf7d0)}.chart-labels{display:flex;justify-content:space-between;gap:.4rem;margin-top:.6rem;color:#7a93b0;font-size:.68rem}.chart-legend{display:flex;align-items:center;gap:1rem;color:#526b87;font-size:.72rem;font-weight:800}.chart-dot{display:inline-block;width:9px;height:9px;border-radius:999px;margin-right:.3rem}.chart-dot.in{background:#146bd7}.chart-dot.out{background:#10b981}
.rank-list{display:grid;gap:.85rem}.rank-item{display:grid;grid-template-columns:24px 42px 1fr auto;gap:.65rem;align-items:center}.rank-no{color:#29435f;font-size:.76rem;font-weight:900}.rank-avatar{width:38px;height:38px;display:grid;place-items:center;border-radius:50%;background:#e0e7ff;color:#4f46e5;font-weight:900}.rank-name{color:#0b1f3a;font-size:.8rem;font-weight:900}.rank-sub{color:#7a93b0;font-size:.68rem}.rank-total{color:#29435f;font-size:.72rem;font-weight:900}.rank-bar{grid-column:3/5;height:7px;border-radius:999px;background:#edf5ff;overflow:hidden}.rank-bar span{display:block;height:100%;border-radius:999px;background:linear-gradient(135deg,#146bd7,#0cb4d8)}
.donut-wrap{display:grid;grid-template-columns:160px 1fr;gap:1rem;align-items:center}.donut{width:150px;height:150px;border-radius:50%;display:grid;place-items:center;background:conic-gradient(#146bd7 0 35%,#10b981 35% 60%,#f59e0b 60% 80%,#0cb4d8 80% 92%,#fb923c 92% 100%)}.donut-inner{width:94px;height:94px;border-radius:50%;display:grid;place-items:center;background:#fff;text-align:center}.donut-inner strong{display:block;color:#0b1f3a;font-size:1.45rem;font-weight:900}.donut-inner span{color:#7a93b0;font-size:.68rem}.dept-list{display:grid;gap:.55rem}.dept-item{display:grid;grid-template-columns:10px 1fr auto;gap:.55rem;align-items:center;color:#29435f;font-size:.78rem}.dept-color{width:9px;height:9px;border-radius:50%}.dept-item strong{font-weight:900}
.report-table-card{background:#fff;border:1px solid #e3edf8;border-radius:22px;box-shadow:0 14px 36px rgba(17,39,68,.05);overflow:hidden}.report-table-head{display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:1rem;border-bottom:1px solid #edf3fb}.report-table-head h3{margin:0;color:#0b1f3a;font-size:.92rem;font-weight:900}.report-table{width:100%;border-collapse:separate;border-spacing:0}.report-table th{padding:.85rem 1rem;color:#6f88a4;font-size:.68rem;font-weight:900;text-transform:uppercase;border-bottom:1px solid #edf3fb;background:#fbfdff}.report-table td{padding:.85rem 1rem;color:#29435f;font-size:.78rem;border-bottom:1px solid #edf3fb;vertical-align:middle}.report-table tbody tr:hover{background:#f7fbff}.report-code{color:#0b1f3a;font-weight:900}.report-guest{display:block;color:#0b1f3a;font-weight:900}.report-muted{color:#7a93b0;font-size:.7rem}.report-footer{display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:1rem;color:#6f88a4;font-size:.78rem}
@media(max-width:1400px){.report-kpis{grid-template-columns:repeat(2,1fr)}.report-grid{grid-template-columns:1fr}.report-filter{grid-template-columns:1fr 1fr}}@media(max-width:768px){.report-actions{justify-content:flex-start;flex-wrap:wrap}.report-kpis,.report-filter,.donut-wrap{grid-template-columns:1fr}.report-table{min-width:980px}}
</style>
@endpush

@section('content')
@php
    $maxChart = max(1, collect($chartDays)->max(fn ($day) => max($day['checkin'], $day['checkout'])) ?? 1);
    $maxHost = max(1, $topHosts->max('total') ?? 1);
    $deptColors = ['#146bd7', '#10b981', '#f59e0b', '#0cb4d8', '#fb923c'];
    $exportParams = ['from_date' => $filters['from_date'], 'to_date' => $filters['to_date'], 'status' => $filters['status'], 'type' => 'visits'];
@endphp

<div class="report-shell">
    <div class="report-actions">
        <a class="report-btn excel" href="{{ route('admin.reports.visits.export-xlsx', $exportParams) }}"><i class="bi bi-file-earmark-spreadsheet"></i> Xuất Excel</a>
        <a class="report-btn pdf" href="{{ route('admin.reports.visits.export', $exportParams) }}"><i class="bi bi-filetype-csv"></i> Xuất CSV</a>
        <button class="report-btn print" type="button" onclick="window.print()"><i class="bi bi-printer"></i> In báo cáo</button>
    </div>

    <form class="report-filter" method="get" action="{{ route('admin.reports.index') }}">
        <div class="report-field">
            <label>Khoảng thời gian</label>
            <div class="d-flex gap-2">
                <input class="form-control" type="date" name="from_date" value="{{ $filters['from_date'] }}">
                <input class="form-control" type="date" name="to_date" value="{{ $filters['to_date'] }}">
            </div>
        </div>
        <div class="report-field">
            <label>Phòng ban</label>
            <select class="form-select" name="department">
                <option value="all">Tất cả phòng ban</option>
                @foreach ($departments as $department)
                    <option value="{{ $department->id }}" @selected($filters['department'] === (string) $department->id)>{{ $department->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="report-field">
            <label>Trạng thái</label>
            <select class="form-select" name="status">
                @foreach ($statuses as $value => $label)
                    <option value="{{ $value }}" @selected($filters['status'] === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="report-field">
            <label>Loại khách</label>
            <select class="form-select" name="type">
                <option value="all" @selected($filters['type'] === 'all')>Tất cả loại khách</option>
                <option value="company" @selected($filters['type'] === 'company')>Khách công ty</option>
                <option value="walkin" @selected($filters['type'] === 'walkin')>Khách vãng lai</option>
            </select>
        </div>
        <button class="btn btn-brand" type="submit"><i class="bi bi-funnel"></i> Lọc dữ liệu</button>
        <a class="btn btn-light" href="{{ route('admin.reports.index') }}"><i class="bi bi-arrow-clockwise"></i> Đặt lại</a>
    </form>

    <section class="report-kpis">
        <div class="report-kpi"><div><span>Tổng khách</span><strong>{{ $reportStats['total'] }}</strong><small class="{{ $reportStats['growth'] < 0 ? 'down' : '' }}">{{ $reportStats['growth'] >= 0 ? '↑' : '↓' }} {{ abs($reportStats['growth']) }}% so với kỳ trước</small></div><div class="report-kpi-icon rk-blue"><i class="bi bi-people-fill"></i></div></div>
        <div class="report-kpi"><div><span>Đang trong công ty</span><strong>{{ $reportStats['checked_in'] }}</strong><small>Đang theo dõi</small></div><div class="report-kpi-icon rk-green"><i class="bi bi-person-check-fill"></i></div></div>
        <div class="report-kpi"><div><span>Đã check-out</span><strong>{{ $reportStats['checked_out'] }}</strong><small>Đã hoàn tất</small></div><div class="report-kpi-icon rk-purple"><i class="bi bi-box-arrow-right"></i></div></div>
        <div class="report-kpi"><div><span>Chờ check-in</span><strong>{{ $reportStats['pending_checkin'] }}</strong><small>Đã duyệt</small></div><div class="report-kpi-icon rk-amber"><i class="bi bi-clock-fill"></i></div></div>
        <div class="report-kpi"><div><span>Quá giờ chưa ra</span><strong>{{ $reportStats['overstay'] }}</strong><small class="down">Cần xử lý</small></div><div class="report-kpi-icon rk-red"><i class="bi bi-exclamation-triangle-fill"></i></div></div>
    </section>

    <section class="report-grid">
        <div class="report-card">
            <div class="report-card-head">
                <h3>Biểu đồ khách theo ngày</h3>
                <div class="chart-legend"><span><i class="chart-dot in"></i>Check-in</span><span><i class="chart-dot out"></i>Check-out</span></div>
            </div>
            <div class="report-card-body">
                <div class="chart">
                    @foreach ($chartDays as $day)
                        <div class="chart-col" title="{{ $day['label'] }}: {{ $day['checkin'] }} vào, {{ $day['checkout'] }} ra">
                            <span class="chart-bar chart-in" style="height: {{ max(4, round($day['checkin'] / $maxChart * 100)) }}%"></span>
                            <span class="chart-bar chart-out" style="height: {{ max(4, round($day['checkout'] / $maxChart * 100)) }}%"></span>
                        </div>
                    @endforeach
                </div>
                <div class="chart-labels">
                    @foreach (collect($chartDays)->take(6) as $day)
                        <span>{{ $day['label'] }}</span>
                    @endforeach
                    @if (count($chartDays) > 6)
                        <span>{{ collect($chartDays)->last()['label'] }}</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="report-card">
            <div class="report-card-head"><h3>Top nhân viên tiếp khách</h3></div>
            <div class="report-card-body rank-list">
                @forelse ($topHosts as $index => $host)
                    <div class="rank-item">
                        <div class="rank-no">{{ $index + 1 }}</div>
                        <div class="rank-avatar">{{ strtoupper(mb_substr($host['name'], 0, 1)) }}</div>
                        <div><div class="rank-name">{{ $host['name'] }}</div><div class="rank-sub">{{ $host['department'] }}</div></div>
                        <div class="rank-total">{{ $host['total'] }} lượt</div>
                        <div class="rank-bar"><span style="width: {{ round($host['total'] / $maxHost * 100) }}%"></span></div>
                    </div>
                @empty
                    <div class="text-secondary small">Chưa có dữ liệu nhân viên tiếp khách.</div>
                @endforelse
            </div>
        </div>

        <div class="report-card">
            <div class="report-card-head"><h3>Top phòng ban</h3></div>
            <div class="report-card-body">
                <div class="donut-wrap">
                    <div class="donut"><div class="donut-inner"><div><strong>{{ $reportStats['total'] }}</strong><span>Tổng khách</span></div></div></div>
                    <div class="dept-list">
                        @forelse ($topDepartments as $index => $department)
                            <div class="dept-item">
                                <span class="dept-color" style="background: {{ $deptColors[$index % count($deptColors)] }}"></span>
                                <span>{{ $department['name'] }}</span>
                                <strong>{{ $department['percent'] }}% ({{ $department['total'] }})</strong>
                            </div>
                        @empty
                            <div class="text-secondary small">Chưa có dữ liệu phòng ban.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="report-table-card">
        <div class="report-table-head">
            <h3>Danh sách khách ra vào</h3>
            <a class="report-btn" href="{{ route('admin.reports.visits', ['from_date' => $filters['from_date'], 'to_date' => $filters['to_date'], 'status' => $filters['status']]) }}"><i class="bi bi-braces"></i> Xem JSON</a>
        </div>
        <div class="table-responsive">
            <table class="report-table">
                <thead>
                <tr>
                    <th>Mã lịch hẹn</th>
                    <th>Khách</th>
                    <th>Công ty</th>
                    <th>Người tiếp</th>
                    <th>Phòng ban</th>
                    <th>Check-in</th>
                    <th>Check-out</th>
                    <th>Thời gian ở lại</th>
                    <th>Trạng thái</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @forelse ($reportVisits as $visit)
                    @php
                        $duration = '-';
                        if ($visit->actual_checkin_at && $visit->actual_checkout_at) {
                            $minutes = $visit->actual_checkin_at->diffInMinutes($visit->actual_checkout_at);
                            $duration = floor($minutes / 60).'h '.($minutes % 60).'m';
                        }
                    @endphp
                    <tr>
                        <td><span class="report-code">{{ $visit->code }}</span></td>
                        <td><span class="report-guest">{{ $visit->visitor?->full_name ?? '-' }}</span><span class="report-muted">{{ $visit->visitor?->phone ?? '' }}</span></td>
                        <td>{{ $visit->visitor?->company ?? '-' }}</td>
                        <td>{{ $visit->hostEmployee?->name ?? '-' }}</td>
                        <td>{{ $visit->hostEmployee?->department?->name ?? '-' }}</td>
                        <td>{{ $visit->actual_checkin_at?->format('d/m/Y H:i') ?? '-' }}</td>
                        <td>{{ $visit->actual_checkout_at?->format('d/m/Y H:i') ?? '-' }}</td>
                        <td>{{ $duration }}</td>
                        <td><x-status-badge :status="$visit->status" /></td>
                        <td><a class="report-btn" href="{{ route('admin.visits.show', $visit) }}"><i class="bi bi-eye"></i></a></td>
                    </tr>
                @empty
                    <tr><td colspan="10" class="text-center text-secondary py-4">Không có dữ liệu báo cáo theo bộ lọc hiện tại.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="report-footer">
            <span>Hiển thị {{ $reportVisits->count() }} / {{ $reportStats['total'] }} kết quả</span>
            <span>Cập nhật lúc {{ now()->format('d/m/Y H:i') }}</span>
        </div>
    </section>
</div>
@endsection
