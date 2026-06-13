@extends('layouts.admin')

@section('title', 'Chi tiết tài khoản | Visitor Management')
@section('page_title', $user->name)
@section('page_subtitle', 'Thông tin đăng nhập, vai trò và nhật ký gần đây')

@section('content')
    <section class="entity-detail">
        <header class="entity-detail-head">
            <div class="entity-detail-identity">
                <div class="entity-detail-avatar">{{ strtoupper(mb_substr($user->name, 0, 1)) }}</div>
                <div><h2 class="entity-detail-title">{{ $user->name }}</h2><p class="entity-detail-subtitle">{{ $user->email }}</p></div>
            </div>
            <div class="entity-detail-actions">
                <span class="status-badge {{ $user->is_active ? 'status-approved' : 'status-checked-out' }}">
                    {{ $user->is_active ? 'Đang hoạt động' : 'Đã khóa' }}
                </span>
                <a class="btn btn-light" href="{{ route('admin.rbac.index') }}"><i class="bi bi-arrow-left"></i>Quay lại</a>
                <a class="btn btn-brand" href="{{ route('admin.rbac.users.edit', $user) }}"><i class="bi bi-pencil"></i>Sửa</a>
                @if ((int) auth()->id() !== $user->id)
                    <form method="post" action="{{ route('admin.rbac.users.destroy', $user) }}" onsubmit="return confirm('Xóa tài khoản này?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-outline-danger" type="submit" title="Xóa tài khoản"><i class="bi bi-trash"></i></button>
                    </form>
                @endif
            </div>
        </header>

        <div class="entity-detail-fields">
            <div class="entity-detail-field"><span>Email</span><strong>{{ $user->email }}</strong></div>
            <div class="entity-detail-field"><span>Vai trò</span><strong>{{ $user->roles->first()?->name ?? 'Chưa phân quyền' }}</strong></div>
            <div class="entity-detail-field"><span>Hồ sơ nhân viên</span><strong>{{ $user->employeeProfile?->name ?? '-' }}</strong></div>
            <div class="entity-detail-field"><span>Phòng ban</span><strong>{{ $user->employeeProfile?->department?->name ?? '-' }}</strong></div>
        </div>

        <div class="entity-detail-section-head">
            <div><h3>Nhật ký gần đây</h3><p>Các thao tác mới nhất của tài khoản.</p></div>
            <span class="entity-detail-count">{{ $logs->count() }} sự kiện</span>
        </div>
        <div class="table-responsive">
            <table class="entity-detail-table">
                <thead><tr><th>Thời gian</th><th>Hành động</th><th>Đối tượng</th></tr></thead>
                <tbody>
                @forelse ($logs as $log)
                    <tr>
                        <td>{{ $log->created_at?->format('H:i d/m/Y') }}</td>
                        <td>{{ $log->action }}</td>
                        <td>{{ $log->entity_type }} #{{ $log->entity_id }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="text-center text-secondary py-4">Chưa có nhật ký hoạt động.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
