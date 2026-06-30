@extends('layouts.admin')

@section('title', 'Khách cần duyệt | Quản lý khách')
@section('page_title', 'Khách cần duyệt')
@section('page_subtitle', 'Duyệt hoặc từ chối yêu cầu tiếp khách được gửi đến bạn')

@push('styles')
<style>
.workspace{padding:0!important}
.content-panel,
.workspace,
.topbar{background:#fff!important}
.topbar{min-height:74px;padding:10px 20px}
.topbar .page-title{font-size:20px;font-weight:700}
.topbar .page-subtitle{margin-top:2px;font-size:13px}
.topbar-right .btn{min-height:38px}
.ap-stat-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:1rem;margin-bottom:1rem}.ap-stat{display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:1rem;border:1px solid #e3edf8;border-radius:20px;background:#fff;box-shadow:0 12px 32px rgba(17,39,68,.05);min-width:0}.ap-stat span{display:block;color:#5f7895;font-size:.74rem;font-weight:500}.ap-stat strong{display:block;margin:.25rem 0;color:#0b1f3a;font-size:clamp(1.45rem,2vw,1.9rem);font-weight:700}.ap-stat small{color:#7c93ad;font-size:.72rem}.ap-stat-icon{width:54px;height:54px;display:grid;place-items:center;border-radius:20px;font-size:1.35rem;flex:0 0 auto}.ap-amber{background:#fff7ed;color:#d97706}.ap-green{background:#ecfdf5;color:#059669}.ap-red{background:#fff1f2;color:#e11d48}.ap-blue{background:#eff6ff;color:#146bd7}.ap-main{background:#fff;border:1px solid #e3edf8;border-radius:22px;box-shadow:0 14px 36px rgba(17,39,68,.05);overflow:hidden;max-width:100%}.ap-filter{display:grid;grid-template-columns:minmax(220px,1fr) minmax(135px,.35fr) minmax(170px,.45fr) minmax(170px,.45fr) auto;gap:.75rem;padding:1rem;border-bottom:1px solid #edf3fb}.ap-filter .form-control,.ap-filter .form-select{min-height:42px;border-color:#dce8f6;border-radius:12px;font-size:.82rem}.ap-filter .btn{min-height:42px;border-radius:12px;font-weight:600;white-space:nowrap}.ap-tabs{display:flex;gap:1.15rem;padding:1rem 1rem 0;border-bottom:1px solid #edf3fb;overflow-x:auto;scrollbar-width:thin}.ap-tab{position:relative;padding:0 0 .85rem;border:0;background:transparent;color:#526b87;font-size:.8rem;font-weight:500;white-space:nowrap}.ap-tab.active,.ap-tab:hover{color:#146bd7;font-weight:600}.ap-tab.active:after{content:"";position:absolute;left:0;right:0;bottom:-1px;height:3px;border-radius:999px;background:#146bd7}.ap-tab em{font-style:normal;margin-left:.35rem;padding:.12rem .45rem;border-radius:999px;background:#edf5ff;color:#146bd7;font-size:.68rem}.ap-list{display:grid;gap:.75rem;padding:1rem;background:#f8fbff;max-width:100%;overflow:hidden}.ap-row{position:relative;display:grid;grid-template-columns:44px minmax(100px,.7fr) minmax(120px,.9fr) minmax(115px,.85fr) minmax(115px,.82fr) minmax(85px,.55fr) minmax(85px,.55fr) minmax(105px,.72fr) minmax(90px,.55fr) minmax(122px,.66fr);gap:.55rem;align-items:center;width:100%;min-width:0;padding:.9rem;border:1px solid #e5eef9;border-radius:18px;background:#fff;box-shadow:0 10px 26px rgba(17,39,68,.035);transition:.16s}.ap-row:hover{transform:translateY(-1px);border-color:#bfd7f3;box-shadow:0 16px 34px rgba(17,39,68,.07)}.ap-row[data-status="approved"]{background:linear-gradient(90deg,#fff,#f4fdf8)}.ap-row[data-status="rejected"]{background:linear-gradient(90deg,#fff,#fff7f7)}.ap-row-link{position:absolute;inset:0;z-index:1;border-radius:18px}.ap-row>div{position:relative;z-index:2;pointer-events:none}.ap-actions,.ap-actions *{pointer-events:auto}.ap-avatar{width:42px;height:42px;display:grid;place-items:center;border-radius:14px;background:#eaf3ff;color:#146bd7;font-weight:500}.ap-cell-label{display:block;margin-bottom:.2rem;color:#7a93b0;font-size:.64rem;font-weight:500}.ap-code{display:block;color:#0b1f3a;font-size:.78rem;font-weight:600}.ap-created{display:block;margin-top:.2rem;color:#7a93b0;font-size:.64rem}.ap-primary{display:block;color:#0b1f3a;font-size:.82rem;font-weight:600;line-height:1.25}.ap-secondary{display:block;margin-top:.18rem;color:#526b87;font-size:.7rem;line-height:1.25}.ap-purpose{display:block;color:#0b1f3a;font-size:.78rem;font-weight:500;line-height:1.25}.ap-status-cell .status-badge{font-weight:500}.ap-actions{display:flex;justify-content:flex-end;gap:.45rem;align-items:center;flex-wrap:wrap;min-width:0}.ap-actions form{display:inline-flex}.ap-btn{min-height:34px;display:inline-flex;align-items:center;justify-content:center;gap:.35rem;border-radius:11px;padding:.42rem .72rem;font-size:.72rem;font-weight:600;text-decoration:none;white-space:nowrap}.ap-btn-approve{border:1px solid #bbf7d0;background:#ecfdf5;color:#059669}.ap-btn-reject{border:1px solid #fecaca;background:#fff7f7;color:#dc2626}.ap-done{border:1px solid #d8e5f2;background:#f8fbff;color:#526b87}.ap-wait{display:inline-flex;align-items:center;gap:.25rem;width:max-content;margin-top:.35rem;padding:.18rem .5rem;border-radius:999px;background:#fff7ed;color:#d97706;font-size:.66rem;font-weight:500}.ap-empty{padding:3rem;text-align:center;color:#7a93b0}.ap-empty i{display:block;margin-bottom:.5rem;color:#bfd3eb;font-size:2.4rem}.ap-footer{display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:1rem;color:#526b87;font-size:.8rem;background:#fff}.ap-pages{display:flex;gap:.4rem}.ap-pages span{width:34px;height:34px;display:grid;place-items:center;border:1px solid #d8e5f2;border-radius:10px;color:#29435f;font-weight:500}.ap-pages .active{background:#146bd7;border-color:#146bd7;color:#fff}@media(max-width:1600px){.ap-stat-grid{grid-template-columns:repeat(4,minmax(0,1fr))}.ap-filter{grid-template-columns:minmax(220px,1fr) 150px 180px 180px auto}.ap-row{grid-template-columns:40px repeat(4,minmax(100px,1fr)) repeat(4,minmax(82px,.65fr)) minmax(115px,.7fr);gap:.45rem;padding:.8rem}.ap-btn{padding:.38rem .58rem;font-size:.7rem}}@media(max-width:1350px){.ap-stat-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.ap-filter{grid-template-columns:1fr 1fr}.ap-row{grid-template-columns:42px repeat(3,minmax(0,1fr));align-items:start}.ap-row>div:nth-child(n+6){grid-column:auto}.ap-actions{grid-column:2/-1;justify-content:flex-start}.ap-status-cell{grid-column:auto}}@media(max-width:900px){.ap-stat-grid,.ap-filter{grid-template-columns:1fr}.ap-list{padding:.75rem}.ap-row{grid-template-columns:1fr;gap:.75rem}.ap-avatar{display:none}.ap-actions{grid-column:auto;justify-content:flex-start}.ap-footer{flex-direction:column;align-items:flex-start}}

/* Compact approval table: clearer hierarchy without changing the page shell. */
.ap-table-scroll{overflow-x:auto;background:#fff}
.ap-row,.ap-btn,.ap-tab{transition:none!important}.ap-row:hover{transform:none!important;box-shadow:none!important}
.ap-main{border-right:0;border-left:0;border-radius:0;box-shadow:none}
.ap-table-inner{min-width:980px}
.ap-table-head,.ap-row{display:grid;grid-template-columns:145px minmax(160px,1.2fr) minmax(160px,1.1fr) minmax(125px,.8fr) 130px 110px 178px;column-gap:14px;align-items:center}
.ap-table-head{padding:10px 16px;border-bottom:1px solid #e3edf8;background:#f8fbff;color:#7a8ca3;font-size:11px;font-weight:600;text-transform:uppercase}
.ap-list{display:block;padding:0;background:#fff;overflow:visible}
.ap-row{min-height:56px;padding:8px 16px;border:0;border-bottom:1px solid #e8eef5;border-radius:0;box-shadow:none}
.ap-row:last-child{border-bottom:0}
.ap-row:hover{transform:none;border-color:#e8eef5;background:#f8fbff;box-shadow:none}
.ap-row[data-status="approved"],
.ap-row[data-status="approved"]:hover,
.ap-row[data-status="rejected"],
.ap-row[data-status="rejected"]:hover{background:#fff}
.ap-row-link{border-radius:0}
.ap-avatar,.ap-cell-label{display:none}
.ap-code,.ap-primary,.ap-purpose{font-size:13px;font-weight:500}
.ap-code{color:#146bd7}
.ap-created,.ap-secondary{font-size:11px;color:#7a8ca3}
.ap-creator{display:block;color:#46566c;font-size:12px;font-weight:500;line-height:1.25;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.ap-actions{justify-content:flex-end;flex-wrap:nowrap;gap:6px;white-space:nowrap}
.ap-actions form{flex:0 0 auto}
.ap-actions .ap-btn{min-height:32px;padding:5px 9px}
.ap-processed{width:30px;height:30px;display:inline-grid;place-items:center;border:1px solid #cfe8d8;border-radius:8px;background:#f0fdf4;color:#15803d;font-size:14px}
.ap-footer{border-top:1px solid #e8eef5}
.ap-pages{display:none}
@media(max-width:1100px){.ap-table-inner{min-width:1040px}.ap-row,.ap-table-head{grid-template-columns:135px minmax(145px,1.15fr) minmax(145px,1.05fr) 115px 120px 100px 174px;column-gap:10px}}
@media(max-width:900px){.ap-table-inner{min-width:900px}.ap-row{display:grid;align-items:center}.ap-actions{grid-column:auto}.ap-footer{flex-direction:row;align-items:center}}
.ap-filter .form-control:focus,.ap-filter .form-select:focus{border-color:#dce8f6!important;box-shadow:none!important;outline:0!important}.ap-filter .form-control,.ap-filter .form-select{transition:none!important}
</style>
@endpush

@section('content')
<section class="ap-main">
    <div class="ap-filter">
        <input id="approvalSearch" class="form-control" placeholder="Tìm theo mã lịch, tên khách, công ty, người tiếp...">
        <input id="approvalDateFilter" class="form-control" type="date" value="{{ now()->format('Y-m-d') }}">
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

    <div class="ap-table-scroll">
        <div class="ap-table-inner">
            <div class="ap-table-head" aria-hidden="true">
                <span>Mã lịch</span>
                <span>Khách</span>
                <span>Người tiếp</span>
                <span>Người tạo</span>
                <span>Thời gian</span>
                <span>Trạng thái</span>
                <span>Thao tác</span>
            </div>
            <div class="ap-list" id="approvalList">
            @forelse ($approvalVisits as $visit)
                <article class="ap-row" data-status="{{ $visit['status'] }}" data-search="{{ strtolower($visit['code'].' '.$visit['visitor'].' '.$visit['company'].' '.$visit['host'].' '.$visit['creator'].' '.$visit['department'].' '.$visit['purpose']) }}" data-department="{{ strtolower($visit['department']) }}" data-date="{{ $visit['date_iso'] ?? '' }}">
                <a class="ap-row-link" href="{{ route('admin.visits.show', $visit['id']) }}" aria-label="Xem chi tiết lịch {{ $visit['code'] }}"></a>

                <div>
                    <span class="ap-code">{{ $visit['code'] }}</span>
                </div>

                <div>
                    <span class="ap-cell-label">Khách</span>
                    <span class="ap-primary">{{ $visit['visitor'] }}</span>
                </div>

                <div>
                    <span class="ap-cell-label">Người tiếp</span>
                    <span class="ap-primary">{{ $visit['host'] }}</span>
                </div>

                <div>
                    <span class="ap-cell-label">Người tạo</span>
                    <span class="ap-creator" title="{{ $visit['creator'] }}">
                        {{ str_starts_with($visit['creator'], 'Kiosk') ? 'Kiosk' : $visit['creator'] }}
                    </span>
                </div>

                <div>
                    <span class="ap-cell-label">Giờ hẹn</span>
                    <span class="ap-primary"><i class="bi bi-clock"></i> {{ $visit['time'] }}</span>
                    <span class="ap-secondary">{{ $visit['date'] }}</span>
                </div>

                <div class="ap-status-cell">
                    <span class="ap-cell-label">Trạng thái</span>
                    <x-status-badge :status="$visit['status']" />
                    @if ($visit['status'] === 'pending' && $visit['waiting_minutes'] >= 15)
                        <span class="ap-wait"><i class="bi bi-exclamation-circle"></i> Chờ lâu</span>
                    @endif
                </div>

                <div class="ap-actions">
                    @if ($visit['status'] === 'pending')
                        <form action="{{ $kioskLobbyModeEnabled ? route('admin.approvals.approve-checkin', $visit['id']) : route('admin.approvals.approve', $visit['id']) }}" method="post" data-disable-on-submit>
                            @csrf
                            @if ($kioskLobbyModeEnabled)
                                <button class="ap-btn ap-btn-approve" type="submit" data-loading-text="Đang cho khách vào..."><i class="bi bi-door-open"></i> Duyệt & cho vào</button>
                            @else
                                <button class="ap-btn ap-btn-approve" type="submit" data-loading-text="Đang duyệt..."><i class="bi bi-check2"></i> Duyệt</button>
                            @endif
                        </form>
                        <form action="{{ route('admin.approvals.reject', $visit['id']) }}" method="post" data-disable-on-submit>
                            @csrf
                            <input type="hidden" name="reason" value="Yêu cầu tiếp khách không phù hợp.">
                            <button class="ap-btn ap-btn-reject" type="submit" data-loading-text="Đang từ chối..."><i class="bi bi-x"></i> Từ chối</button>
                        </form>
                    @else
                        <span class="ap-processed" title="Đã xử lý" aria-label="Đã xử lý"><i class="bi bi-check2"></i></span>
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
        </div>
    </div>

</section>
@endsection

@push('scripts')
<script>
(() => {
    const searchInput = document.getElementById('approvalSearch');
    const departmentFilter = document.getElementById('approvalDepartmentFilter');
    const statusFilter = document.getElementById('approvalStatusFilter');
    const dateFilter = document.getElementById('approvalDateFilter');
    const cards = Array.from(document.querySelectorAll('#approvalList .ap-row'));
    const tabs = Array.from(document.querySelectorAll('[data-approval-tab]'));
    let tabStatus = 'all';

    const applyFilters = () => {
        const keyword = (searchInput.value || '').trim().toLowerCase();
        const department = departmentFilter.value;
        const selectedStatus = statusFilter.value;
        const selectedDate = dateFilter.value;

        const counts = { all: 0, pending: 0, approved: 0, rejected: 0 };

        cards.forEach((card) => {
            const matchKeyword = keyword === '' || (card.dataset.search || '').includes(keyword);
            const matchDepartment = department === 'all' || card.dataset.department === department;
            const matchDate = selectedDate === '' || card.dataset.date === selectedDate;
            const matchBaseFilters = matchKeyword && matchDepartment && matchDate;
            const matchTab = tabStatus === 'all' || card.dataset.status === tabStatus;
            const matchSelect = selectedStatus === 'all' || card.dataset.status === selectedStatus;

            if (matchBaseFilters) {
                counts.all += 1;
                if (Object.hasOwn(counts, card.dataset.status)) {
                    counts[card.dataset.status] += 1;
                }
            }

            card.classList.toggle('d-none', !(matchBaseFilters && matchTab && matchSelect));
        });

        tabs.forEach((tab) => {
            const count = counts[tab.dataset.approvalTab] ?? 0;
            const badge = tab.querySelector('em');
            if (badge) {
                badge.textContent = count;
            }
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
    dateFilter.addEventListener('change', applyFilters);
    statusFilter.addEventListener('change', () => {
        tabStatus = statusFilter.value;
        tabs.forEach((item) => item.classList.toggle('active', item.dataset.approvalTab === tabStatus));
        if (tabStatus === 'all') {
            tabs.forEach((item) => item.classList.toggle('active', item.dataset.approvalTab === 'all'));
        }
        applyFilters();
    });

    applyFilters();
})();
(() => {
    const liveUrl = @json(route('admin.visits.live-state'));
    let currentVersion = @json($visitLiveState['version'] ?? '');
    let reloading = false;

    const shouldPauseLiveRefresh = () => {
        const active = document.activeElement;
        return document.hidden
            || document.querySelector('.modal.show')
            || (active && ['INPUT', 'TEXTAREA', 'SELECT'].includes(active.tagName))
            || document.querySelector('form[data-disable-on-submit] button[disabled]');
    };

    const checkLiveState = async () => {
        if (reloading || shouldPauseLiveRefresh()) {
            return;
        }

        try {
            const response = await fetch(liveUrl, {
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                cache: 'no-store',
            });

            if (! response.ok) {
                return;
            }

            const payload = await response.json();
            if (! currentVersion) {
                currentVersion = payload.version || '';
                return;
            }

            if (payload.version && payload.version !== currentVersion) {
                reloading = true;
                window.location.reload();
            }
        } catch (error) {
            // Mat ket noi tam thoi thi bo qua lan nay, lan sau tu kiem tra lai.
        }
    };

    window.setInterval(checkLiveState, 5000);
})();
</script>
@endpush
