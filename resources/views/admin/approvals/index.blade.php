@extends('layouts.admin')

@section('title', 'Phê duyệt tiếp khách | Quản lý khách')
@section('page_title', 'Phê duyệt yêu cầu tiếp khách')
@section('page_subtitle', 'Xem thông tin, duyệt hoặc từ chối các yêu cầu tiếp khách')

@push('styles')
<style>
.ap-stat-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:1rem;margin-bottom:1rem}.ap-stat{display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:1rem;border:1px solid #e3edf8;border-radius:20px;background:#fff;box-shadow:0 12px 32px rgba(17,39,68,.05)}.ap-stat span{display:block;color:#5f7895;font-size:.74rem;font-weight:900}.ap-stat strong{display:block;margin:.25rem 0;color:#0b1f3a;font-size:1.9rem;font-weight:900}.ap-stat small{color:#7c93ad;font-size:.72rem}.ap-stat-icon{width:54px;height:54px;display:grid;place-items:center;border-radius:20px;font-size:1.35rem}.ap-amber{background:#fff7ed;color:#d97706}.ap-green{background:#ecfdf5;color:#059669}.ap-red{background:#fff1f2;color:#e11d48}.ap-blue{background:#eff6ff;color:#146bd7}
.ap-shell{display:grid;gap:1rem}.ap-main{background:#fff;border:1px solid #e3edf8;border-radius:22px;box-shadow:0 14px 36px rgba(17,39,68,.05);overflow:visible}.ap-filter{display:grid;grid-template-columns:1fr 150px 190px 190px auto;gap:.75rem;padding:1rem;border-bottom:1px solid #edf3fb}.ap-filter .form-control,.ap-filter .form-select{min-height:42px;border-color:#dce8f6;border-radius:12px;font-size:.82rem}.ap-filter .btn{min-height:42px;border-radius:12px;font-weight:900}.ap-tabs{display:flex;gap:1.15rem;padding:1rem 1rem 0;border-bottom:1px solid #edf3fb;overflow:auto}.ap-tab{position:relative;padding:0 0 .85rem;border:0;background:transparent;color:#526b87;font-size:.8rem;font-weight:900;white-space:nowrap}.ap-tab.active,.ap-tab:hover{color:#146bd7}.ap-tab.active:after{content:"";position:absolute;left:0;right:0;bottom:-1px;height:3px;border-radius:999px;background:#146bd7}.ap-tab em{font-style:normal;margin-left:.35rem;padding:.12rem .45rem;border-radius:999px;background:#edf5ff;color:#146bd7;font-size:.68rem}
.ap-list{display:grid;gap:.65rem;padding:1rem;background:#f8fbff;overflow-x:auto;overflow-y:visible}.ap-row{display:grid;grid-template-columns:48px 128px minmax(150px,1fr) minmax(145px,.9fr) minmax(105px,.65fr) minmax(105px,.65fr) minmax(140px,.9fr) 110px minmax(220px,auto);gap:.7rem;align-items:center;min-width:1180px;padding:.95rem;border:1px solid #e5eef9;border-radius:18px;background:#fff;box-shadow:0 10px 26px rgba(17,39,68,.035);transition:.16s}.ap-row:hover{transform:translateY(-1px);border-color:#bfd7f3;box-shadow:0 16px 34px rgba(17,39,68,.07)}.ap-row[data-status="approved"]{background:linear-gradient(90deg,#fff,#f4fdf8)}.ap-row[data-status="rejected"]{background:linear-gradient(90deg,#fff,#fff7f7)}.ap-avatar{width:42px;height:42px;display:grid;place-items:center;border-radius:14px;background:#eaf3ff;color:#146bd7;font-weight:900}.ap-cell-label{display:block;margin-bottom:.2rem;color:#7a93b0;font-size:.64rem;font-weight:900}.ap-code{display:block;color:#0b1f3a;font-size:.78rem;font-weight:900}.ap-created{display:block;margin-top:.2rem;color:#7a93b0;font-size:.64rem}.ap-primary{display:block;color:#0b1f3a;font-size:.82rem;font-weight:900}.ap-secondary{display:block;margin-top:.18rem;color:#526b87;font-size:.7rem}.ap-purpose{color:#0b1f3a;font-size:.78rem;font-weight:800}.ap-actions{display:flex;justify-content:flex-end;gap:.5rem;align-items:center;min-width:220px}.ap-btn{min-height:36px;display:inline-flex;align-items:center;justify-content:center;gap:.35rem;border-radius:11px;padding:.45rem .75rem;font-size:.74rem;font-weight:900;text-decoration:none;white-space:nowrap}.ap-btn-detail{border:1px solid #d8e5f2;background:#fff;color:#29435f}.ap-btn-approve{border:1px solid #bbf7d0;background:#ecfdf5;color:#059669}.ap-btn-reject{border:1px solid #fecaca;background:#fff7f7;color:#dc2626}.ap-done{border:1px solid #d8e5f2;background:#f8fbff;color:#526b87}.ap-wait{display:inline-flex;align-items:center;gap:.25rem;width:max-content;margin-top:.35rem;padding:.18rem .5rem;border-radius:999px;background:#fff7ed;color:#d97706;font-size:.66rem;font-weight:900}.ap-empty{padding:3rem;text-align:center;color:#7a93b0}.ap-empty i{display:block;margin-bottom:.5rem;color:#bfd3eb;font-size:2.4rem}.ap-footer{display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:1rem;color:#526b87;font-size:.8rem;background:#fff}.ap-pages{display:flex;gap:.4rem}.ap-pages span{width:34px;height:34px;display:grid;place-items:center;border:1px solid #d8e5f2;border-radius:10px;color:#29435f;font-weight:900}.ap-pages .active{background:#146bd7;border-color:#146bd7;color:#fff}
@media(max-width:1500px){.ap-filter{grid-template-columns:1fr 1fr}.ap-list{padding-bottom:1.25rem}}@media(max-width:768px){.ap-stat-grid,.ap-filter{grid-template-columns:1fr}.ap-row{grid-template-columns:1fr;min-width:0}.ap-actions{justify-content:flex-start;flex-wrap:wrap}.ap-avatar{display:none}}
</style>
@endpush

@section('content')
<section class="ap-stat-grid">
    <div class="ap-stat">
        <div><span>Chờ duyệt</span><strong>{{ $approvalStats['pending'] }}</strong><small>Yêu cầu cần xử lý</small></div>
        <div class="ap-stat-icon ap-amber"><i class="bi bi-hourglass-split"></i></div>
    </div>
    <div class="ap-stat">
        <div><span>Đã duyệt</span><strong>{{ $approvalStats['approved'] }}</strong><small>Yêu cầu đã thông qua</small></div>
        <div class="ap-stat-icon ap-green"><i class="bi bi-check2-circle"></i></div>
    </div>
    <div class="ap-stat">
        <div><span>Từ chối</span><strong>{{ $approvalStats['rejected'] }}</strong><small>Yêu cầu không phù hợp</small></div>
        <div class="ap-stat-icon ap-red"><i class="bi bi-x-lg"></i></div>
    </div>
    <div class="ap-stat">
        <div><span>Hôm nay</span><strong>{{ $approvalStats['today'] }}</strong><small>Tổng yêu cầu tiếp khách</small></div>
        <div class="ap-stat-icon ap-blue"><i class="bi bi-calendar-event"></i></div>
    </div>
</section>

<section class="ap-main">
    <div class="ap-filter">
        <input id="approvalSearch" class="form-control" placeholder="Tìm theo mã lịch, tên khách, công ty, người tiếp...">
        <input class="form-control" type="date" value="{{ now()->format('Y-m-d') }}">
        <select id="approvalDepartmentFilter" class="form-select">
            <option value="all">Tất cả phòng ban</option>
            @foreach (collect($approvalVisits)->pluck('department')->unique()->filter()->sort() as $department)
                <option value="{{ strtolower($department) }}">{{ $department }}</option>
            @endforeach
        </select>
        <select id="approvalStatusFilter" class="form-select">
            <option value="all">Tất cả trạng thái</option>
            <option value="pending">Chờ duyệt</option>
            <option value="approved">Đã duyệt</option>
            <option value="rejected">Từ chối</option>
        </select>
        <a class="btn btn-light" href="{{ route('admin.approvals.index') }}"><i class="bi bi-arrow-clockwise"></i> Làm mới</a>
    </div>

    <div class="ap-tabs">
        <button class="ap-tab active" type="button" data-approval-tab="all">Tất cả <em>{{ $approvalStats['all'] }}</em></button>
        <button class="ap-tab" type="button" data-approval-tab="pending">Chờ duyệt <em>{{ $approvalStats['pending'] }}</em></button>
        <button class="ap-tab" type="button" data-approval-tab="approved">Đã duyệt <em>{{ $approvalStats['approved'] }}</em></button>
        <button class="ap-tab" type="button" data-approval-tab="rejected">Từ chối <em>{{ $approvalStats['rejected'] }}</em></button>
    </div>

    <div class="ap-list" id="approvalList">
        @forelse ($approvalVisits as $visit)
            <article class="ap-row" data-status="{{ $visit['status'] }}" data-search="{{ strtolower($visit['code'].' '.$visit['visitor'].' '.$visit['company'].' '.$visit['host'].' '.$visit['department'].' '.$visit['purpose']) }}" data-department="{{ strtolower($visit['department']) }}">
                <div class="ap-avatar">{{ strtoupper(mb_substr($visit['visitor'], 0, 1)) }}</div>

                <div>
                    <span class="ap-code">{{ $visit['code'] }}</span>
                    <span class="ap-created">Tạo lúc: {{ $visit['created_time'] }}</span>
                </div>

                <div>
                    <span class="ap-cell-label">Khách</span>
                    <span class="ap-primary">{{ $visit['visitor'] }}</span>
                    <span class="ap-secondary">{{ $visit['company'] }}</span>
                </div>

                <div>
                    <span class="ap-cell-label">Người tiếp</span>
                    <span class="ap-primary">{{ $visit['host'] }}</span>
                    <span class="ap-secondary">Liên hệ nội bộ</span>
                </div>

                <div>
                    <span class="ap-cell-label">Phòng ban</span>
                    <span class="ap-primary">{{ $visit['department'] }}</span>
                </div>

                <div>
                    <span class="ap-cell-label">Giờ hẹn</span>
                    <span class="ap-primary"><i class="bi bi-clock"></i> {{ $visit['time'] }}</span>
                    <span class="ap-secondary">{{ $visit['date'] }}</span>
                </div>

                <div>
                    <span class="ap-cell-label">Mục đích</span>
                    <span class="ap-purpose">{{ $visit['purpose'] ?: 'Chưa có mục đích' }}</span>
                </div>

                <div class="ap-status-cell">
                    <span class="ap-cell-label">Trạng thái</span>
                    <x-status-badge :status="$visit['status']" />
                    @if ($visit['status'] === 'pending' && $visit['waiting_minutes'] >= 15)
                        <span class="ap-wait"><i class="bi bi-exclamation-circle"></i> Chờ lâu</span>
                    @endif
                </div>

                <div class="ap-actions">
                    <a class="ap-btn ap-btn-detail" href="{{ route('admin.visits.show', $visit['id']) }}"><i class="bi bi-eye"></i> Chi tiết</a>
                    @if ($visit['status'] === 'pending')
                        <form action="{{ route('admin.approvals.reject', $visit['id']) }}" method="post">
                            @csrf
                            <input type="hidden" name="reason" value="Yêu cầu tiếp khách không phù hợp.">
                            <button class="ap-btn ap-btn-reject" type="submit"><i class="bi bi-x"></i> Từ chối</button>
                        </form>
                        <form action="{{ route('admin.approvals.approve', $visit['id']) }}" method="post">
                            @csrf
                            <button class="ap-btn ap-btn-approve" type="submit"><i class="bi bi-check2"></i> Duyệt</button>
                        </form>
                    @else
                        <span class="ap-btn ap-done">Đã xử lý</span>
                    @endif
                </div>
            </article>
        @empty
            <div class="ap-empty">
                <i class="bi bi-check2-circle"></i>
                Chưa có yêu cầu phê duyệt.
            </div>
        @endforelse
    </div>

    <div class="ap-footer">
        <span>Hiển thị {{ count($approvalVisits) }} yêu cầu</span>
        <div class="ap-pages"><span class="active">1</span><span>2</span><span><i class="bi bi-chevron-right"></i></span></div>
    </div>
</section>
@endsection

@push('scripts')
<script>
(() => {
    const searchInput = document.getElementById('approvalSearch');
    const departmentFilter = document.getElementById('approvalDepartmentFilter');
    const statusFilter = document.getElementById('approvalStatusFilter');
    const cards = Array.from(document.querySelectorAll('#approvalList .ap-row'));
    const tabs = Array.from(document.querySelectorAll('[data-approval-tab]'));
    let tabStatus = 'all';

    const applyFilters = () => {
        const keyword = (searchInput.value || '').trim().toLowerCase();
        const department = departmentFilter.value;
        const selectedStatus = statusFilter.value;

        cards.forEach((card) => {
            const matchKeyword = keyword === '' || (card.dataset.search || '').includes(keyword);
            const matchDepartment = department === 'all' || card.dataset.department === department;
            const matchTab = tabStatus === 'all' || card.dataset.status === tabStatus;
            const matchSelect = selectedStatus === 'all' || card.dataset.status === selectedStatus;
            card.classList.toggle('d-none', !(matchKeyword && matchDepartment && matchTab && matchSelect));
        });
    };

    tabs.forEach((tab) => {
        tab.addEventListener('click', () => {
            tabs.forEach((item) => item.classList.remove('active'));
            tab.classList.add('active');
            tabStatus = tab.dataset.approvalTab || 'all';
            statusFilter.value = tabStatus;
            applyFilters();
        });
    });

    searchInput.addEventListener('input', applyFilters);
    departmentFilter.addEventListener('change', applyFilters);
    statusFilter.addEventListener('change', () => {
        tabStatus = statusFilter.value;
        tabs.forEach((item) => item.classList.toggle('active', item.dataset.approvalTab === tabStatus));
        if (tabStatus === 'all') {
            tabs.forEach((item) => item.classList.toggle('active', item.dataset.approvalTab === 'all'));
        }
        applyFilters();
    });
})();
</script>
@endpush
