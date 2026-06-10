@extends('layouts.admin')

@section('title', 'Chi tiết vai trò | Quản lý khách')
@section('page_title', $role->name)
@section('page_subtitle', 'Thông tin vai trò, quyền hạn và tài khoản đang sử dụng')

@push('styles')
<style>
.role-detail{width:100%;color:#10233d}.role-card{width:100%;border:1px solid #dce8f5;border-radius:8px;background:#fff;box-shadow:0 14px 34px rgba(17,39,68,.05);overflow:hidden}.role-header{display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:24px 28px 18px;border-bottom:1px solid #edf3fb}.role-heading{display:flex;align-items:center;gap:.75rem;min-width:0}.role-heading h2{margin:0;color:#07182d;font-size:1.25rem;font-weight:650;letter-spacing:0}.role-type{display:inline-flex;align-items:center;gap:.35rem;padding:.28rem .62rem;border:1px solid #d9e7f5;border-radius:999px;background:#fff;color:#526b87;font-size:.74rem;font-weight:500;white-space:nowrap}.role-type.system{color:#146bd7;border-color:#cfe2fb;background:#f7fbff}.role-actions{display:flex;align-items:center;gap:.42rem;flex:0 0 auto}.role-icon-btn{width:38px;height:38px;display:inline-grid;place-items:center;border:1px solid #dbe7f4;border-radius:10px;background:#fff;color:#29435f;text-decoration:none;transition:background .15s ease,border-color .15s ease,color .15s ease}.role-icon-btn:hover{background:#f5f9fd;border-color:#b9d5f1;color:#146bd7}.role-icon-btn.danger{color:#dc2626}.role-icon-btn.danger:hover{background:#fff5f5;border-color:#fecaca;color:#dc2626}.role-icon-btn[disabled]{opacity:.42;cursor:not-allowed}.role-meta{display:flex;align-items:center;gap:1.45rem;flex-wrap:wrap;padding:18px 28px;border-bottom:1px solid #edf3fb;background:#fbfdff}.role-meta-item{display:inline-flex;align-items:baseline;gap:.35rem;color:#667f99;font-size:.84rem}.role-meta-item strong{color:#10233d;font-size:.9rem;font-weight:550}.role-section{display:grid;gap:.75rem;padding:22px 28px;border-bottom:1px solid #edf3fb}.role-section:last-child{border-bottom:0}.role-section-head{display:flex;align-items:baseline;gap:.55rem}.role-section-head h3{margin:0;color:#07182d;font-size:1rem;font-weight:600}.role-section-head span{color:#7187a3;font-size:.8rem}.role-chip-list{display:flex;align-items:center;flex-wrap:wrap;gap:.45rem}.role-chip{display:inline-flex;align-items:center;gap:.32rem;padding:.32rem .58rem;border:1px solid #dbe7f4;border-radius:999px;background:#fff;color:#29435f;font-size:.76rem;font-weight:500}.role-chip i{color:#146bd7;font-size:.78rem}.role-empty{padding:.85rem 0;color:#7187a3;font-size:.86rem}.role-table-wrap{width:100%;overflow:auto;border-top:1px solid #eef3f8}.role-table{width:100%;min-width:720px;border-collapse:separate;border-spacing:0}.role-table th{padding:.78rem .65rem;background:#fff;color:#7187a3;font-size:.72rem;font-weight:600;text-align:left;text-transform:uppercase;border-bottom:1px solid #eef3f8}.role-table td{padding:.86rem .65rem;border-bottom:1px solid #f0f4f8;color:#29435f;vertical-align:middle}.role-table tr:last-child td{border-bottom:0}.role-user{display:flex;align-items:center;gap:.65rem}.role-avatar{width:34px;height:34px;display:grid;place-items:center;border-radius:10px;background:#eef6ff;color:#146bd7;font-size:.82rem;font-weight:600}.role-user strong{display:block;color:#10233d;font-weight:550}.role-user span{display:block;color:#7187a3;font-size:.76rem}.role-status{display:inline-flex;align-items:center;gap:.32rem;padding:.25rem .58rem;border-radius:999px;background:#f2fbf6;color:#047857;font-size:.74rem;font-weight:500}.role-status.off{background:#f5f7fa;color:#64748b}.role-delete-form{margin:0}@media(max-width:720px){.role-header{align-items:flex-start;flex-direction:column;padding:20px}.role-actions{align-self:flex-end}.role-meta{gap:.75rem 1rem;padding:16px 20px}.role-meta-item{width:calc(50% - .5rem)}.role-section{padding:18px 20px}}
</style>
@endpush

@section('content')
@php
    $permissionLabels = [
        'visits.manage' => 'Quản lý lịch hẹn',
        'visits.approve' => 'Phê duyệt lịch hẹn',
        'visitors.manage' => 'Quản lý khách',
        'checkin.manage' => 'Check-in/Check-out',
        'employees.manage' => 'Quản lý nhân viên',
        'departments.manage' => 'Quản lý phòng ban',
        'reports.view' => 'Xem báo cáo',
        'watchlists.manage' => 'Quản lý cảnh báo',
        'system.manage' => 'Cài đặt hệ thống',
        'badges.manage' => 'Quản lý badge',
    ];
@endphp

<div class="role-detail">
    <div class="role-card">
        <div class="role-header">
            <div class="role-heading">
                <h2>{{ $role->name }}</h2>
                <span class="role-type {{ $isProtectedRole ? 'system' : '' }}">
                    <i class="bi {{ $isProtectedRole ? 'bi-lock' : 'bi-person-gear' }}"></i>
                    {{ $isProtectedRole ? 'Vai trò hệ thống' : 'Vai trò tùy chỉnh' }}
                </span>
            </div>

            <div class="role-actions" aria-label="Hành động vai trò">
                <a class="role-icon-btn" href="{{ route('admin.rbac.index') }}" title="Quay lại phân quyền" aria-label="Quay lại phân quyền">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <a class="role-icon-btn" href="{{ route('admin.rbac.roles.edit', $role) }}" title="Sửa vai trò" aria-label="Sửa vai trò">
                    <i class="bi bi-pencil-square"></i>
                </a>
                @if (! $isProtectedRole && $role->users->isEmpty())
                    <form class="role-delete-form" method="post" action="{{ route('admin.rbac.roles.destroy', $role) }}">
                        @csrf
                        @method('DELETE')
                        <button class="role-icon-btn danger" type="submit" title="Xóa vai trò" aria-label="Xóa vai trò">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </form>
                @else
                    <button class="role-icon-btn danger" type="button" disabled title="Không thể xóa vai trò hệ thống hoặc vai trò đang có tài khoản" aria-label="Không thể xóa vai trò">
                        <i class="bi bi-trash3"></i>
                    </button>
                @endif
            </div>
        </div>

        <div class="role-meta" aria-label="Thông tin vai trò">
            <span class="role-meta-item">Tên vai trò: <strong>{{ $role->name }}</strong></span>
            <span class="role-meta-item">Slug: <strong>{{ $role->slug }}</strong></span>
            <span class="role-meta-item">Số quyền: <strong>{{ $role->permissions->count() }}</strong></span>
            <span class="role-meta-item">Tài khoản: <strong>{{ $role->users->count() }}</strong></span>
        </div>

        <section class="role-section">
            <div class="role-section-head">
                <h3>Quyền được cấp</h3>
                <span>{{ $role->permissions->count() }} quyền</span>
            </div>
            <div class="role-chip-list">
                @forelse ($role->permissions as $permission)
                    <span class="role-chip">
                        <i class="bi bi-check2"></i>
                        {{ $permissionLabels[$permission->slug] ?? $permission->name }}
                    </span>
                @empty
                    <div class="role-empty">Vai trò này chưa được cấp quyền nào.</div>
                @endforelse
            </div>
        </section>

        <section class="role-section">
            <div class="role-section-head">
                <h3>Tài khoản đang dùng vai trò</h3>
                <span>{{ $role->users->count() }} tài khoản</span>
            </div>
            <div class="role-table-wrap">
                <table class="role-table">
                    <thead>
                        <tr>
                            <th>Tài khoản</th>
                            <th>Email</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($role->users as $user)
                            <tr>
                                <td>
                                    <div class="role-user">
                                        <span class="role-avatar">{{ mb_substr($user->name ?? $user->email, 0, 1) }}</span>
                                        <div>
                                            <strong>{{ $user->name }}</strong>
                                            <span>{{ $user->employeeProfile?->department?->name ?? 'Chưa gắn hồ sơ nhân viên' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <span class="role-status {{ $user->is_active ? '' : 'off' }}">
                                        <i class="bi {{ $user->is_active ? 'bi-check-circle' : 'bi-slash-circle' }}"></i>
                                        {{ $user->is_active ? 'Đang hoạt động' : 'Đã khóa' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-secondary">Chưa có tài khoản nào dùng vai trò này.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>
@endsection
