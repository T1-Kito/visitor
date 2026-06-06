@extends('layouts.mobile')

@section('title', 'Báo cáo')

@push('styles')
<style>
.mr-page{display:grid;gap:12px}.mr-head{display:grid;grid-template-columns:42px 1fr auto;gap:10px;align-items:center}.mr-back{width:42px;height:42px;display:grid;place-items:center;border:1px solid #dbe7f3;border-radius:14px;background:#fff;color:#42617f;text-decoration:none}.mr-head h1{margin:0;color:#10233d;font-size:1.08rem;font-weight:600}.mr-head p{margin:2px 0 0;color:#7b8fa8;font-size:.72rem}.mr-export{display:flex;gap:6px}.mr-export a{width:38px;height:38px;display:grid;place-items:center;border:1px solid #dbe7f3;border-radius:12px;background:#fff;color:#1976d2;text-decoration:none}.mr-filter{display:grid;grid-template-columns:1fr 1fr;gap:8px;padding:12px;border:1px solid #e1ebf5;border-radius:18px;background:#fff}.mr-field{display:grid;gap:4px}.mr-field label{color:#6e83a0;font-size:.68rem}.mr-field input,.mr-field select{width:100%;min-height:40px;padding:0 9px;border:1px solid #d9e6f3;border-radius:11px;background:#fff;color:#203852;font:inherit;font-size:.76rem}.mr-field.full{grid-column:1/-1}.mr-filter button{grid-column:1/-1;min-height:40px;border:0;border-radius:11px;background:linear-gradient(135deg,#1976d2,#11a9c7);color:#fff;font:inherit;font-size:.8rem;font-weight:500}.mr-stats{display:grid;grid-template-columns:1fr 1fr;gap:8px}.mr-stat{padding:12px;border:1px solid #e2ebf5;border-radius:16px;background:#fff}.mr-stat i{width:30px;height:30px;display:grid;place-items:center;border-radius:10px;background:#edf6ff;color:#1976d2}.mr-stat strong{display:block;margin-top:8px;color:#10233d;font-size:1.2rem;font-weight:600}.mr-stat span{color:#7187a3;font-size:.7rem}.mr-stat.green i{background:#ecfdf5;color:#059669}.mr-stat.orange i{background:#fff7ed;color:#ea580c}.mr-stat.purple i{background:#f5f3ff;color:#7c3aed}.mr-section{padding:13px;border:1px solid #e1ebf5;border-radius:18px;background:#fff}.mr-section-head{display:flex;align-items:center;justify-content:space-between;gap:8px;margin-bottom:12px}.mr-section h2{margin:0;color:#10233d;font-size:.9rem;font-weight:600}.mr-section-head span{color:#7b8fa8;font-size:.68rem}.mr-chart{height:150px;display:flex;align-items:flex-end;gap:7px;padding-top:8px}.mr-bar-item{min-width:0;flex:1;display:grid;justify-items:center;gap:5px}.mr-bars{height:112px;width:100%;display:flex;align-items:flex-end;justify-content:center;gap:3px}.mr-bar{width:min(12px,40%);min-height:3px;border-radius:6px 6px 2px 2px;background:#bfd9f5}.mr-bar.in{background:linear-gradient(180deg,#22c55e,#10b981)}.mr-bar-item small{color:#8396ad;font-size:.58rem}.mr-legend{display:flex;justify-content:center;gap:14px;margin-top:8px;color:#7187a3;font-size:.65rem}.mr-dot{display:inline-block;width:8px;height:8px;margin-right:4px;border-radius:50%;background:#bfd9f5}.mr-dot.in{background:#10b981}.mr-depts{display:grid;gap:10px}.mr-dept-top{display:flex;justify-content:space-between;gap:10px;color:#29435f;font-size:.75rem}.mr-dept-top strong{font-weight:500}.mr-progress{height:7px;margin-top:5px;overflow:hidden;border-radius:999px;background:#edf3f8}.mr-progress span{display:block;height:100%;border-radius:inherit;background:linear-gradient(90deg,#1976d2,#11a9c7)}.mr-alert{display:grid;grid-template-columns:34px 1fr;gap:9px;align-items:center;padding:9px 0;border-bottom:1px solid #edf2f7;color:inherit;text-decoration:none}.mr-alert:last-child{border-bottom:0}.mr-alert i{width:34px;height:34px;display:grid;place-items:center;border-radius:11px;background:#fff1f2;color:#dc2626}.mr-alert strong,.mr-visit strong{display:block;color:#203852;font-size:.76rem;font-weight:500}.mr-alert span,.mr-visit span{display:block;margin-top:2px;color:#8194ab;font-size:.65rem}.mr-visits{display:grid}.mr-visit{display:grid;grid-template-columns:36px 1fr auto;gap:9px;align-items:center;padding:9px 0;border-bottom:1px solid #edf2f7;color:inherit;text-decoration:none}.mr-visit:last-child{border-bottom:0}.mr-avatar{width:36px;height:36px;display:grid;place-items:center;border-radius:12px;background:#edf6ff;color:#1976d2;font-size:.8rem;font-weight:600}.mr-status{padding:4px 7px;border-radius:999px;background:#eef4fa;color:#526b87!important;font-size:.6rem!important}.mr-empty{padding:16px 0;text-align:center;color:#879ab2;font-size:.74rem}@media(max-width:380px){.mr-head{grid-template-columns:38px 1fr}.mr-export{grid-column:1/-1;justify-content:flex-end}.mr-filter{grid-template-columns:1fr}.mr-field.full,.mr-filter button{grid-column:auto}}
</style>
@endpush

@section('content')
<div class="mr-page">
    <header class="mr-head">
        <a class="mr-back" href="{{ route('mobile.home') }}" aria-label="Quay lại"><i class="bi bi-chevron-left"></i></a>
        <div><h1>Báo cáo</h1><p>Theo dõi hoạt động khách ra/vào.</p></div>
        <div class="mr-export">
            <a href="{{ route('admin.reports.visits.export', [...$filters, 'type' => 'visits']) }}" title="Tải CSV"><i class="bi bi-filetype-csv"></i></a>
            <a href="{{ route('admin.reports.visits.export-xlsx', [...$filters, 'type' => 'visits']) }}" title="Tải Excel"><i class="bi bi-file-earmark-excel"></i></a>
        </div>
    </header>

    <form class="mr-filter" method="get" action="{{ route('mobile.reports') }}">
        <div class="mr-field"><label>Từ ngày</label><input type="date" name="from_date" value="{{ $filters['from_date'] }}"></div>
        <div class="mr-field"><label>Đến ngày</label><input type="date" name="to_date" value="{{ $filters['to_date'] }}"></div>
        <div class="mr-field full"><label>Trạng thái</label><select name="status">@foreach($statuses as $value => $label)<option value="{{ $value }}" @selected($filters['status'] === $value)>{{ $label }}</option>@endforeach</select></div>
        <button type="submit"><i class="bi bi-funnel"></i> Xem báo cáo</button>
    </form>

    <section class="mr-stats">
        <div class="mr-stat"><i class="bi bi-calendar3"></i><strong>{{ $stats['total'] }}</strong><span>Tổng lượt khách</span></div>
        <div class="mr-stat green"><i class="bi bi-building-check"></i><strong>{{ $stats['inside'] }}</strong><span>Trong công ty</span></div>
        <div class="mr-stat orange"><i class="bi bi-box-arrow-right"></i><strong>{{ $stats['checked_out'] }}</strong><span>Đã check-out</span></div>
        <div class="mr-stat purple"><i class="bi bi-hourglass-split"></i><strong>{{ $stats['pending'] }}</strong><span>Chờ duyệt</span></div>
    </section>

    <section class="mr-section">
        <div class="mr-section-head"><h2>Hoạt động 7 ngày gần nhất</h2><span>{{ $filters['from_date'] }} - {{ $filters['to_date'] }}</span></div>
        <div class="mr-chart">
            @foreach($chartDays as $day)
                <div class="mr-bar-item">
                    <div class="mr-bars">
                        <span class="mr-bar" style="height:{{ max(3, round($day['total'] / $chartMax * 100)) }}%"></span>
                        <span class="mr-bar in" style="height:{{ max(3, round($day['checkin'] / $chartMax * 100)) }}%"></span>
                    </div>
                    <small>{{ $day['label'] }}</small>
                </div>
            @endforeach
        </div>
        <div class="mr-legend"><span><i class="mr-dot"></i>Lịch hẹn</span><span><i class="mr-dot in"></i>Đã vào</span></div>
    </section>

    <section class="mr-section">
        <div class="mr-section-head"><h2>Phòng ban nổi bật</h2><span>Top {{ $topDepartments->count() }}</span></div>
        <div class="mr-depts">
            @forelse($topDepartments as $department)
                <div><div class="mr-dept-top"><strong>{{ $department['name'] }}</strong><span>{{ $department['total'] }} lượt</span></div><div class="mr-progress"><span style="width:{{ $department['percent'] }}%"></span></div></div>
            @empty
                <div class="mr-empty">Chưa có dữ liệu phòng ban.</div>
            @endforelse
        </div>
    </section>

    @if($alerts->isNotEmpty())
        <section class="mr-section">
            <div class="mr-section-head"><h2>Cần chú ý</h2><span>{{ $stats['overstay'] }} quá giờ</span></div>
            @foreach($alerts as $visit)
                <a class="mr-alert" href="{{ route('mobile.visits.show', $visit) }}"><i class="bi bi-alarm"></i><span><strong>{{ $visit->visitor?->full_name ?? '-' }}</strong><span>{{ $visit->code }} · Quá giờ {{ $visit->expected_checkout_at?->format('H:i d/m') }}</span></span></a>
            @endforeach
        </section>
    @endif

    <section class="mr-section">
        <div class="mr-section-head"><h2>Lượt khách gần nhất</h2><span>{{ $recentVisits->count() }} lượt</span></div>
        <div class="mr-visits">
            @forelse($recentVisits as $visit)
                <a class="mr-visit" href="{{ route('mobile.visits.show', $visit) }}">
                    <span class="mr-avatar">{{ strtoupper(mb_substr($visit->visitor?->full_name ?? 'K', 0, 1)) }}</span>
                    <span><strong>{{ $visit->visitor?->full_name ?? '-' }}</strong><span>{{ $visit->code }} · {{ $visit->scheduled_at?->format('H:i d/m') }}</span></span>
                    <span class="mr-status">{{ $statusLabels[$visit->status] ?? $visit->status }}</span>
                </a>
            @empty
                <div class="mr-empty">Không có dữ liệu trong khoảng đã chọn.</div>
            @endforelse
        </div>
    </section>
</div>
@endsection
