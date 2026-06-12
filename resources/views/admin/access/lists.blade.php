@extends('layouts.admin')

@section('title', 'Danh sách ra/vào')
@section('page_title', 'Danh sách ra/vào')
@section('page_subtitle', 'Tra cứu khách đang trong công ty, khách vào và khách ra')

@push('styles')
<style>
.al-page{display:grid;grid-template-rows:auto minmax(0,1fr);gap:.75rem;min-height:100%;color:#10233d}.al-btn{min-height:40px;display:inline-flex;align-items:center;justify-content:center;gap:.42rem;padding:.55rem .85rem;border:1px solid #dbe7f4;border-radius:13px;background:#fff;color:#315b89;text-decoration:none;font-size:.82rem;font-weight:500}.al-btn.primary{border:0;color:#fff;background:linear-gradient(135deg,#1976d2,#11a9c7);box-shadow:0 12px 24px rgba(20,107,215,.13)}.al-tabs{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:.65rem}.al-tab{position:relative;overflow:hidden;display:grid;gap:.1rem;padding:.85rem .95rem;border:1px solid #e2edf8;border-radius:18px;background:#fff;text-decoration:none;color:#10233d;box-shadow:0 10px 22px rgba(17,39,68,.035)}.al-tab::after{content:"";position:absolute;right:-20px;top:-22px;width:76px;height:76px;border-radius:999px;background:var(--glow,rgba(20,107,215,.08))}.al-tab span,.al-tab strong{position:relative;z-index:1}.al-tab span{color:#667d98;font-size:.75rem}.al-tab strong{font-size:1.05rem;font-weight:600}.al-tab.active{border-color:#9dccff;background:#f7fbff}.al-tab.inside{--glow:rgba(86,190,132,.18)}.al-tab.in{--glow:rgba(157,124,231,.18)}.al-tab.out{--glow:rgba(245,158,87,.18)}.al-tab.all{--glow:rgba(125,181,255,.18)}.al-panel{display:flex;min-height:100%;flex-direction:column;border:1px solid #e2edf8;border-radius:18px;background:#fff;box-shadow:0 10px 28px rgba(17,39,68,.04);overflow:hidden}.al-filter{display:grid;grid-template-columns:minmax(220px,1fr) 150px 150px 180px auto;gap:.6rem;align-items:end;padding:1rem;border-bottom:1px solid #eef4fb}.al-field{display:grid;gap:.3rem}.al-field label{color:#667d98;font-size:.72rem;font-weight:500}.al-field input,.al-field select{min-height:42px;border:1px solid #d8e5f2;border-radius:12px;color:#10233d;font-size:.84rem}.al-table{display:grid}.al-row,.al-head{display:grid;grid-template-columns:130px 128px minmax(220px,1.25fr) minmax(170px,1fr) minmax(130px,.8fr) 135px 135px;gap:.75rem;align-items:center}.al-head{padding:.78rem 1rem;color:#7187a3;font-size:.68rem;font-weight:600;text-transform:uppercase;border-bottom:1px solid #eef4fb}.al-row{padding:.82rem 1rem;border-bottom:1px solid #f1f5fa;color:#10233d;text-decoration:none}.al-row:hover{background:#f7fbff}.al-code{color:#1976d2;font-weight:600}.al-badge{display:inline-flex;width:max-content;align-items:center;gap:.32rem;padding:.25rem .58rem;border-radius:999px;font-size:.72rem;font-weight:500}.al-badge.inside{background:#ecfdf5;color:#047857}.al-badge.in{background:#f2edff;color:#6d4fc2}.al-badge.out{background:#fff7ed;color:#c2410c}.al-person{display:flex;align-items:center;gap:.58rem;min-width:0}.al-avatar{width:34px;height:34px;display:grid;place-items:center;border-radius:12px;background:#edf6ff;color:#1976d2;font-weight:600}.al-main{font-size:.86rem;font-weight:600}.al-muted{display:block;color:#7187a3;font-size:.72rem;font-weight:400;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.al-empty{padding:2rem;text-align:center;color:#879ab2}.al-empty i{display:block;margin-bottom:.35rem;color:#bdd5ee;font-size:2rem}.al-pagination{display:flex;align-items:center;justify-content:space-between;gap:1rem;margin-top:auto;padding:.75rem 1rem;border-top:1px solid #eef4fb;color:#7187a3;font-size:.76rem}.al-page-controls{display:flex;align-items:center;gap:.45rem}.al-page-control{width:34px;height:34px;display:grid;place-items:center;border:1px solid #dbe7f4;border-radius:10px;background:#fff;color:#315b89;text-decoration:none}.al-page-control:hover{border-color:var(--gate-blue);color:var(--gate-blue)}.al-page-control.disabled{color:#b8c5d3;pointer-events:none}.al-page-current{min-width:78px;text-align:center;color:#526b87;font-weight:500}@media(max-width:1300px){.al-filter{grid-template-columns:1fr 1fr}.al-head{display:none}.al-row{grid-template-columns:1fr;gap:.35rem}.al-tabs{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:768px){.al-page{min-height:auto}.al-btn{width:100%}.al-filter{grid-template-columns:1fr}.al-tabs{grid-template-columns:1fr}.al-pagination{align-items:flex-start;flex-direction:column}}
</style>
@endpush

@section('content')
@php
    $type = $filters['type'];
    $queryBase = ['from' => $filters['from'], 'to' => $filters['to'], 'q' => $filters['q'], 'department' => $filters['department']];
@endphp

<div class="al-page">
    <section class="al-tabs">
        <a class="al-tab inside {{ $type === 'inside' ? 'active' : '' }}" href="{{ route('admin.access.lists', array_merge($queryBase, ['type' => 'inside'])) }}"><span>Đang trong công ty</span><strong>{{ $listStats['inside'] }}</strong></a>
        <a class="al-tab in {{ $type === 'in' ? 'active' : '' }}" href="{{ route('admin.access.lists', array_merge($queryBase, ['type' => 'in'])) }}"><span>Khách vào hôm nay</span><strong>{{ $listStats['in_today'] }}</strong></a>
        <a class="al-tab out {{ $type === 'out' ? 'active' : '' }}" href="{{ route('admin.access.lists', array_merge($queryBase, ['type' => 'out'])) }}"><span>Khách ra hôm nay</span><strong>{{ $listStats['out_today'] }}</strong></a>
        <a class="al-tab all {{ $type === 'all' ? 'active' : '' }}" href="{{ route('admin.access.lists', array_merge($queryBase, ['type' => 'all'])) }}"><span>Tất cả theo lọc</span><strong>{{ $listStats['all_range'] }}</strong></a>
    </section>

    <section class="al-panel">
        <form class="al-filter" method="get" action="{{ route('admin.access.lists') }}">
            <input type="hidden" name="type" value="{{ $type }}">
            <div class="al-field">
                <label>Tìm kiếm</label>
                <input class="form-control" name="q" value="{{ $filters['q'] }}" placeholder="Mã lịch, tên khách, công ty, SĐT...">
            </div>
            <div class="al-field">
                <label>Từ ngày</label>
                <input class="form-control" type="date" name="from" value="{{ $filters['from'] }}">
            </div>
            <div class="al-field">
                <label>Đến ngày</label>
                <input class="form-control" type="date" name="to" value="{{ $filters['to'] }}">
            </div>
            <div class="al-field">
                <label>Phòng ban</label>
                <select class="form-select" name="department">
                    <option value="">Tất cả phòng ban</option>
                    @foreach ($departments as $department)
                        <option value="{{ $department }}" @selected($filters['department'] === $department)>{{ $department }}</option>
                    @endforeach
                </select>
            </div>
            <button class="al-btn primary" type="submit"><i class="bi bi-funnel"></i> Lọc</button>
        </form>

        @if (count($accessRows) > 0)
            <div class="al-head"><span>Mã lịch</span><span>Loại</span><span>Khách</span><span>Người gặp</span><span>Phòng ban</span><span>Giờ vào</span><span>Giờ ra</span></div>
            @foreach ($accessRows as $row)
                <a class="al-row" href="{{ $row['detail_url'] }}">
                    <span class="al-code">{{ $row['code'] }}</span>
                    <span><span class="al-badge {{ $row['badge_type'] }}"><i class="bi {{ $row['badge_type'] === 'out' ? 'bi-box-arrow-left' : 'bi-box-arrow-in-right' }}"></i>{{ $row['label'] }}</span></span>
                    <span class="al-person"><span class="al-avatar">{{ strtoupper(mb_substr($row['visitor'], 0, 1)) }}</span><span><span class="al-main d-block">{{ $row['visitor'] }}</span><span class="al-muted">{{ $row['company'] }} · {{ $row['phone'] }}</span></span></span>
                    <span><span class="al-main">{{ $row['host'] }}</span><span class="al-muted">{{ $row['purpose'] }}</span></span>
                    <span class="al-main">{{ $row['department'] }}</span>
                    <span class="al-main">{{ $row['checkin_at'] }}</span>
                    <span class="al-main">{{ $row['checkout_at'] }}</span>
                </a>
            @endforeach
        @else
            <div class="al-empty"><i class="bi bi-inboxes"></i>Không có dữ liệu phù hợp với bộ lọc.</div>
        @endif

        @if ($accessRows->total() > 0)
            <div class="al-pagination">
                <span>
                    Hiển thị {{ $accessRows->firstItem() }}-{{ $accessRows->lastItem() }}
                    trong {{ $accessRows->total() }} lượt ra/vào
                </span>
                @if ($accessRows->hasPages())
                    <nav class="al-page-controls" aria-label="Phân trang danh sách ra vào">
                        <a class="al-page-control {{ $accessRows->onFirstPage() ? 'disabled' : '' }}"
                           href="{{ $accessRows->previousPageUrl() ?: '#' }}"
                           aria-label="Trang trước">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                        <span class="al-page-current">
                            Trang {{ $accessRows->currentPage() }}/{{ $accessRows->lastPage() }}
                        </span>
                        <a class="al-page-control {{ $accessRows->hasMorePages() ? '' : 'disabled' }}"
                           href="{{ $accessRows->nextPageUrl() ?: '#' }}"
                           aria-label="Trang sau">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </nav>
                @endif
            </div>
        @endif
    </section>
</div>
@endsection
