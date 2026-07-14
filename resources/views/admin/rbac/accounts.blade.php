@extends('layouts.admin')

@section('title', 'Tài khoản nhân viên | Visitor Management')
@section('page_title', '')
@section('page_subtitle', '')

@push('styles')
<style>
.acct-shell{display:grid;gap:1rem;color:#10233d}.acct-toolbar,.acct-card{border:1px solid #e1e9f3;border-radius:18px;background:#fff;box-shadow:0 10px 28px rgba(17,39,68,.045)}.acct-toolbar{display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:1rem}.acct-toolbar h2,.acct-card-head h3{margin:0;font-size:1rem;font-weight:600}.acct-toolbar p,.acct-card-head p{margin:.16rem 0 0;color:#7187a3;font-size:.78rem}.acct-actions{display:flex;gap:.55rem;flex-wrap:wrap}.acct-btn{min-height:38px;display:inline-flex;align-items:center;justify-content:center;gap:.4rem;padding:.5rem .78rem;border:1px solid #dbe6f3;border-radius:12px;background:#fff;color:#29435f;font-size:.8rem;font-weight:500;text-decoration:none}.acct-btn:hover{background:#eef7ff;color:#146bd7}.acct-btn.primary{border:0;color:#fff;background:#146bd7}.acct-btn.primary:hover{color:#fff;background:#0f63c8}.acct-card{overflow:hidden}.acct-card-head{display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:.9rem 1rem;border-bottom:1px solid #edf3fb;background:#fbfdff}.acct-form{display:grid;grid-template-columns:minmax(220px,1.1fr) minmax(220px,1fr) minmax(180px,.7fr) minmax(180px,.75fr) auto;gap:.75rem;align-items:end;padding:1rem}.acct-field{display:grid;gap:.35rem}.acct-field label{color:#526b87;font-size:.72rem;font-weight:500}.acct-control{min-height:40px;border:1px solid #dbe6f3!important;border-radius:12px!important;background:#fff!important;color:#10233d!important;font-size:.82rem!important;box-shadow:none!important}.acct-control:focus{border-color:#8cc6ff!important;box-shadow:0 0 0 .18rem rgba(20,107,215,.09)!important}.acct-hint{padding:0 1rem 1rem;color:#7187a3;font-size:.76rem}.acct-table{width:100%;border-collapse:separate;border-spacing:0}.acct-table th{padding:.7rem .85rem;background:#fbfdff;color:#7187a3;font-size:.68rem;font-weight:600;text-transform:uppercase;border-bottom:1px solid #edf3fb}.acct-table td{padding:.72rem .85rem;border-bottom:1px solid #edf3fb;color:#29435f;font-size:.8rem;vertical-align:middle}.acct-table tr:hover td{background:#f8fcff}.acct-user{display:flex;align-items:center;gap:.65rem}.acct-avatar{width:38px;height:38px;display:grid;place-items:center;border-radius:12px;background:#eaf3ff;color:#146bd7;font-weight:600}.acct-name{display:block;color:#10233d;font-weight:600}.acct-sub{display:block;margin-top:.1rem;color:#7187a3;font-size:.7rem}.acct-role{display:inline-flex;align-items:center;padding:.25rem .6rem;border-radius:999px;background:#eef7ff;color:#146bd7;font-size:.72rem;font-weight:500}.acct-status{display:inline-flex;align-items:center;gap:.28rem;padding:.24rem .56rem;border-radius:999px;font-size:.72rem;font-weight:500}.acct-status.on{background:#ecfdf5;color:#059669}.acct-status.off{background:#f1f5f9;color:#64748b}.acct-empty{padding:2rem;text-align:center;color:#7187a3}.acct-select-row{display:flex;gap:.45rem;align-items:center}.acct-select-row select{min-width:170px}@media(max-width:1180px){.acct-form{grid-template-columns:1fr 1fr}.acct-form .acct-btn{width:100%}}@media(max-width:720px){.acct-toolbar,.acct-card-head{align-items:flex-start;flex-direction:column}.acct-form{grid-template-columns:1fr}.acct-table{min-width:820px}}
</style>
@endpush

@section('content')
@php
    $roleLabels = [
        'admin' => 'Admin',
        'receptionist' => 'Lễ tân',
        'guard' => 'Bảo vệ',
        'employee' => 'Host',
        'department_manager' => 'QL phòng ban',
        'security_admin' => 'An ninh',
    ];
    $availableEmployees = $employees->filter(fn ($employee) => $employee->user_id === null)->values();
@endphp

<div class="acct-shell">
    <section class="acct-card">
        <div class="acct-card-head">
            <div>
                <h3>Tài khoản mới</h3>
                <p>Chọn nhân viên, email đăng nhập và vai trò vận hành.</p>
            </div>
            <div class="acct-actions">
                <a class="acct-btn" href="{{ route('admin.rbac.index') }}"><i class="bi bi-shield-lock"></i>Ma trận phân quyền</a>
            </div>
        </div>
        <form class="acct-form" method="post" action="{{ route('admin.rbac.users.store') }}">
            @csrf
            <div class="acct-field">
                <label>Nhân viên</label>
                <select id="accountEmployeeSelect" class="form-select acct-control" name="employee_id" required @disabled($availableEmployees->isEmpty())>
                    <option value="">Chọn nhân viên</option>
                    @foreach ($availableEmployees as $employee)
                        <option value="{{ $employee->id }}" data-email="{{ $employee->email }}" @selected((string) old('employee_id') === (string) $employee->id)>
                            {{ $employee->name }} - {{ $employee->department?->name ?? 'Chưa có phòng ban' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="acct-field">
                <label>Email đăng nhập</label>
                <input id="accountEmailInput" class="form-control acct-control" type="email" name="email" value="{{ old('email') }}" placeholder="email@congty.vn" required>
            </div>
            <div class="acct-field">
                <label>Vai trò</label>
                <select class="form-select acct-control" name="role_id" required>
                    <option value="">Chọn vai trò</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}" @selected((string) old('role_id') === (string) $role->id)>{{ $roleLabels[$role->slug] ?? $role->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="acct-field">
                <label>Mật khẩu tạm thời</label>
                <input class="form-control acct-control" type="password" name="password" placeholder="Tối thiểu 8 ký tự" required>
            </div>
            <button class="acct-btn primary" type="submit" @disabled($availableEmployees->isEmpty())><i class="bi bi-person-plus"></i>Tạo tài khoản</button>
            <input type="hidden" name="is_active" value="1">
        </form>
        <div class="acct-hint">
            @if ($availableEmployees->isEmpty())
                Tất cả nhân viên đang hoạt động đã có tài khoản đăng nhập.
            @else
                Với role Host, nhân viên chỉ thấy và duyệt các yêu cầu có host_employee_id của chính mình.
            @endif
        </div>
    </section>

    <section class="acct-card">
        <div class="acct-card-head">
            <div>
                <h3>Danh sách tài khoản</h3>
                <p>Quản lý email đăng nhập, vai trò và trạng thái tài khoản.</p>
            </div>
        </div>
        <div class="table-responsive">
            <table class="acct-table">
                <thead>
                <tr>
                    <th>Tài khoản</th>
                    <th>Nhân viên</th>
                    <th>Phòng ban</th>
                    <th>Vai trò</th>
                    <th>Trạng thái</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @forelse ($users as $user)
                    @php $role = $user->roles->first(); @endphp
                    <tr>
                        <td>
                            <div class="acct-user">
                                <span class="acct-avatar">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</span>
                                <div>
                                    <span class="acct-name">{{ $user->name }}</span>
                                    <span class="acct-sub">{{ $user->email }}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="acct-name">{{ $user->employeeProfile?->name ?? 'Chưa liên kết' }}</span>
                            <span class="acct-sub">{{ $user->employeeProfile?->job_title ?? '-' }}</span>
                        </td>
                        <td>{{ $user->employeeProfile?->department?->name ?? '-' }}</td>
                        <td>
                            @if ($role?->slug === 'super_admin')
                                <span class="acct-role">Super Admin</span>
                            @else
                                <form class="acct-select-row" method="post" action="{{ route('admin.rbac.user-role.update', $user->id) }}">
                                    @csrf
                                    <select class="form-select form-select-sm acct-control" name="role_id">
                                        @foreach ($roles as $item)
                                            <option value="{{ $item->id }}" @selected($role?->id === $item->id)>{{ $roleLabels[$item->slug] ?? $item->name }}</option>
                                        @endforeach
                                    </select>
                                    <button class="acct-btn" type="submit">Lưu</button>
                                </form>
                            @endif
                        </td>
                        <td>
                            <span class="acct-status {{ $user->is_active ? 'on' : 'off' }}">
                                <i class="bi {{ $user->is_active ? 'bi-check-circle' : 'bi-pause-circle' }}"></i>
                                {{ $user->is_active ? 'Đang hoạt động' : 'Tạm khóa' }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a class="acct-btn" href="{{ route('admin.rbac.users.edit', $user) }}"><i class="bi bi-pencil"></i>Sửa</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="acct-empty">Chưa có tài khoản nào.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
(() => {
    const employeeSelect = document.getElementById('accountEmployeeSelect');
    const emailInput = document.getElementById('accountEmailInput');
    if (!employeeSelect || !emailInput) return;

    const syncEmail = () => {
        const option = employeeSelect.options[employeeSelect.selectedIndex];
        const email = option?.dataset?.email || '';
        if (email && !emailInput.value) {
            emailInput.value = email;
        }
    };

    employeeSelect.addEventListener('change', () => {
        emailInput.value = '';
        syncEmail();
    });
    syncEmail();
})();
</script>
@endpush
