@extends('layouts.admin')

@section('title', 'Quản lý lịch hẹn | Quản lý khách')
@section('page_title', 'Quản lý lịch hẹn')
@section('page_subtitle', 'Tìm kiếm, lọc, theo dõi và quản lý lịch hẹn khách đến')

@push('styles')
<style>
.vs-stat-grid{display:grid;grid-template-columns:repeat(6,minmax(0,1fr));gap:.85rem;margin-bottom:1rem}.vs-stat{display:flex;align-items:center;justify-content:space-between;gap:.75rem;padding:1rem;border:1px solid #e4edf8;border-radius:18px;background:#fff;box-shadow:0 2px 14px rgba(17,39,68,.04)}.vs-stat span{display:block;color:#5a7a99;font-size:.72rem;font-weight:800}.vs-stat strong{display:block;margin:.25rem 0;color:#0b1f3a;font-size:1.75rem;font-weight:900}.vs-stat small{color:#7a93b0;font-size:.7rem}.vs-ico{width:48px;height:48px;display:grid;place-items:center;border-radius:18px;font-size:1.25rem}.vs-blue{background:#eff6ff;color:#146bd7}.vs-amber{background:#fff7ed;color:#d97706}.vs-green{background:#ecfdf5;color:#059669}.vs-cyan{background:#eefbff;color:#0891b2}.vs-slate{background:#f1f5f9;color:#475569}.vs-red{background:#fff1f2;color:#e11d48}
.vs-layout{display:grid;grid-template-columns:minmax(680px,1fr) 310px;gap:1rem;align-items:start}.vs-main,.vs-side-card{background:#fff;border:1px solid #e4edf8;border-radius:22px;box-shadow:0 2px 16px rgba(17,39,68,.05);overflow:hidden}.vs-tabs{display:flex;gap:1.2rem;padding:1rem 1.15rem 0;border-bottom:1px solid #edf3fb;overflow:auto}.vs-tab{position:relative;padding:0 0 .8rem;border:0;background:transparent;color:#526b87;font-size:.8rem;font-weight:800;white-space:nowrap}.vs-tab.active,.vs-tab:hover{color:#146bd7}.vs-tab.active:after{content:"";position:absolute;left:0;right:0;bottom:-1px;height:3px;border-radius:999px;background:#146bd7}.vs-tab em{font-style:normal;margin-left:.35rem;padding:.1rem .45rem;border-radius:999px;background:#edf5ff;color:#146bd7;font-size:.68rem}
.vs-filter{display:grid;grid-template-columns:minmax(300px,1fr) 150px 170px 190px auto;gap:.65rem;padding:1rem 1.15rem}.vs-filter .form-control,.vs-filter .form-select{min-height:42px;border-color:#dce8f6;border-radius:12px;font-size:.82rem}.vs-card-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:.85rem;padding:0 1.15rem 1rem}.vs-card{display:grid;gap:.7rem;padding:.85rem;border:1px solid #e5eef9;border-radius:16px;background:#fff;box-shadow:0 10px 24px rgba(17,39,68,.04);transition:.16s}.vs-card:hover{transform:translateY(-2px);box-shadow:0 16px 34px rgba(17,39,68,.08);border-color:#bfd7f3}.vs-card-top{display:flex;align-items:flex-start;justify-content:space-between;gap:.6rem}.vs-code{color:#0b1f3a;font-size:.78rem;font-weight:900}.vs-time{color:#0b1f3a;font-size:.76rem;font-weight:800}.vs-person{display:flex;gap:.65rem;align-items:center}.vs-avatar{width:42px;height:42px;display:grid;place-items:center;border-radius:50%;background:#f3e8ff;color:#7e22ce;font-weight:900}.vs-name{color:#0b1f3a;font-size:.9rem;font-weight:900}.vs-sub{color:#7a93b0;font-size:.72rem}.vs-meta{display:grid;gap:.25rem;color:#5a7a99;font-size:.7rem}.vs-meta b{color:#0b1f3a}.vs-actions{display:grid;grid-template-columns:1fr 1fr;gap:.55rem;margin-top:.15rem}.vs-actions form{margin:0}.vs-btn{display:flex;align-items:center;justify-content:center;min-height:36px;border-radius:10px;font-size:.76rem;font-weight:800;text-decoration:none}.vs-btn-light{border:1px solid #d8e5f2;color:#334b67;background:#fff}.vs-btn-blue{border:0;color:#fff;background:linear-gradient(135deg,#146bd7,#0cb4d8)}.vs-btn-red{border:0;color:#fff;background:linear-gradient(135deg,#dc2626,#ef4444)}.vs-btn-green{border:0;color:#fff;background:linear-gradient(135deg,#059669,#10b981)}
.vs-side{display:grid;gap:1rem;align-self:start;align-content:start}.vs-side-card{padding:1rem}.vs-side-title{display:flex;align-items:center;justify-content:space-between;margin-bottom:.85rem}.vs-side-title h3{margin:0;color:#0b1f3a;font-size:.9rem;font-weight:900}.vs-quick{display:grid;grid-template-columns:1fr 1fr;gap:.65rem}.vs-quick a{display:grid;place-items:center;gap:.35rem;min-height:72px;border:1px solid #dce8f6;border-radius:14px;color:#146bd7;background:#fbfdff;font-size:.75rem;font-weight:800;text-decoration:none;text-align:center}.vs-task{position:relative;display:flex;align-items:center;justify-content:space-between;gap:.75rem;padding:.78rem .72rem .78rem .85rem;border:1px solid #edf3fb;border-radius:16px;background:#fbfdff;overflow:hidden;transition:.16s}.vs-task:before{content:"";position:absolute;left:0;top:0;bottom:0;width:4px;background:#146bd7}.vs-task:hover{transform:translateY(-1px);box-shadow:0 10px 24px rgba(17,39,68,.08)}.vs-task strong{display:block;color:#0b1f3a;font-size:.78rem}.vs-task span{color:#5a6f8a;font-size:.7rem}.vs-task-icon{width:34px;height:34px;display:grid;place-items:center;flex:0 0 auto;border-radius:12px;font-size:1rem}.vs-task-content{display:flex;align-items:center;gap:.65rem;min-width:0}.vs-task-overstay{background:#fff7ed;border-color:#fed7aa}.vs-task-overstay:before{background:#f97316}.vs-task-overstay .vs-task-icon{background:#ffedd5;color:#ea580c}.vs-task-pending{background:#fffbeb;border-color:#fde68a}.vs-task-pending:before{background:#f59e0b}.vs-task-pending .vs-task-icon{background:#fef3c7;color:#d97706}.vs-task-approved{background:#eff6ff;border-color:#bfdbfe}.vs-task-approved:before{background:#2563eb}.vs-task-approved .vs-task-icon{background:#dbeafe;color:#2563eb}.vs-upcoming{display:grid;gap:.75rem}.vs-upcoming-row{display:grid;grid-template-columns:44px 1fr auto;gap:.65rem;align-items:center}.vs-upcoming-row time{color:#146bd7;font-size:.75rem;font-weight:900}.vs-empty{padding:2rem;text-align:center;color:#7a93b0}.vs-pager{display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:0 1.15rem 1rem;border-top:1px solid #edf3fb}.vs-pager-info{color:#5a7a99;font-size:.78rem;font-weight:700}.vs-pager-actions{display:flex;align-items:center;gap:.45rem}.vs-page-btn{min-width:36px;height:36px;border:1px solid #d8e5f2;border-radius:10px;background:#fff;color:#334b67;font-size:.78rem;font-weight:800}.vs-page-btn.active{border-color:#146bd7;background:#146bd7;color:#fff}.vs-page-btn:disabled{opacity:.45;cursor:not-allowed}.vs-page-size{width:auto;min-height:36px;border-color:#d8e5f2;border-radius:10px;color:#334b67;font-size:.78rem;font-weight:800}
@media(max-width:1400px){.vs-stat-grid{grid-template-columns:repeat(3,1fr)}.vs-layout{grid-template-columns:1fr}.vs-card-grid{grid-template-columns:repeat(2,1fr)}.vs-filter{grid-template-columns:minmax(280px,1fr) 150px 170px 180px}.vs-filter .btn{grid-column:4}}@media(max-width:768px){.vs-stat-grid,.vs-card-grid{grid-template-columns:1fr}.vs-filter{grid-template-columns:1fr}.vs-filter .btn{grid-column:auto}.vs-quick{grid-template-columns:1fr}.vs-pager{align-items:flex-start;flex-direction:column}.vs-pager-actions{flex-wrap:wrap}}
.vs-stat span,.vs-tab,.vs-btn,.vs-quick a,.vs-pager-info,.vs-page-btn,.vs-page-size{font-weight:500}.vs-stat strong{font-weight:650}.vs-code,.vs-time,.vs-name,.vs-avatar,.vs-side-title h3,.vs-task strong,.vs-upcoming-row time{font-weight:600}.vs-meta b{font-weight:600}.vs-btn-blue,.vs-btn-red,.vs-btn-green,.vs-page-btn.active{font-weight:600}.vs-card{box-shadow:0 8px 22px rgba(17,39,68,.035)}.vs-main,.vs-side-card{box-shadow:0 10px 30px rgba(17,39,68,.045)}
.vs-main{align-self:start}
.vs-filter{border-bottom:1px solid #edf3fb}
.vs-tabs{padding-top:.75rem;overflow-x:auto;overflow-y:hidden;scrollbar-width:none}
.vs-tabs::-webkit-scrollbar{display:none}
.vs-pager{min-height:52px;padding:.5rem 1.15rem;border-top:1px solid #edf3fb}
.vs-layout{display:block}
.vs-table-scroll{width:100%;overflow-x:auto}
.vs-table{width:100%;min-width:1240px;border-collapse:collapse;table-layout:fixed}
.vs-table th{height:38px;padding:8px 12px;border-top:1px solid #edf3fb;border-bottom:1px solid #dfe8f2;background:#f8fafc;color:#71839a;font-size:11px;font-weight:600;text-align:left;text-transform:uppercase}
.vs-table td{height:60px;padding:8px 12px;border-bottom:1px solid #e8eef5;background:#fff;color:#20364f;font-size:13px;vertical-align:middle}
.vs-table tbody tr:hover td{background:#f8fbff}.vs-table tbody tr[data-row-link]{cursor:pointer}
.vs-table th:nth-child(1){width:145px}.vs-table th:nth-child(2){width:16%}.vs-table th:nth-child(3){width:16%}.vs-table th:nth-child(4){width:13%}.vs-table th:nth-child(5){width:13%}.vs-table th:nth-child(6){width:12%}.vs-table th:nth-child(7){width:120px}.vs-table th:nth-child(8){width:120px}.vs-table th:nth-child(9){width:96px;text-align:right}
.vs-table td:last-child{text-align:right}
.vs-table-code{color:#146bd7;font-weight:600;text-decoration:none;white-space:nowrap}
.vs-table-primary{display:block;color:#10233d;font-weight:500;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.vs-table-secondary{display:block;margin-top:2px;color:#8192a8;font-size:11px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.vs-table-time{font-weight:500;white-space:nowrap}
.vs-row-actions{display:flex;align-items:center;justify-content:flex-end;gap:7px;white-space:nowrap}
.vs-row-actions form{display:inline-flex;margin:0}.vs-row-actions .vs-delete-action{margin-left:4px;padding-left:7px;border-left:1px solid #edf3fb}
.vs-icon-btn{width:34px;height:34px;display:inline-grid;place-items:center;border:1px solid #d8e5f2;border-radius:10px;background:#fff;color:#46627f;text-decoration:none;font-size:14px}
.vs-icon-btn:hover{border-color:#a9c9ea;background:#f4f9ff;color:#146bd7}
.vs-icon-btn.approve,.vs-icon-btn.checkin{border-color:#bce7ce;background:#f0fdf4;color:#15803d}
.vs-icon-btn.checkout{border-color:#fed7aa;background:#fff7ed;color:#c2410c}
.vs-icon-btn.danger{border-color:#fecaca;background:#fff;color:#dc2626}.vs-icon-btn.danger:hover{border-color:#fca5a5;background:#fff5f5;color:#b91c1c}
.vs-icon-btn.done{cursor:default;border-color:#e2e8f0;background:#f8fafc;color:#94a3b8}
.vs-filter-empty{padding:32px 16px;text-align:center;color:#8192a8;font-size:13px}
.vs-card,.vs-row,.vs-btn,.vs-tab{transition:none!important}.vs-card:hover{transform:none!important;box-shadow:none!important}
.vs-filter .form-control:focus,.vs-filter .form-select:focus{border-color:#dce8f6!important;box-shadow:none!important;outline:0!important}.vs-filter .form-control,.vs-filter .form-select{transition:none!important}
.vs-bulk-open{min-height:42px;border:1px solid #fecaca!important;border-radius:12px!important;background:#fff!important;color:#dc2626!important;font-size:.8rem!important;font-weight:600!important;white-space:nowrap}.vs-bulk-open:hover{background:#fff5f5!important}.vs-bulk-backdrop{position:fixed;inset:0;z-index:1080;display:none;align-items:center;justify-content:center;padding:1rem;background:rgba(15,32,55,.42)}.vs-bulk-backdrop.show{display:flex}.vs-bulk-dialog{width:min(620px,100%);border:1px solid #e5eaf1;border-radius:20px;background:#fff;box-shadow:0 28px 90px rgba(15,32,55,.28);overflow:hidden}.vs-bulk-head{display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;padding:1rem 1.1rem;border-bottom:1px solid #edf2f7}.vs-bulk-head h3{margin:0;color:#10233d;font-size:1rem}.vs-bulk-head p{margin:.2rem 0 0;color:#7187a3;font-size:.76rem}.vs-bulk-close{width:34px;height:34px;border:1px solid #dbe6f3;border-radius:10px;background:#fff;color:#64748b}.vs-bulk-body{display:grid;gap:1rem;padding:1.1rem}.vs-bulk-grid{display:grid;grid-template-columns:1fr 1fr;gap:.8rem}.vs-bulk-field label{display:block;margin-bottom:.35rem;color:#29435f;font-size:.75rem;font-weight:600}.vs-bulk-field .form-control,.vs-bulk-field .form-select{min-height:42px;border-color:#dce8f6;border-radius:11px;font-size:.8rem}.vs-bulk-field-wide{grid-column:1/-1}.vs-bulk-preview{display:flex;align-items:flex-start;gap:.7rem;padding:.8rem;border:1px solid #cfe2f7;border-radius:13px;background:#f3f8fe;color:#29435f;font-size:.78rem;line-height:1.45}.vs-bulk-preview.danger{border-color:#fecaca;background:#fff5f5;color:#b91c1c}.vs-bulk-confirm{display:flex;align-items:flex-start;gap:.55rem;color:#526b87;font-size:.76rem;line-height:1.4}.vs-bulk-confirm input{margin-top:.15rem}.vs-bulk-actions{display:flex;justify-content:flex-end;gap:.6rem;padding-top:.2rem}.vs-bulk-actions .btn{min-height:40px;border-radius:11px;font-size:.8rem;font-weight:600}@media(max-width:768px){.vs-bulk-grid{grid-template-columns:1fr}.vs-bulk-field-wide{grid-column:auto}}
</style>
@endpush

@section('content')
@php
    $creatorFilterOptions = collect($visits)
        ->pluck('creator')
        ->map(fn (string $creator): string => str_starts_with($creator, 'Kiosk') ? 'Kiosk / Khách tự đăng ký' : $creator)
        ->unique()
        ->sort()
        ->values();
@endphp
<div class="vs-layout">
    <section class="vs-main">
        <div class="vs-filter">
            <input id="visitSearch" class="form-control" placeholder="Tìm mã lịch, khách, người gặp, người tạo, mục đích...">
            <input id="visitDateFilter" class="form-control" type="date" value="" title="Để trống để xem tất cả ngày">
            <select id="departmentFilter" class="form-select">
                <option value="all">Tất cả phòng ban</option>
                @foreach (collect($visits)->pluck('department')->unique()->filter()->sort() as $department)
                    <option value="{{ strtolower($department) }}">{{ $department }}</option>
                @endforeach
            </select>
            <select id="creatorFilter" class="form-select">
                <option value="all">Tất cả người tạo</option>
                @foreach ($creatorFilterOptions as $creator)
                    <option value="{{ str_starts_with($creator, 'Kiosk') ? 'kiosk' : strtolower($creator) }}">
                        {{ $creator }}
                    </option>
                @endforeach
            </select>
            @if (auth()->user()?->hasPermission('visits.delete'))
                <button class="vs-bulk-open" id="openVisitBulkDelete" type="button"><i class="bi bi-trash3"></i> Xóa hàng loạt</button>
            @endif
        </div>

        @if (auth()->user()?->hasPermission('visits.delete'))
            <div class="vs-bulk-backdrop {{ $errors->hasAny(['from_date', 'to_date', 'status_scope', 'confirm_bulk_delete']) ? 'show' : '' }}" id="visitBulkDeleteModal" aria-hidden="{{ $errors->hasAny(['from_date', 'to_date', 'status_scope', 'confirm_bulk_delete']) ? 'false' : 'true' }}">
                <section class="vs-bulk-dialog" role="dialog" aria-modal="true" aria-labelledby="visitBulkDeleteTitle">
                    <div class="vs-bulk-head">
                        <div>
                            <h3 id="visitBulkDeleteTitle">Xóa lịch hẹn hàng loạt</h3>
                            <p>Chọn phạm vi theo ngày hẹn. Dữ liệu đã xóa không thể khôi phục.</p>
                        </div>
                        <button class="vs-bulk-close" id="closeVisitBulkDelete" type="button" aria-label="Đóng"><i class="bi bi-x-lg"></i></button>
                    </div>
                    <form id="visitBulkDeleteForm" action="{{ route('admin.visits.bulk-delete') }}" method="post" data-preview-url="{{ route('admin.visits.bulk-delete.preview') }}">
                        @csrf
                        @method('delete')
                        <div class="vs-bulk-body">
                            <div class="vs-bulk-grid">
                                <div class="vs-bulk-field">
                                    <label for="bulkVisitFromDate">Từ ngày</label>
                                    <input class="form-control" id="bulkVisitFromDate" name="from_date" type="date" value="{{ old('from_date', now()->startOfMonth()->toDateString()) }}" required>
                                    @error('from_date')<span class="text-danger small d-block mt-1">{{ $message }}</span>@enderror
                                </div>
                                <div class="vs-bulk-field">
                                    <label for="bulkVisitToDate">Đến ngày</label>
                                    <input class="form-control" id="bulkVisitToDate" name="to_date" type="date" value="{{ old('to_date', now()->toDateString()) }}" required>
                                    @error('to_date')<span class="text-danger small d-block mt-1">{{ $message }}</span>@enderror
                                </div>
                                <div class="vs-bulk-field vs-bulk-field-wide">
                                    <label for="bulkVisitStatusScope">Phạm vi trạng thái</label>
                                    <select class="form-select" id="bulkVisitStatusScope" name="status_scope">
                                        <option value="history" @selected(old('status_scope', 'history') === 'history')>Chỉ lịch sử đã kết thúc / từ chối / hủy (khuyến nghị)</option>
                                        <option value="all" @selected(old('status_scope') === 'all')>Tất cả trạng thái trong khoảng ngày</option>
                                        <option value="checked_out" @selected(old('status_scope') === 'checked_out')>Đã rời công ty</option>
                                        <option value="rejected" @selected(old('status_scope') === 'rejected')>Đã từ chối</option>
                                        <option value="cancelled" @selected(old('status_scope') === 'cancelled')>Đã hủy</option>
                                        <option value="pending" @selected(old('status_scope') === 'pending')>Chờ duyệt</option>
                                        <option value="approved" @selected(old('status_scope') === 'approved')>Đã duyệt, chưa check-in</option>
                                        <option value="checked_in" @selected(old('status_scope') === 'checked_in')>Đang trong công ty</option>
                                    </select>
                                    @error('status_scope')<span class="text-danger small d-block mt-1">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="vs-bulk-preview" id="visitBulkDeletePreview">
                                <i class="bi bi-info-circle"></i>
                                <span>Đang kiểm tra số lịch hẹn phù hợp...</span>
                            </div>
                            <label class="vs-bulk-confirm">
                                <input id="confirmVisitBulkDelete" name="confirm_bulk_delete" type="checkbox" value="1" required>
                                <span>Tôi hiểu dữ liệu lịch hẹn và thông tin vận hành liên quan trong phạm vi này sẽ bị xóa.</span>
                            </label>
                            @error('confirm_bulk_delete')<span class="text-danger small d-block mt-1">{{ $message }}</span>@enderror
                            <div class="vs-bulk-actions">
                                <button class="btn btn-light" id="cancelVisitBulkDelete" type="button">Hủy</button>
                                <button class="btn btn-danger" id="submitVisitBulkDelete" type="submit" disabled><i class="bi bi-trash3"></i> Xóa lịch đã chọn</button>
                            </div>
                        </div>
                    </form>
                </section>
            </div>
        @endif

        <div class="vs-tabs" id="visitTabs">
            @foreach ($statusFilters as $value => $label)
                <button class="vs-tab {{ $value === 'all' ? 'active' : '' }}" type="button" data-status-tab="{{ $value }}">
                    {{ $label }} <em>{{ $statusCounts[$value] ?? 0 }}</em>
                </button>
            @endforeach
        </div>

        <div class="vs-table-scroll">
            <table class="vs-table">
                <thead>
                    <tr>
                        <th>Mã lịch</th>
                        <th>Khách</th>
                        <th>Người gặp</th>
                        <th>Người tạo</th>
                        <th>Người duyệt</th>
                        <th>Phòng ban</th>
                        <th>Giờ hẹn</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody id="visitsGrid">
                @forelse ($visits as $visit)
                    <tr class="vs-row" data-row-link="{{ route('admin.visits.show', $visit['id']) }}" onclick="if (!event.target.closest('a,button,form,input,select')) window.location=this.dataset.rowLink" data-search="{{ strtolower($visit['code'].' '.$visit['visitor'].' '.$visit['host'].' '.$visit['creator'].' '.$visit['approver'].' '.$visit['department'].' '.$visit['purpose']) }}"
                        data-status="{{ $visit['status'] }}"
                        data-department="{{ strtolower($visit['department']) }}"
                        data-creator="{{ str_starts_with($visit['creator'], 'Kiosk') ? 'kiosk' : strtolower($visit['creator']) }}" data-date="{{ $visit['date_iso'] ?? '' }}">
                        <td><a class="vs-table-code" href="{{ route('admin.visits.show', $visit['id']) }}">{{ $visit['code'] }}</a></td>
                        <td>
                            <span class="vs-table-primary">{{ $visit['visitor'] }}</span>
                            <span class="vs-table-secondary">{{ $visit['purpose'] ?: 'Chưa có mục đích' }}</span>
                        </td>
                        <td><span class="vs-table-primary">{{ $visit['host'] }}</span></td>
                        <td>
                            <span class="vs-table-primary" title="{{ $visit['creator'] }}">
                                {{ str_starts_with($visit['creator'], 'Kiosk') ? 'Kiosk' : $visit['creator'] }}
                            </span>
                            <span class="vs-table-secondary">{{ str_starts_with($visit['creator'], 'Kiosk') ? 'Khách tự đăng ký' : 'Tài khoản hệ thống' }}</span>
                        </td>
                        <td>
                            <span class="vs-table-primary" title="{{ $visit['approver'] }}">{{ $visit['approver'] }}</span>
                        </td>
                        <td><span class="vs-table-primary">{{ $visit['department'] }}</span></td>
                        <td>
                            <span class="vs-table-time">{{ $visit['time'] }}</span>
                            <span class="vs-table-secondary">{{ $visit['date'] }}</span>
                        </td>
                        <td><x-status-badge :status="$visit['status']" /></td>
                        <td>
                            <div class="vs-row-actions">
                                @if ($visit['status'] === 'pending')
                                    <form action="{{ route('admin.approvals.approve', $visit['id']) }}" method="post" data-disable-on-submit>@csrf<button class="vs-icon-btn approve" type="submit" title="Duyet lich" aria-label="Duyet lich"><i class="bi bi-check2"></i></button></form>
                                @elseif ($visit['status'] === 'approved')
                                    <form action="{{ route('admin.checkin.confirm', $visit['id']) }}" method="post" data-disable-on-submit>@csrf<button class="vs-icon-btn checkin" type="submit" title="Cho khach vao" aria-label="Cho khach vao"><i class="bi bi-box-arrow-in-right"></i></button></form>
                                @elseif ($visit['status'] === 'checked_in')
                                    <form action="{{ route('admin.checkout.confirm', $visit['id']) }}" method="post" data-disable-on-submit>@csrf<button class="vs-icon-btn checkout" type="submit" title="Cho khach ra" aria-label="Cho khach ra"><i class="bi bi-box-arrow-right"></i></button></form>
                                @endif
                                @if (auth()->user()?->hasPermission('visits.delete'))
                                <form class="vs-delete-action" action="{{ route('admin.visits.destroy', $visit['id']) }}" method="post" onsubmit="return confirm('Xoa lich hen nay?')" data-disable-on-submit>
                                    @csrf
                                    @method('delete')
                                    <button class="vs-icon-btn danger" type="submit" title="Xoa lich hen" aria-label="Xoa lich hen"><i class="bi bi-trash"></i></button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td class="vs-empty" colspan="9">Chưa có lịch hẹn.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="vs-filter-empty d-none" id="visitFilterEmpty">Không có lịch hẹn phù hợp.</div>

        <div class="vs-pager" id="visitPager">
            <div class="vs-pager-info" id="visitPagerInfo">0 lịch</div>
            <div class="vs-pager-actions">
                <button class="vs-page-btn" type="button" id="visitPrevPage" aria-label="Trang trước"><i class="bi bi-chevron-left"></i></button>
                <div class="vs-pager-actions" id="visitPageNumbers"></div>
                <button class="vs-page-btn" type="button" id="visitNextPage" aria-label="Trang sau"><i class="bi bi-chevron-right"></i></button>
            </div>
        </div>
    </section>

</div>
@endsection

@push('scripts')
<script>
(() => {
    const searchInput = document.getElementById('visitSearch');
    const departmentFilter = document.getElementById('departmentFilter');
    const creatorFilter = document.getElementById('creatorFilter');
    const dateFilter = document.getElementById('visitDateFilter');
    const cards = Array.from(document.querySelectorAll('#visitsGrid .vs-row'));
    const tabs = Array.from(document.querySelectorAll('[data-status-tab]'));
    const pager = document.getElementById('visitPager');
    const pagerInfo = document.getElementById('visitPagerInfo');
    const pageNumbers = document.getElementById('visitPageNumbers');
    const prevPage = document.getElementById('visitPrevPage');
    const nextPage = document.getElementById('visitNextPage');
    const filterEmpty = document.getElementById('visitFilterEmpty');
    let status = 'all';
    let currentPage = 1;
    let filteredCards = cards;

    const applyFilters = () => {
        const keyword = (searchInput.value || '').trim().toLowerCase();
        const department = departmentFilter.value;
        const creator = creatorFilter.value;
        const selectedDate = dateFilter.value;

        filteredCards = cards.filter((card) => {
            const matchKeyword = keyword === '' || (card.dataset.search || '').includes(keyword);
            const matchStatus = status === 'all' || card.dataset.status === status;
            const matchDepartment = department === 'all' || card.dataset.department === department;
            const matchCreator = creator === 'all' || card.dataset.creator === creator;
            const matchDate = selectedDate === '' || card.dataset.date === selectedDate;
            return matchKeyword && matchStatus && matchDepartment && matchCreator && matchDate;
        });

        currentPage = 1;
        renderPage();
    };

    const renderPage = () => {
        const pageSize = 12;
        const total = filteredCards.length;
        const totalPages = Math.max(1, Math.ceil(total / pageSize));
        currentPage = Math.min(Math.max(currentPage, 1), totalPages);
        const start = (currentPage - 1) * pageSize;
        const end = start + pageSize;

        cards.forEach((card) => card.classList.add('d-none'));
        filteredCards.slice(start, end).forEach((card) => card.classList.remove('d-none'));
        filterEmpty.classList.toggle('d-none', total !== 0 || cards.length === 0);

        pager.classList.toggle('d-none', totalPages <= 1);
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
            if (status === 'all') {
                dateFilter.value = '';
            }
            applyFilters();
        });
    });

    searchInput.addEventListener('input', applyFilters);
    departmentFilter.addEventListener('change', applyFilters);
    creatorFilter.addEventListener('change', applyFilters);
    dateFilter.addEventListener('change', applyFilters);
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
(() => {
    const modal = document.getElementById('visitBulkDeleteModal');
    const openButton = document.getElementById('openVisitBulkDelete');
    const closeButton = document.getElementById('closeVisitBulkDelete');
    const cancelButton = document.getElementById('cancelVisitBulkDelete');
    const form = document.getElementById('visitBulkDeleteForm');

    if (!modal || !openButton || !form) {
        return;
    }

    const fromInput = document.getElementById('bulkVisitFromDate');
    const toInput = document.getElementById('bulkVisitToDate');
    const statusInput = document.getElementById('bulkVisitStatusScope');
    const confirmation = document.getElementById('confirmVisitBulkDelete');
    const submitButton = document.getElementById('submitVisitBulkDelete');
    const preview = document.getElementById('visitBulkDeletePreview');
    const previewText = preview.querySelector('span');
    let matchingCount = 0;
    let previewRequest = null;

    const syncSubmitButton = () => {
        submitButton.disabled = matchingCount < 1 || !confirmation.checked;
    };

    const setModalOpen = (open) => {
        modal.classList.toggle('show', open);
        modal.setAttribute('aria-hidden', open ? 'false' : 'true');
        if (open) {
            updatePreview();
        }
    };

    const updatePreview = async () => {
        matchingCount = 0;
        confirmation.checked = false;
        syncSubmitButton();
        preview.classList.remove('danger');

        if (!fromInput.value || !toInput.value) {
            previewText.textContent = 'Vui lòng chọn đầy đủ ngày bắt đầu và ngày kết thúc.';
            return;
        }

        if (toInput.value < fromInput.value) {
            preview.classList.add('danger');
            previewText.textContent = 'Ngày kết thúc phải bằng hoặc sau ngày bắt đầu.';
            return;
        }

        previewText.textContent = 'Đang kiểm tra số lịch hẹn phù hợp...';
        previewRequest?.abort();
        previewRequest = new AbortController();

        const params = new URLSearchParams({
            from_date: fromInput.value,
            to_date: toInput.value,
            status_scope: statusInput.value,
        });

        try {
            const response = await fetch(`${form.dataset.previewUrl}?${params.toString()}`, {
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                cache: 'no-store',
                signal: previewRequest.signal,
            });
            const payload = await response.json();

            if (!response.ok) {
                throw new Error(payload.message || 'Không kiểm tra được dữ liệu.');
            }

            matchingCount = Number(payload.count || 0);
            preview.classList.toggle('danger', matchingCount > 0);
            previewText.textContent = matchingCount > 0
                ? `Có ${matchingCount} lịch hẹn sẽ bị xóa cùng dữ liệu vận hành liên quan.`
                : 'Không có lịch hẹn phù hợp trong phạm vi đã chọn.';
            syncSubmitButton();
        } catch (error) {
            if (error.name === 'AbortError') return;
            preview.classList.add('danger');
            previewText.textContent = error.message || 'Không kiểm tra được dữ liệu. Vui lòng thử lại.';
        }
    };

    openButton.addEventListener('click', () => setModalOpen(true));
    closeButton?.addEventListener('click', () => setModalOpen(false));
    cancelButton?.addEventListener('click', () => setModalOpen(false));
    [fromInput, toInput, statusInput].forEach((input) => input.addEventListener('change', updatePreview));
    confirmation.addEventListener('change', syncSubmitButton);
    modal.addEventListener('click', (event) => {
        if (event.target === modal) setModalOpen(false);
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && modal.classList.contains('show')) setModalOpen(false);
    });
    form.addEventListener('submit', (event) => {
        const confirmed = matchingCount > 0
            && confirmation.checked
            && window.confirm(`Bạn chắc chắn muốn xóa ${matchingCount} lịch hẹn? Thao tác này không thể hoàn tác.`);
        if (!confirmed) event.preventDefault();
    });

    if (modal.classList.contains('show')) updatePreview();
})();
(() => {
    const liveUrl = @json(route('admin.visits.live-state'));
    let currentVersion = @json($visitLiveState['version'] ?? '');
    let reloading = false;

    const shouldPauseLiveRefresh = () => {
        const active = document.activeElement;
        return document.hidden
            || document.querySelector('.modal.show')
            || document.querySelector('.vs-bulk-backdrop.show')
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
