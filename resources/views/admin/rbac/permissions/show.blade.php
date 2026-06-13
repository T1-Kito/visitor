@extends('layouts.admin')

@section('title', 'Chi tiết quyền | Visitor Management')
@section('page_title', $permission->name)
@section('page_subtitle', 'Thông tin quyền và các vai trò đang được cấp')

@section('content')
    <section class="entity-detail">
        <header class="entity-detail-head">
            <div class="entity-detail-identity">
                <div class="entity-detail-avatar"><i class="bi bi-shield-check"></i></div>
                <div><h2 class="entity-detail-title">{{ $permission->name }}</h2><p class="entity-detail-subtitle">{{ $permission->slug }}</p></div>
            </div>
            <div class="entity-detail-actions">
                <span class="status-badge {{ $isProtectedPermission ? 'status-checked-in' : 'status-approved' }}">
                    {{ $isProtectedPermission ? 'Quyền hệ thống' : 'Quyền tùy chỉnh' }}
                </span>
                <a class="btn btn-light" href="{{ route('admin.rbac.index') }}"><i class="bi bi-arrow-left"></i>Quay lại</a>
                <a class="btn btn-brand" href="{{ route('admin.rbac.permissions.edit', $permission) }}"><i class="bi bi-pencil"></i>Sửa</a>
                @if (! $isProtectedPermission && $permission->roles->isEmpty())
                    <form method="post" action="{{ route('admin.rbac.permissions.destroy', $permission) }}" onsubmit="return confirm('Xóa quyền này?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-outline-danger" type="submit" title="Xóa quyền"><i class="bi bi-trash"></i></button>
                    </form>
                @endif
            </div>
        </header>

        <div class="entity-detail-fields">
            <div class="entity-detail-field"><span>Tên quyền</span><strong>{{ $permission->name }}</strong></div>
            <div class="entity-detail-field"><span>Slug</span><strong>{{ $permission->slug }}</strong></div>
            <div class="entity-detail-field"><span>Số vai trò sử dụng</span><strong>{{ $permission->roles->count() }}</strong></div>
            <div class="entity-detail-field"><span>Cập nhật</span><strong>{{ $permission->updated_at?->format('H:i d/m/Y') ?? '-' }}</strong></div>
        </div>

        <div class="entity-detail-section-head">
            <div><h3>Vai trò được cấp quyền</h3><p>Các vai trò đang sử dụng quyền này.</p></div>
            <span class="entity-detail-count">{{ $permission->roles->count() }} vai trò</span>
        </div>
        <div class="table-responsive">
            <table class="entity-detail-table">
                <thead><tr><th>Vai trò</th><th>Slug</th></tr></thead>
                <tbody>
                @forelse ($permission->roles as $role)
                    <tr>
                        <td><a class="entity-detail-link" href="{{ route('admin.rbac.roles.show', $role) }}">{{ $role->name }}</a></td>
                        <td><code>{{ $role->slug }}</code></td>
                    </tr>
                @empty
                    <tr><td colspan="2" class="text-center text-secondary py-4">Quyền này chưa được cấp cho vai trò nào.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
