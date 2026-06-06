@extends('layouts.admin')

@section('title', 'Báo cáo & Thống kê | Gatehouse Pro')
@section('page_title', 'Báo cáo & Thống kê')
@section('page_subtitle', 'Tổng hợp và phân tích dữ liệu khách ra vào')

@push('styles')
<style>
.report-shell{display:grid;gap:1rem;color:#19324d}.report-toolbar{display:flex;align-items:center;justify-content:space-between;gap:1rem}.report-actions{display:flex;gap:.55rem;flex-wrap:wrap;margin-left:auto}.report-btn{min-height:38px;display:inline-flex;align-items:center;justify-content:center;gap:.45rem;border:1px solid #dbe7f3;border-radius:10px;padding:.45rem .8rem;background:#fff;color:#35516f;font-size:.78rem;font-weight:500;text-decoration:none;transition:.18s ease}.report-btn:hover{border-color:#a9c8e8;background:#f6faff;color:#146bd7}.report-btn.excel{border-color:#bdebd2;color:#087f5b}.report-btn.csv{border-color:#ffd0d5;color:#d7354e}.report-btn.print{border-color:#1d8ed7;background:#168bd1;color:#fff}
.report-filter{display:grid;grid-template-columns:1.2fr 1fr 1fr 1fr auto auto;gap:.75rem;align-items:end;padding:1rem;border:1px solid #e0eaf4;border-radius:16px;background:#fff;box-shadow:0 8px 24px rgba(31,67,106,.045)}.report-field label{display:block;margin-bottom:.35rem;color:#607996;font-size:.72rem;font-weight:500}.report-field .form-control,.report-field .form-select{min-height:41px;border-color:#d9e5f1;border-radius:10px;color:#29435f;font-size:.8rem;font-weight:400}.report-filter .btn{min-height:41px;border-radius:10px;font-size:.8rem;font-weight:500}
.report-kpis{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:.85rem}.report-kpi{position:relative;display:flex;align-items:center;justify-content:space-between;gap:.75rem;min-height:112px;padding:1rem 1.05rem;border:1px solid #e0eaf4;border-radius:16px;background:#fff;box-shadow:0 8px 24px rgba(31,67,106,.045);overflow:hidden}.report-kpi::after{content:"";position:absolute;right:-24px;bottom:-32px;width:88px;height:88px;border-radius:50%;background:var(--kpi-soft)}.report-kpi-label{display:block;color:#69819d;font-size:.74rem;font-weight:500}.report-kpi-value{display:block;margin:.2rem 0 .15rem;color:#17314e;font-size:1.65rem;line-height:1.1;font-weight:600}.report-kpi-note{color:#7890aa;font-size:.69rem;font-weight:400}.report-kpi-icon{position:relative;z-index:1;width:43px;height:43px;display:grid;place-items:center;border-radius:13px;background:var(--kpi-soft);color:var(--kpi-color);font-size:1.08rem}.report-kpi.blue{--kpi-color:#1672d4;--kpi-soft:#eaf4ff}.report-kpi.green{--kpi-color:#07936d;--kpi-soft:#e8faf3}.report-kpi.amber{--kpi-color:#c97908;--kpi-soft:#fff4e2}.report-kpi.red{--kpi-color:#d83e58;--kpi-soft:#fff0f3}
.report-overview-grid{display:grid;grid-template-columns:minmax(0,7fr) minmax(280px,3fr);gap:1rem}.report-ranking-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:1rem}.report-card,.report-table-card{border:1px solid #e0eaf4;border-radius:16px;background:#fff;box-shadow:0 8px 24px rgba(31,67,106,.045);overflow:hidden}.report-card-head,.report-table-head{display:flex;align-items:center;justify-content:space-between;gap:1rem;min-height:58px;padding:.85rem 1rem;border-bottom:1px solid #ebf1f7}.report-card-head h3,.report-table-head h3{margin:0;color:#18324f;font-size:.92rem;font-weight:600}.report-card-subtitle{margin:.18rem 0 0;color:#7a91aa;font-size:.7rem}.report-card-body{padding:1rem}
.chart-legend{display:flex;align-items:center;gap:.85rem;color:#6b839d;font-size:.7rem;font-weight:400}.chart-dot{display:inline-block;width:8px;height:8px;margin-right:.3rem;border-radius:50%}.chart-dot.in{background:#2d83dc}.chart-dot.out{background:#35b88a}.chart{height:240px;display:flex;align-items:end;gap:.42rem;padding:.6rem .3rem 0;border-bottom:1px solid #edf2f7;background:repeating-linear-gradient(to top,transparent 0,transparent 47px,#f0f5fa 48px)}.chart-col{flex:1;min-width:15px;height:100%;display:flex;align-items:end;justify-content:center;gap:3px}.chart-bar{width:8px;border-radius:7px 7px 0 0}.chart-in{background:#2d83dc}.chart-out{background:#35b88a}.chart-labels{display:flex;justify-content:space-between;gap:.35rem;margin-top:.65rem;color:#8498af;font-size:.67rem}
.alert-list{display:grid}.report-alert{display:grid;grid-template-columns:38px minmax(0,1fr) auto;gap:.7rem;align-items:center;padding:.78rem 0;border-bottom:1px solid #edf2f7;color:inherit;text-decoration:none}.report-alert:first-child{padding-top:0}.report-alert:last-child{padding-bottom:0;border-bottom:0}.report-alert:hover .alert-title{color:#146bd7}.alert-icon{width:38px;height:38px;display:grid;place-items:center;border-radius:12px}.report-alert.danger .alert-icon{background:#fff0f2;color:#d93d56}.report-alert.warning .alert-icon{background:#fff6e6;color:#c87b0b}.alert-title{color:#29435f;font-size:.78rem;font-weight:500}.alert-detail,.alert-time{color:#8397ad;font-size:.67rem}.alert-time{white-space:nowrap}.report-empty{padding:1.5rem .5rem;color:#8397ad;font-size:.76rem;text-align:center}
.rank-list{display:grid;gap:.82rem}.rank-item{display:grid;grid-template-columns:28px 40px minmax(0,1fr) auto;gap:.65rem;align-items:center}.rank-no{color:#8ba0b5;font-size:.72rem;font-weight:500}.rank-avatar{width:38px;height:38px;display:grid;place-items:center;border-radius:12px;background:#edf4ff;color:#3479ca;font-size:.78rem;font-weight:500}.rank-name{color:#29435f;font-size:.79rem;font-weight:500}.rank-sub{color:#8498af;font-size:.67rem}.rank-total{color:#526d89;font-size:.71rem;font-weight:500}.rank-bar{grid-column:3/5;height:5px;border-radius:999px;background:#edf3f9;overflow:hidden}.rank-bar span{display:block;height:100%;border-radius:999px;background:#3b8bdc}.dept-rank .rank-avatar{background:#eaf9f4;color:#09946e}.dept-rank .rank-bar span{background:#35b88a}
.report-table-head .table-meta{color:#8196ad;font-size:.7rem}.report-table{width:100%;border-collapse:separate;border-spacing:0}.report-table th{padding:.78rem 1rem;border-bottom:1px solid #e8eff6;background:#fbfdff;color:#758ca5;font-size:.66rem;font-weight:500;text-transform:uppercase;white-space:nowrap}.report-table td{padding:.78rem 1rem;border-bottom:1px solid #edf2f7;color:#3b5672;font-size:.76rem;font-weight:400;vertical-align:middle}.report-table tbody tr:hover{background:#f7fbff}.report-code,.report-guest{display:block;color:#203b58;font-weight:500}.report-muted{display:block;color:#899db2;font-size:.67rem}.report-footer{display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:.85rem 1rem;color:#8498af;font-size:.7rem}
@media(max-width:1300px){.report-filter{grid-template-columns:repeat(2,minmax(0,1fr))}.report-overview-grid{grid-template-columns:2fr 1fr}}@media(max-width:1000px){.report-kpis{grid-template-columns:repeat(2,minmax(0,1fr))}.report-overview-grid,.report-ranking-grid{grid-template-columns:1fr}}@media(max-width:700px){.report-toolbar{align-items:flex-start;flex-direction:column}.report-actions{margin-left:0}.report-filter,.report-kpis{grid-template-columns:1fr}.report-table{min-width:980px}.report-footer{align-items:flex-start;flex-direction:column}}
@media print{.report-actions,.report-filter,.topbar,.sidebar{display:none!important}.report-shell{gap:.6rem}.report-card,.report-table-card,.report-kpi{box-shadow:none}}
</style>
@endpush

@section('content')
@php
    $maxChart = max(1, collect($chartDays)->max(fn ($day) => max($day['checkin'], $day['checkout'])) ?? 1);
    $maxHost = max(1, $topHosts->max('total') ?? 1);
    $maxDepartment = max(1, $topDepartments->max('total') ?? 1);
    $exportParams = [
        'from_date' => $filters['from_date'],
        'to_date' => $filters['to_date'],
        'status' => $filters['status'],
        'type' => 'visits',
    ];
@endphp

<div class="report-shell">
    <div class="report-toolbar">
        <div></div>
        <div class="report-actions">
            <a class="report-btn excel" href="{{ route('admin.reports.visits.export-xlsx', $exportParams) }}"><i class="bi bi-file-earmark-spreadsheet"></i> Xuất Excel</a>
            <a class="report-btn csv" href="{{ route('admin.reports.visits.export', $exportParams) }}"><i class="bi bi-filetype-csv"></i> Xuất CSV</a>
            <button class="report-btn print" type="button" onclick="window.print()"><i class="bi bi-printer"></i> In báo cáo</button>
        </div>
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
        <article class="report-kpi blue">
            <div><span class="report-kpi-label">Tổng khách</span><strong class="report-kpi-value">{{ $reportStats['total'] }}</strong><span class="report-kpi-note">{{ $reportStats['growth'] >= 0 ? 'Tăng' : 'Giảm' }} {{ abs($reportStats['growth']) }}% so với kỳ trước</span></div>
            <div class="report-kpi-icon"><i class="bi bi-people"></i></div>
        </article>
        <article class="report-kpi green">
            <div><span class="report-kpi-label">Trong công ty</span><strong class="report-kpi-value">{{ $reportStats['checked_in'] }}</strong><span class="report-kpi-note">Đang được theo dõi</span></div>
            <div class="report-kpi-icon"><i class="bi bi-person-check"></i></div>
        </article>
        <article class="report-kpi amber">
            <div><span class="report-kpi-label">Chờ duyệt</span><strong class="report-kpi-value">{{ $reportStats['pending_approval'] }}</strong><span class="report-kpi-note">Đang chờ người tiếp xử lý</span></div>
            <div class="report-kpi-icon"><i class="bi bi-hourglass-split"></i></div>
        </article>
        <article class="report-kpi red">
            <div><span class="report-kpi-label">Quá giờ</span><strong class="report-kpi-value">{{ $reportStats['overstay'] }}</strong><span class="report-kpi-note">Cần kiểm tra và xử lý</span></div>
            <div class="report-kpi-icon"><i class="bi bi-alarm"></i></div>
        </article>
    </section>

    <section class="report-overview-grid">
        <article class="report-card">
            <header class="report-card-head">
                <div><h3>Khách theo ngày</h3><p class="report-card-subtitle">So sánh lượt check-in và check-out trong khoảng đã chọn</p></div>
                <div class="chart-legend"><span><i class="chart-dot in"></i>Check-in</span><span><i class="chart-dot out"></i>Check-out</span></div>
            </header>
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
                    @foreach (collect($chartDays)->take(6) as $day)<span>{{ $day['label'] }}</span>@endforeach
                    @if (count($chartDays) > 6)<span>{{ collect($chartDays)->last()['label'] }}</span>@endif
                </div>
            </div>
        </article>

        <article class="report-card">
            <header class="report-card-head">
                <div><h3>Cảnh báo</h3><p class="report-card-subtitle">Các trường hợp cần ưu tiên xử lý</p></div>
                <span class="table-meta">{{ $reportAlerts->count() }} mục</span>
            </header>
            <div class="report-card-body alert-list">
                @forelse ($reportAlerts as $alert)
                    <a class="report-alert {{ $alert['tone'] }}" href="{{ $alert['url'] }}">
                        <span class="alert-icon"><i class="bi {{ $alert['icon'] }}"></i></span>
                        <span><span class="alert-title d-block">{{ $alert['title'] }}</span><span class="alert-detail">{{ $alert['detail'] }}</span></span>
                        <span class="alert-time">{{ $alert['time'] }}</span>
                    </a>
                @empty
                    <div class="report-empty"><i class="bi bi-check-circle me-1"></i> Không có cảnh báo trong khoảng thời gian này.</div>
                @endforelse
            </div>
        </article>
    </section>

    <section class="report-ranking-grid">
        <article class="report-card">
            <header class="report-card-head"><div><h3>Top nhân viên tiếp khách</h3><p class="report-card-subtitle">Xếp theo số lượt tiếp khách</p></div></header>
            <div class="report-card-body rank-list">
                @forelse ($topHosts as $index => $host)
                    <div class="rank-item">
                        <span class="rank-no">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                        <span class="rank-avatar">{{ strtoupper(mb_substr($host['name'], 0, 1)) }}</span>
                        <span><span class="rank-name d-block">{{ $host['name'] }}</span><span class="rank-sub">{{ $host['department'] }}</span></span>
                        <span class="rank-total">{{ $host['total'] }} lượt</span>
                        <span class="rank-bar"><span style="width: {{ round($host['total'] / $maxHost * 100) }}%"></span></span>
                    </div>
                @empty
                    <div class="report-empty">Chưa có dữ liệu nhân viên tiếp khách.</div>
                @endforelse
            </div>
        </article>

        <article class="report-card dept-rank">
            <header class="report-card-head"><div><h3>Top phòng ban</h3><p class="report-card-subtitle">Tỷ trọng khách theo phòng ban</p></div></header>
            <div class="report-card-body rank-list">
                @forelse ($topDepartments as $index => $department)
                    <div class="rank-item">
                        <span class="rank-no">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                        <span class="rank-avatar"><i class="bi bi-building"></i></span>
                        <span><span class="rank-name d-block">{{ $department['name'] }}</span><span class="rank-sub">{{ $department['percent'] }}% tổng lượt khách</span></span>
                        <span class="rank-total">{{ $department['total'] }} lượt</span>
                        <span class="rank-bar"><span style="width: {{ round($department['total'] / $maxDepartment * 100) }}%"></span></span>
                    </div>
                @empty
                    <div class="report-empty">Chưa có dữ liệu phòng ban.</div>
                @endforelse
            </div>
        </article>
    </section>

    <section class="report-table-card">
        <header class="report-table-head">
            <div><h3>Dữ liệu khách ra vào</h3><p class="report-card-subtitle">Chi tiết theo bộ lọc hiện tại</p></div>
            <span class="table-meta">{{ $reportVisits->count() }} / {{ $reportStats['total'] }} kết quả</span>
        </header>
        <div class="table-responsive">
            <table class="report-table">
                <thead>
                <tr><th>Mã lịch hẹn</th><th>Khách</th><th>Công ty</th><th>Người tiếp</th><th>Phòng ban</th><th>Check-in</th><th>Check-out</th><th>Thời gian ở lại</th><th>Trạng thái</th><th></th></tr>
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
                        <td><a class="report-btn" href="{{ route('admin.visits.show', $visit) }}" title="Xem chi tiết"><i class="bi bi-chevron-right"></i></a></td>
                    </tr>
                @empty
                    <tr><td colspan="10" class="text-center text-secondary py-4">Không có dữ liệu báo cáo theo bộ lọc hiện tại.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <footer class="report-footer">
            <span>Hiển thị {{ $reportVisits->count() }} trên {{ $reportStats['total'] }} kết quả</span>
            <span>Cập nhật lúc {{ now()->format('d/m/Y H:i') }}</span>
        </footer>
    </section>
</div>
@endsection
