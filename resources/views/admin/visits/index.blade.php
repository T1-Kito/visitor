@extends('layouts.admin')

@section('title', 'Quản lý lịch hẹn | Quản lý khách')
@section('page_title', 'Quản lý lịch hẹn')
@section('page_subtitle', 'Tìm kiếm, lọc, theo dõi và quản lý lịch hẹn khách đến')

@push('styles')
<style>
.vs-stat-grid{display:grid;grid-template-columns:repeat(6,minmax(0,1fr));gap:.85rem;margin-bottom:1rem}.vs-stat{display:flex;align-items:center;justify-content:space-between;gap:.75rem;padding:1rem;border:1px solid #e4edf8;border-radius:18px;background:#fff;box-shadow:0 2px 14px rgba(17,39,68,.04)}.vs-stat span{display:block;color:#5a7a99;font-size:.72rem;font-weight:800}.vs-stat strong{display:block;margin:.25rem 0;color:#0b1f3a;font-size:1.75rem;font-weight:900}.vs-stat small{color:#7a93b0;font-size:.7rem}.vs-ico{width:48px;height:48px;display:grid;place-items:center;border-radius:18px;font-size:1.25rem}.vs-blue{background:#eff6ff;color:#146bd7}.vs-amber{background:#fff7ed;color:#d97706}.vs-green{background:#ecfdf5;color:#059669}.vs-cyan{background:#eefbff;color:#0891b2}.vs-slate{background:#f1f5f9;color:#475569}.vs-red{background:#fff1f2;color:#e11d48}
.vs-layout{display:grid;grid-template-columns:minmax(680px,1fr) 310px;gap:1rem;align-items:start}.vs-main,.vs-side-card{background:#fff;border:1px solid #e4edf8;border-radius:22px;box-shadow:0 2px 16px rgba(17,39,68,.05);overflow:hidden}.vs-tabs{display:flex;gap:1.2rem;padding:1rem 1.15rem 0;border-bottom:1px solid #edf3fb;overflow:auto}.vs-tab{position:relative;padding:0 0 .8rem;border:0;background:transparent;color:#526b87;font-size:.8rem;font-weight:800;white-space:nowrap}.vs-tab.active,.vs-tab:hover{color:#146bd7}.vs-tab.active:after{content:"";position:absolute;left:0;right:0;bottom:-1px;height:3px;border-radius:999px;background:#146bd7}.vs-tab em{font-style:normal;margin-left:.35rem;padding:.1rem .45rem;border-radius:999px;background:#edf5ff;color:#146bd7;font-size:.68rem}
.vs-filter{display:grid;grid-template-columns:1fr 150px 170px auto;gap:.65rem;padding:1rem 1.15rem}.vs-filter .form-control,.vs-filter .form-select{min-height:42px;border-color:#dce8f6;border-radius:12px;font-size:.82rem}.vs-card-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:.85rem;padding:0 1.15rem 1rem}.vs-card{display:grid;gap:.7rem;padding:.85rem;border:1px solid #e5eef9;border-radius:16px;background:#fff;box-shadow:0 10px 24px rgba(17,39,68,.04);transition:.16s}.vs-card:hover{transform:translateY(-2px);box-shadow:0 16px 34px rgba(17,39,68,.08);border-color:#bfd7f3}.vs-card-top{display:flex;align-items:flex-start;justify-content:space-between;gap:.6rem}.vs-code{color:#0b1f3a;font-size:.78rem;font-weight:900}.vs-time{color:#0b1f3a;font-size:.76rem;font-weight:800}.vs-person{display:flex;gap:.65rem;align-items:center}.vs-avatar{width:42px;height:42px;display:grid;place-items:center;border-radius:50%;background:#f3e8ff;color:#7e22ce;font-weight:900}.vs-name{color:#0b1f3a;font-size:.9rem;font-weight:900}.vs-sub{color:#7a93b0;font-size:.72rem}.vs-meta{display:grid;gap:.25rem;color:#5a7a99;font-size:.7rem}.vs-meta b{color:#0b1f3a}.vs-actions{display:grid;grid-template-columns:1fr 1fr;gap:.55rem;margin-top:.15rem}.vs-actions form{margin:0}.vs-btn{display:flex;align-items:center;justify-content:center;min-height:36px;border-radius:10px;font-size:.76rem;font-weight:800;text-decoration:none}.vs-btn-light{border:1px solid #d8e5f2;color:#334b67;background:#fff}.vs-btn-blue{border:0;color:#fff;background:linear-gradient(135deg,#146bd7,#0cb4d8)}.vs-btn-red{border:0;color:#fff;background:linear-gradient(135deg,#dc2626,#ef4444)}.vs-btn-green{border:0;color:#fff;background:linear-gradient(135deg,#059669,#10b981)}
.vs-side{display:grid;gap:1rem;align-self:start;align-content:start}.vs-side-card{padding:1rem}.vs-side-title{display:flex;align-items:center;justify-content:space-between;margin-bottom:.85rem}.vs-side-title h3{margin:0;color:#0b1f3a;font-size:.9rem;font-weight:900}.vs-quick{display:grid;grid-template-columns:1fr 1fr;gap:.65rem}.vs-quick a{display:grid;place-items:center;gap:.35rem;min-height:72px;border:1px solid #dce8f6;border-radius:14px;color:#146bd7;background:#fbfdff;font-size:.75rem;font-weight:800;text-decoration:none;text-align:center}.vs-task{position:relative;display:flex;align-items:center;justify-content:space-between;gap:.75rem;padding:.78rem .72rem .78rem .85rem;border:1px solid #edf3fb;border-radius:16px;background:#fbfdff;overflow:hidden;transition:.16s}.vs-task:before{content:"";position:absolute;left:0;top:0;bottom:0;width:4px;background:#146bd7}.vs-task:hover{transform:translateY(-1px);box-shadow:0 10px 24px rgba(17,39,68,.08)}.vs-task strong{display:block;color:#0b1f3a;font-size:.78rem}.vs-task span{color:#5a6f8a;font-size:.7rem}.vs-task-icon{width:34px;height:34px;display:grid;place-items:center;flex:0 0 auto;border-radius:12px;font-size:1rem}.vs-task-content{display:flex;align-items:center;gap:.65rem;min-width:0}.vs-task-overstay{background:#fff7ed;border-color:#fed7aa}.vs-task-overstay:before{background:#f97316}.vs-task-overstay .vs-task-icon{background:#ffedd5;color:#ea580c}.vs-task-pending{background:#fffbeb;border-color:#fde68a}.vs-task-pending:before{background:#f59e0b}.vs-task-pending .vs-task-icon{background:#fef3c7;color:#d97706}.vs-task-approved{background:#eff6ff;border-color:#bfdbfe}.vs-task-approved:before{background:#2563eb}.vs-task-approved .vs-task-icon{background:#dbeafe;color:#2563eb}.vs-upcoming{display:grid;gap:.75rem}.vs-upcoming-row{display:grid;grid-template-columns:44px 1fr auto;gap:.65rem;align-items:center}.vs-upcoming-row time{color:#146bd7;font-size:.75rem;font-weight:900}.vs-empty{padding:2rem;text-align:center;color:#7a93b0}.vs-pager{display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:0 1.15rem 1rem;border-top:1px solid #edf3fb}.vs-pager-info{color:#5a7a99;font-size:.78rem;font-weight:700}.vs-pager-actions{display:flex;align-items:center;gap:.45rem}.vs-page-btn{min-width:36px;height:36px;border:1px solid #d8e5f2;border-radius:10px;background:#fff;color:#334b67;font-size:.78rem;font-weight:800}.vs-page-btn.active{border-color:#146bd7;background:#146bd7;color:#fff}.vs-page-btn:disabled{opacity:.45;cursor:not-allowed}.vs-page-size{width:auto;min-height:36px;border-color:#d8e5f2;border-radius:10px;color:#334b67;font-size:.78rem;font-weight:800}
@media(max-width:1400px){.vs-stat-grid{grid-template-columns:repeat(3,1fr)}.vs-layout{grid-template-columns:1fr}.vs-card-grid{grid-template-columns:repeat(2,1fr)}}@media(max-width:768px){.vs-stat-grid,.vs-card-grid{grid-template-columns:1fr}.vs-filter{grid-template-columns:1fr}.vs-quick{grid-template-columns:1fr}.vs-pager{align-items:flex-start;flex-direction:column}.vs-pager-actions{flex-wrap:wrap}}
.vs-stat span,.vs-tab,.vs-btn,.vs-quick a,.vs-pager-info,.vs-page-btn,.vs-page-size{font-weight:500}.vs-stat strong{font-weight:650}.vs-code,.vs-time,.vs-name,.vs-avatar,.vs-side-title h3,.vs-task strong,.vs-upcoming-row time{font-weight:600}.vs-meta b{font-weight:600}.vs-btn-blue,.vs-btn-red,.vs-btn-green,.vs-page-btn.active{font-weight:600}.vs-card{box-shadow:0 8px 22px rgba(17,39,68,.035)}.vs-main,.vs-side-card{box-shadow:0 10px 30px rgba(17,39,68,.045)}
</style>
@endpush

@section('content')
<section class="vs-stat-grid">
    <div class="vs-stat"><div><span>Tổng số lịch</span><strong>{{ $visitStats['today'] }}</strong><small>Hôm nay</small></div><div class="vs-ico vs-blue"><i class="bi bi-calendar-event"></i></div></div>
    <div class="vs-stat"><div><span>Chờ duyệt</span><strong>{{ $visitStats['pending'] }}</strong><small>Cần xử lý</small></div><div class="vs-ico vs-amber"><i class="bi bi-hourglass-split"></i></div></div>
    <div class="vs-stat"><div><span>Đã duyệt</span><strong>{{ $visitStats['approved'] }}</strong><small>Sẵn sàng tiếp nhận</small></div><div class="vs-ico vs-green"><i class="bi bi-check2-circle"></i></div></div>
    <div class="vs-stat"><div><span>Đang trong công ty</span><strong>{{ $visitStats['checked_in'] }}</strong><small>Đang theo dõi</small></div><div class="vs-ico vs-cyan"><i class="bi bi-person-bounding-box"></i></div></div>
    <div class="vs-stat"><div><span>Đã rời công ty</span><strong>{{ $visitStats['checked_out'] }}</strong><small>Hoàn tất</small></div><div class="vs-ico vs-slate"><i class="bi bi-box-arrow-left"></i></div></div>
    <div class="vs-stat"><div><span>Quá giờ</span><strong>{{ $visitStats['overstay'] }}</strong><small>Cần kiểm tra</small></div><div class="vs-ico vs-red"><i class="bi bi-alarm"></i></div></div>
</section>

<div class="vs-layout">
    <section class="vs-main">
        <div class="vs-tabs" id="visitTabs">
            @foreach ($statusFilters as $value => $label)
                <button class="vs-tab {{ $value === 'all' ? 'active' : '' }}" type="button" data-status-tab="{{ $value }}">
                    {{ $label }} <em>{{ $statusCounts[$value] ?? 0 }}</em>
                </button>
            @endforeach
        </div>

        <div class="vs-filter">
            <input id="visitSearch" class="form-control" placeholder="Tìm theo mã lịch, tên khách, người gặp...">
            <input class="form-control" type="date" value="{{ now()->format('Y-m-d') }}">
            <select id="departmentFilter" class="form-select">
                <option value="all">Tất cả phòng ban</option>
                @foreach (collect($visits)->pluck('department')->unique()->filter()->sort() as $department)
                    <option value="{{ strtolower($department) }}">{{ $department }}</option>
                @endforeach
            </select>
            <button class="btn btn-light" type="button"><i class="bi bi-funnel"></i> Bộ lọc</button>
        </div>

        <div class="vs-card-grid" id="visitsGrid">
            @forelse ($visits as $visit)
                <article class="vs-card" data-search="{{ strtolower($visit['code'].' '.$visit['visitor'].' '.$visit['host'].' '.$visit['department']) }}" data-status="{{ $visit['status'] }}" data-department="{{ strtolower($visit['department']) }}">
                    <div class="vs-card-top">
                        <div>
                            <div class="vs-code">{{ $visit['code'] }}</div>
                            <x-status-badge :status="$visit['status']" />
                        </div>
                        <div class="vs-time">{{ $visit['time'] }}</div>
                    </div>
                    <div class="vs-person">
                        <div class="vs-avatar">{{ strtoupper(mb_substr($visit['visitor'], 0, 1)) }}</div>
                        <div>
                            <div class="vs-name">{{ $visit['visitor'] }}</div>
                            <div class="vs-sub">{{ $visit['purpose'] ?: 'Chưa có mục đích' }}</div>
                        </div>
                    </div>
                    <div class="vs-meta">
                        <div>Người gặp: <b>{{ $visit['host'] }}</b></div>
                        <div>Phòng ban: <b>{{ $visit['department'] }}</b></div>
                    </div>
                    <div class="vs-actions">
                        <a class="vs-btn vs-btn-light" href="{{ route('admin.visits.show', $visit['id']) }}">Chi tiết</a>
                        @if ($visit['status'] === 'pending')
                            <form action="{{ route('admin.approvals.approve', $visit['id']) }}" method="post">@csrf<button class="vs-btn vs-btn-green w-100" type="submit">Duyệt</button></form>
                        @elseif ($visit['status'] === 'approved')
                            <form action="{{ route('admin.checkin.confirm', $visit['id']) }}" method="post">@csrf<button class="vs-btn vs-btn-blue w-100" type="submit">Cho khách vào</button></form>
                        @elseif ($visit['status'] === 'checked_in')
                            <form action="{{ route('admin.checkout.confirm', $visit['id']) }}" method="post">@csrf<button class="vs-btn vs-btn-red w-100" type="submit">Cho khách ra</button></form>
                        @else
                            <span class="vs-btn vs-btn-light">Đã xử lý</span>
                        @endif
                    </div>
                </article>
            @empty
                <div class="vs-empty">Chưa có lịch hẹn.</div>
            @endforelse
        </div>

        <div class="vs-pager" id="visitPager">
            <div class="vs-pager-info" id="visitPagerInfo">0 lịch</div>
            <div class="vs-pager-actions">
                <button class="vs-page-btn" type="button" id="visitPrevPage" aria-label="Trang trước"><i class="bi bi-chevron-left"></i></button>
                <div class="vs-pager-actions" id="visitPageNumbers"></div>
                <button class="vs-page-btn" type="button" id="visitNextPage" aria-label="Trang sau"><i class="bi bi-chevron-right"></i></button>
            </div>
        </div>
    </section>

    <aside class="vs-side">
        <section class="vs-side-card">
            <div class="vs-side-title"><h3>Thao tác nhanh</h3></div>
            <div class="vs-quick">
                <a href="{{ route('admin.visits.create') }}"><i class="bi bi-calendar-plus"></i> Tạo lịch hẹn</a>
                <a href="{{ route('kiosk.index') }}" target="_blank"><i class="bi bi-person-plus"></i> Khách vãng lai</a>
                <a href="{{ route('admin.access.index') }}"><i class="bi bi-qr-code-scan"></i> Làm thủ tục ra/vào</a>
                <a href="{{ route('admin.reports.index') }}"><i class="bi bi-file-earmark-arrow-down"></i> Xuất báo cáo</a>
            </div>
        </section>

        <section class="vs-side-card">
            <div class="vs-side-title"><h3>Công việc cần xử lý</h3></div>
            <div class="d-grid gap-2">
                <div class="vs-task vs-task-overstay">
                    <div class="vs-task-content"><span class="vs-task-icon"><i class="bi bi-alarm"></i></span><div><strong>{{ $visitStats['overstay'] }} khách quá giờ</strong><span>Cần xử lý ngay</span></div></div>
                    <i class="bi bi-chevron-right"></i>
                </div>
                <div class="vs-task vs-task-pending">
                    <div class="vs-task-content"><span class="vs-task-icon"><i class="bi bi-hourglass-split"></i></span><div><strong>{{ $visitStats['pending'] }} khách chờ duyệt</strong><span>Xem và phê duyệt</span></div></div>
                    <i class="bi bi-chevron-right"></i>
                </div>
                <div class="vs-task vs-task-approved">
                    <div class="vs-task-content"><span class="vs-task-icon"><i class="bi bi-calendar-check"></i></span><div><strong>{{ $visitStats['approved'] }} khách sắp đến</strong><span>Theo dõi làm thủ tục vào</span></div></div>
                    <i class="bi bi-chevron-right"></i>
                </div>
            </div>
        </section>

        <section class="vs-side-card">
            <div class="vs-side-title"><h3>Lịch sắp đến</h3><a class="small text-decoration-none" href="{{ route('admin.visits.index') }}">Xem tất cả</a></div>
            <div class="vs-upcoming">
                @forelse ($upcomingVisits as $visit)
                    <div class="vs-upcoming-row">
                        <time>{{ $visit['time'] }}</time>
                        <div><strong>{{ $visit['visitor'] }}</strong><div class="vs-sub">{{ $visit['host'] }}</div></div>
                        <span class="ci-pill ci-pill-blue">Sắp đến</span>
                    </div>
                @empty
                    <div class="vs-empty py-3">Không có lịch sắp đến.</div>
                @endforelse
            </div>
        </section>
    </aside>
</div>
@endsection

@push('scripts')
<script>
(() => {
    const searchInput = document.getElementById('visitSearch');
    const departmentFilter = document.getElementById('departmentFilter');
    const cards = Array.from(document.querySelectorAll('#visitsGrid .vs-card'));
    const tabs = Array.from(document.querySelectorAll('[data-status-tab]'));
    const pager = document.getElementById('visitPager');
    const pagerInfo = document.getElementById('visitPagerInfo');
    const pageNumbers = document.getElementById('visitPageNumbers');
    const prevPage = document.getElementById('visitPrevPage');
    const nextPage = document.getElementById('visitNextPage');
    let status = 'all';
    let currentPage = 1;
    let filteredCards = cards;

    const applyFilters = () => {
        const keyword = (searchInput.value || '').trim().toLowerCase();
        const department = departmentFilter.value;

        filteredCards = cards.filter((card) => {
            const matchKeyword = keyword === '' || (card.dataset.search || '').includes(keyword);
            const matchStatus = status === 'all' || card.dataset.status === status;
            const matchDepartment = department === 'all' || card.dataset.department === department;
            return matchKeyword && matchStatus && matchDepartment;
        });

        currentPage = 1;
        renderPage();
    };

    const renderPage = () => {
        const pageSize = 9;
        const total = filteredCards.length;
        const totalPages = Math.max(1, Math.ceil(total / pageSize));
        currentPage = Math.min(Math.max(currentPage, 1), totalPages);
        const start = (currentPage - 1) * pageSize;
        const end = start + pageSize;

        cards.forEach((card) => card.classList.add('d-none'));
        filteredCards.slice(start, end).forEach((card) => card.classList.remove('d-none'));

        pager.classList.toggle('d-none', cards.length === 0);
        pagerInfo.textContent = total === 0
            ? 'Không có lịch phù hợp'
            : `${start + 1} - ${Math.min(end, total)} / ${total} lịch`;

        prevPage.disabled = currentPage === 1;
        nextPage.disabled = currentPage === totalPages;
        pageNumbers.innerHTML = '';

        const visiblePages = Array.from({ length: totalPages }, (_, index) => index + 1)
            .filter((page) => page === 1 || page === totalPages || Math.abs(page - currentPage) <= 1);
        let lastPage = 0;

        visiblePages.forEach((page) => {
            if (page - lastPage > 1) {
                const dots = document.createElement('span');
                dots.className = 'vs-pager-info';
                dots.textContent = '...';
                pageNumbers.appendChild(dots);
            }

            const button = document.createElement('button');
            button.type = 'button';
            button.className = `vs-page-btn ${page === currentPage ? 'active' : ''}`;
            button.textContent = page;
            button.addEventListener('click', () => {
                currentPage = page;
                renderPage();
            });
            pageNumbers.appendChild(button);
            lastPage = page;
        });
    };

    tabs.forEach((tab) => {
        tab.addEventListener('click', () => {
            tabs.forEach((item) => item.classList.remove('active'));
            tab.classList.add('active');
            status = tab.dataset.statusTab || 'all';
            applyFilters();
        });
    });

    searchInput.addEventListener('input', applyFilters);
    departmentFilter.addEventListener('change', applyFilters);
    prevPage.addEventListener('click', () => {
        currentPage -= 1;
        renderPage();
    });
    nextPage.addEventListener('click', () => {
        currentPage += 1;
        renderPage();
    });

    applyFilters();
})();
</script>
@endpush
