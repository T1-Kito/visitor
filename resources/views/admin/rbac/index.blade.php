@extends('layouts.admin')

@section('title', 'Phân quyền | Visitor Management')
@section('page_title', 'Phân quyền')
@section('page_subtitle', 'Chọn quyền theo từng vai trò và lưu thay đổi một lần')

@push('styles')
<style>
.pm-shell{display:grid;gap:1rem;color:#10233d}.pm-actions{display:flex;gap:.55rem;flex-wrap:wrap}.pm-btn{min-height:38px;display:inline-flex;align-items:center;justify-content:center;gap:.4rem;padding:.5rem .78rem;border:1px solid #dbe6f3;border-radius:12px;background:#fff;color:#29435f;font-size:.8rem;font-weight:500;text-decoration:none}.pm-btn:hover{background:#eef7ff;color:#146bd7}.pm-btn.primary{border:0;color:#fff;background:#146bd7}.pm-btn.primary:hover{color:#fff;background:#0f63c8}.pm-layout{display:grid;grid-template-columns:minmax(0,1fr);gap:1rem;align-items:start}.pm-card{border:1px solid #e1e9f3;border-radius:18px;background:#fff;box-shadow:0 10px 28px rgba(17,39,68,.045);overflow:hidden}.pm-card-head{display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:.85rem 1rem;border-bottom:1px solid #edf3fb;background:#fbfdff}.pm-card-head h3{margin:0;font-size:.9rem;font-weight:600}.pm-card-head p{margin:.12rem 0 0;color:#7187a3;font-size:.72rem}.pm-filter{display:flex;gap:.6rem;align-items:center;flex-wrap:wrap;padding:.75rem 1rem;border-bottom:1px solid #edf3fb;background:#fff}.pm-search{position:relative;flex:1;min-width:260px}.pm-search i{position:absolute;left:.8rem;top:50%;transform:translateY(-50%);color:#9aafca;font-size:.82rem}.pm-search input{width:100%;height:38px;padding-left:2.1rem;border:1px solid #dbe6f3;border-radius:12px;background:#fff;color:#10233d;font-size:.8rem;outline:0}.pm-search input:focus{border-color:#8cc6ff;box-shadow:0 0 0 .18rem rgba(20,107,215,.09)}.pm-module-tabs{display:flex;gap:.35rem;flex-wrap:wrap}.pm-module-tab{min-height:34px;padding:.35rem .62rem;border:1px solid #dbe6f3;border-radius:999px;background:#fff;color:#526b87;font-size:.72rem}.pm-module-tab:hover,.pm-module-tab.is-active{background:#eaf4ff;border-color:#b9dafb;color:#146bd7}.pm-table-wrap{max-height:calc(100vh - 300px);overflow:auto}.pm-table{width:100%;min-width:1080px;border-collapse:separate;border-spacing:0}.pm-table th{position:sticky;top:0;z-index:3;padding:.75rem .8rem;background:#f7fbff;color:#526b87;font-size:.72rem;font-weight:600;border-bottom:1px solid #e1e9f3;text-align:center}.pm-table th:first-child{left:0;z-index:4;text-align:left}.pm-table td{padding:.72rem .8rem;border-bottom:1px solid #edf3fb;color:#29435f;font-size:.8rem;text-align:center;background:#fff}.pm-table tbody tr:hover td{background:#f8fcff}.pm-perm-cell{position:sticky;left:0;z-index:2;text-align:left!important;background:#fff!important;min-width:280px}.pm-module-row td{position:sticky;left:0;z-index:2;padding:.7rem 1rem;background:#f1f7ff!important;color:#146bd7;font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.04em;border-bottom:1px solid #dbeafe}.pm-perm-name{display:block;color:#10233d;font-weight:600}.pm-perm-desc{display:block;margin-top:.1rem;color:#7f95ad;font-size:.69rem}.pm-check{width:18px;height:18px;accent-color:#146bd7}.pm-check[disabled]{opacity:.35}.pm-role-head{display:grid;gap:.1rem;justify-items:center}.pm-role-name{font-size:.78rem;color:#10233d;font-weight:600;white-space:nowrap}.pm-role-sub{font-size:.66rem;color:#8aa0ba;font-weight:400}.pm-save{position:sticky;bottom:0;display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:.85rem 1rem;border-top:1px solid #edf3fb;background:rgba(255,255,255,.96);backdrop-filter:blur(8px)}.pm-save span{color:#7187a3;font-size:.76rem}.pm-side{display:grid;gap:1rem}.pm-account-table{width:100%;border-collapse:separate;border-spacing:0}.pm-account-table th{padding:.65rem .75rem;background:#fbfdff;color:#7187a3;font-size:.66rem;font-weight:600;text-transform:uppercase;border-bottom:1px solid #edf3fb}.pm-account-table td{padding:.68rem .75rem;border-bottom:1px solid #edf3fb;color:#29435f;font-size:.76rem;vertical-align:middle}.pm-account-table tr:hover{background:#f8fcff}.pm-user-name{font-weight:600;color:#10233d}.pm-user-email{color:#7187a3;font-size:.68rem}.pm-status{display:inline-flex;align-items:center;gap:.28rem;padding:.22rem .5rem;border-radius:999px;font-size:.68rem;font-weight:500}.pm-status.on{background:#ecfdf5;color:#059669}.pm-status.off{background:#f1f5f9;color:#64748b}.pm-control{min-height:36px;border:1px solid #dbe6f3!important;border-radius:10px!important;background:#fff!important;color:#10233d!important;font-size:.74rem!important;box-shadow:none!important}.pm-modal-backdrop{position:fixed;inset:0;z-index:1060;display:none;align-items:center;justify-content:center;padding:1rem;background:rgba(15,32,55,.38)}.pm-modal-backdrop.is-open{display:flex}.pm-modal{width:min(560px,100%);max-height:min(760px,calc(100vh - 2rem));display:grid;grid-template-rows:auto minmax(0,1fr);border-radius:20px;background:#fff;box-shadow:0 24px 80px rgba(15,32,55,.24);overflow:hidden}.pm-modal.wide{width:min(760px,100%)}.pm-modal-head{display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:1rem;border-bottom:1px solid #edf3fb}.pm-modal-head h3{margin:0;font-size:.95rem;font-weight:600}.pm-modal-head p{margin:.15rem 0 0;color:#7187a3;font-size:.74rem}.pm-modal-close{width:34px;height:34px;border:1px solid #dbe6f3;border-radius:11px;background:#fff;color:#64748b}.pm-modal-body{padding:1rem;overflow:auto}.pm-form{display:grid;gap:.75rem}.pm-grid-2{display:grid;grid-template-columns:1fr 1fr;gap:.75rem}.pm-chip-list{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:.45rem;max-height:260px;overflow:auto;padding:.65rem;border:1px solid #e1e9f3;border-radius:14px;background:#fbfdff}.pm-chip{display:flex;align-items:flex-start;gap:.45rem;padding:.5rem;border:1px solid #edf3fb;border-radius:12px;background:#fff;color:#29435f;font-size:.75rem}.pm-chip small{display:block;color:#8aa0ba;font-size:.68rem}.pm-hidden{display:none!important}@media(max-width:1300px){.pm-table-wrap{max-height:none}}@media(max-width:768px){.pm-actions,.pm-save,.pm-card-head{align-items:flex-start;flex-direction:column}.pm-grid-2,.pm-chip-list{grid-template-columns:1fr}.pm-account-table{min-width:680px}.pm-btn.primary{width:100%}}
</style>
@endpush

@section('content')
@php
    $protectedPermissionSlugs = ['dashboard.view', 'visits.manage', 'approvals.manage', 'checkin.manage', 'reports.export', 'system.manage', 'departments.manage', 'employees.manage', 'visitors.manage', 'badges.manage', 'alerts.view'];
    $roleLabels = [
        'super_admin' => 'Super Admin',
        'admin' => 'Admin',
        'receptionist' => 'Lễ tân',
        'guard' => 'Bảo vệ',
        'employee' => 'Host',
        'department_manager' => 'QL phòng ban',
        'security_admin' => 'An ninh',
    ];
    $roleOrder = ['super_admin', 'admin', 'receptionist', 'guard', 'employee', 'department_manager', 'security_admin'];
    $matrixRoles = $roles->sortBy(function ($role) use ($roleOrder) {
        $index = array_search($role->slug, $roleOrder, true);
        return $index === false ? 100 + $role->id : $index;
    })->values();
    $permissionBySlug = $permissions->keyBy('slug');
    $permissionGroups = $permissions->groupBy(fn ($permission) => \Illuminate\Support\Str::headline(str($permission->slug)->before('.')->toString()));
    $rows = [
        'dashboard' => [
            'label' => 'Dashboard',
            'items' => [
                ['label' => 'Xem dashboard', 'desc' => 'Xem tổng quan vận hành', 'slug' => 'dashboard.view'],
            ],
        ],
        'visits' => [
            'label' => 'Lịch hẹn',
            'items' => [
                ['label' => 'Xem lịch hẹn', 'desc' => 'Danh sách và chi tiết lịch', 'slug' => 'visits.manage'],
                ['label' => 'Tạo lịch hẹn', 'desc' => 'Đăng ký khách mới', 'slug' => 'visits.manage'],
                ['label' => 'Sửa lịch hẹn', 'desc' => 'Cập nhật thông tin lịch', 'slug' => 'visits.manage'],
                ['label' => 'Hủy lịch hẹn', 'desc' => 'Hủy lịch khi cần', 'slug' => 'visits.manage'],
                ['label' => 'Gửi QR qua email', 'desc' => 'Gửi mã QR cho khách', 'slug' => 'visits.manage'],
            ],
        ],
        'approvals' => [
            'label' => 'Phê duyệt',
            'items' => [
                ['label' => 'Xem yêu cầu duyệt', 'desc' => 'Danh sách lịch chờ duyệt', 'slug' => 'approvals.manage'],
                ['label' => 'Duyệt lịch hẹn', 'desc' => 'Chấp thuận hoặc từ chối', 'slug' => 'approvals.manage'],
            ],
        ],
        'access' => [
            'label' => 'Check-in/Check-out',
            'items' => [
                ['label' => 'Xem màn hình ra/vào', 'desc' => 'Truy cập quầy xử lý', 'slug' => 'checkin.manage'],
                ['label' => 'Check-in khách', 'desc' => 'Xác nhận khách vào', 'slug' => 'checkin.manage'],
                ['label' => 'Check-out khách', 'desc' => 'Xác nhận khách ra', 'slug' => 'checkin.manage'],
                ['label' => 'Xem danh sách ra/vào', 'desc' => 'Tra cứu lịch sử ra vào', 'slug' => 'checkin.manage'],
            ],
        ],
        'visitors' => [
            'label' => 'Khách',
            'items' => [
                ['label' => 'Xem khách', 'desc' => 'Danh bạ khách', 'slug' => 'visitors.manage'],
                ['label' => 'Thêm khách', 'desc' => 'Tạo hồ sơ khách', 'slug' => 'visitors.manage'],
                ['label' => 'Sửa khách', 'desc' => 'Cập nhật hồ sơ khách', 'slug' => 'visitors.manage'],
                ['label' => 'Xóa khách', 'desc' => 'Xóa hồ sơ khách', 'slug' => 'visitors.manage'],
            ],
        ],
        'employees' => [
            'label' => 'Nhân viên',
            'items' => [
                ['label' => 'Xem nhân viên', 'desc' => 'Danh sách người cần gặp', 'slug' => 'employees.manage'],
                ['label' => 'Thêm nhân viên', 'desc' => 'Tạo nhân viên/host', 'slug' => 'employees.manage'],
                ['label' => 'Sửa nhân viên', 'desc' => 'Cập nhật thông tin host', 'slug' => 'employees.manage'],
                ['label' => 'Xóa nhân viên', 'desc' => 'Xóa nhân viên', 'slug' => 'employees.manage'],
            ],
        ],
        'departments' => [
            'label' => 'Phòng ban',
            'items' => [
                ['label' => 'Xem phòng ban', 'desc' => 'Danh sách phòng ban', 'slug' => 'departments.manage'],
                ['label' => 'Thêm phòng ban', 'desc' => 'Tạo phòng ban', 'slug' => 'departments.manage'],
                ['label' => 'Sửa phòng ban', 'desc' => 'Cập nhật phòng ban', 'slug' => 'departments.manage'],
                ['label' => 'Xóa phòng ban', 'desc' => 'Xóa phòng ban', 'slug' => 'departments.manage'],
            ],
        ],
        'badges' => [
            'label' => 'Thẻ/Badge',
            'items' => [
                ['label' => 'Xem thẻ', 'desc' => 'Danh sách thẻ ra vào', 'slug' => 'badges.manage'],
                ['label' => 'Cấp thẻ', 'desc' => 'Cấp badge cho khách', 'slug' => 'badges.manage'],
                ['label' => 'Thu hồi thẻ', 'desc' => 'Khóa/thu hồi badge', 'slug' => 'badges.manage'],
            ],
        ],
        'reports' => [
            'label' => 'Báo cáo',
            'items' => [
                ['label' => 'Xem báo cáo', 'desc' => 'Xem số liệu vận hành', 'slug' => 'reports.export'],
                ['label' => 'Xuất file', 'desc' => 'Xuất CSV/Excel/PDF', 'slug' => 'reports.export'],
            ],
        ],
        'system' => [
            'label' => 'Hệ thống',
            'items' => [
                ['label' => 'Cài đặt kiosk', 'desc' => 'Logo, nội dung, màu sắc', 'slug' => 'system.manage'],
                ['label' => 'Cài đặt máy in', 'desc' => 'Cấu hình in phiếu', 'slug' => 'system.manage'],
                ['label' => 'Phân quyền', 'desc' => 'Quản lý vai trò và quyền', 'slug' => 'system.manage'],
                ['label' => 'Cảnh báo an ninh', 'desc' => 'Danh sách cảnh báo', 'slug' => 'alerts.view'],
            ],
        ],
    ];
@endphp

<div class="pm-shell">
    <div class="pm-layout">
        <section class="pm-card">
            <div class="pm-card-head">
                <div>
                    <h3>Phân quyền</h3>
                    <p>{{ $matrixRoles->count() }} vai trò · {{ $permissions->count() }} quyền hệ thống</p>
                </div>
                <div class="pm-actions">
                    <button class="pm-btn" type="button" data-pm-modal-open="createRoleModal"><i class="bi bi-diagram-3"></i>Tạo vai trò</button>
                    <a class="pm-btn primary" href="{{ route('admin.rbac.accounts.index') }}"><i class="bi bi-person-gear"></i>Tài khoản nhân viên</a>
                </div>
            </div>

            <div class="pm-filter">
                <div class="pm-search">
                    <i class="bi bi-search"></i>
                    <input id="pmSearch" placeholder="Tìm quyền hoặc module">
                </div>
                <div class="pm-module-tabs">
                    <button class="pm-module-tab is-active" type="button" data-module-filter="all">Tất cả</button>
                    @foreach ($rows as $moduleKey => $module)
                        <button class="pm-module-tab" type="button" data-module-filter="{{ $moduleKey }}">{{ $module['label'] }}</button>
                    @endforeach
                </div>
            </div>

            <form method="post" action="{{ route('admin.rbac.permission-matrix.update') }}">
                @csrf
                @foreach ($matrixRoles as $role)
                    <input type="hidden" name="role_ids[]" value="{{ $role->id }}">
                @endforeach

                <div class="pm-table-wrap">
                    <table class="pm-table">
                        <thead>
                        <tr>
                            <th>Quyền</th>
                            @foreach ($matrixRoles as $role)
                                <th>
                                    <div class="pm-role-head">
                                        <span class="pm-role-name">{{ $roleLabels[$role->slug] ?? $role->name }}</span>
                                        <span class="pm-role-sub">{{ $role->users_count ?? 0 }} tài khoản</span>
                                    </div>
                                </th>
                            @endforeach
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($rows as $moduleKey => $module)
                            <tr class="pm-module-row" data-module="{{ $moduleKey }}" data-search-text="{{ \Illuminate\Support\Str::lower($module['label']) }}">
                                <td colspan="{{ $matrixRoles->count() + 1 }}">{{ $module['label'] }}</td>
                            </tr>
                            @foreach ($module['items'] as $item)
                                @php $permission = $permissionBySlug->get($item['slug']); @endphp
                                <tr data-module="{{ $moduleKey }}" data-search-text="{{ \Illuminate\Support\Str::lower($module['label'].' '.$item['label'].' '.$item['desc']) }}">
                                    <td class="pm-perm-cell">
                                        <span class="pm-perm-name">{{ $item['label'] }}</span>
                                        <span class="pm-perm-desc">{{ $item['desc'] }}</span>
                                    </td>
                                    @foreach ($matrixRoles as $role)
                                        <td>
                                            @if ($permission)
                                                <input
                                                    class="pm-check"
                                                    type="checkbox"
                                                    name="matrix[{{ $role->id }}][]"
                                                    value="{{ $permission->id }}"
                                                    data-role-id="{{ $role->id }}"
                                                    data-permission-id="{{ $permission->id }}"
                                                    @checked($role->permissions->contains('id', $permission->id))
                                                >
                                            @else
                                                <span class="text-secondary">—</span>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="pm-save">
                    <span>Những quyền trùng permission hệ thống sẽ được đồng bộ tự động khi tick.</span>
                    <button class="pm-btn primary" type="submit"><i class="bi bi-check2"></i>Lưu thay đổi</button>
                </div>
            </form>
        </section>

    </div>
</div>

<div class="pm-modal-backdrop" data-pm-modal="createRoleModal" aria-hidden="true">
    <section class="pm-modal wide">
        <div class="pm-modal-head">
            <div>
                <h3>Tạo vai trò</h3>
                <p>Vai trò mới có thể chọn nhanh các quyền ban đầu.</p>
            </div>
            <button class="pm-modal-close" type="button" data-pm-modal-close><i class="bi bi-x-lg"></i></button>
        </div>
        <div class="pm-modal-body">
            <form class="pm-form" method="post" action="{{ route('admin.rbac.roles.store') }}">
                @csrf
                <div>
                    <input class="form-control pm-control" name="role_name" value="{{ old('role_name') }}" placeholder="Tên vai trò" required>
                    <div class="form-text">Mã vai trò sẽ được hệ thống tự sinh, không cần nhập thủ công.</div>
                </div>
                <div class="pm-chip-list">
                    @foreach ($permissionGroups as $groupPermissions)
                        @foreach ($groupPermissions as $permission)
                            <label class="pm-chip">
                                <input type="checkbox" name="permission_ids[]" value="{{ $permission->id }}" @checked(collect(old('permission_ids', []))->contains((string) $permission->id) || collect(old('permission_ids', []))->contains($permission->id))>
                                <span>{{ $permission->name }}<small>{{ $permission->slug }}</small></span>
                            </label>
                        @endforeach
                    @endforeach
                </div>
                <button class="pm-btn primary" type="submit"><i class="bi bi-diagram-3"></i>Tạo vai trò</button>
            </form>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
(() => {
    const search = document.getElementById('pmSearch');
    const tabs = Array.from(document.querySelectorAll('[data-module-filter]'));
    const rows = Array.from(document.querySelectorAll('[data-module]'));
    let activeModule = 'all';

    function filterRows() {
        const query = (search?.value || '').trim().toLowerCase();
        rows.forEach((row) => {
            const matchesModule = activeModule === 'all' || row.dataset.module === activeModule;
            const matchesSearch = query === '' || row.dataset.searchText.includes(query);
            row.classList.toggle('pm-hidden', !(matchesModule && matchesSearch));
        });
    }

    tabs.forEach((tab) => {
        tab.addEventListener('click', () => {
            activeModule = tab.dataset.moduleFilter;
            tabs.forEach((item) => item.classList.toggle('is-active', item === tab));
            filterRows();
        });
    });

    search?.addEventListener('input', filterRows);

    document.querySelectorAll('.pm-check').forEach((checkbox) => {
        checkbox.addEventListener('change', () => {
            document.querySelectorAll(`.pm-check[data-role-id="${checkbox.dataset.roleId}"][data-permission-id="${checkbox.dataset.permissionId}"]`).forEach((item) => {
                item.checked = checkbox.checked;
            });
        });
    });

    document.querySelectorAll('[data-pm-modal-open]').forEach((button) => {
        button.addEventListener('click', () => {
            const modal = document.querySelector(`[data-pm-modal="${button.dataset.pmModalOpen}"]`);
            modal?.classList.add('is-open');
            modal?.setAttribute('aria-hidden', 'false');
        });
    });

    document.querySelectorAll('[data-pm-modal]').forEach((modal) => {
        modal.addEventListener('click', (event) => {
            if (event.target === modal || event.target.closest('[data-pm-modal-close]')) {
                modal.classList.remove('is-open');
                modal.setAttribute('aria-hidden', 'true');
            }
        });
    });

    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') return;
        document.querySelectorAll('[data-pm-modal].is-open').forEach((modal) => {
            modal.classList.remove('is-open');
            modal.setAttribute('aria-hidden', 'true');
        });
    });
})();
</script>
@endpush
