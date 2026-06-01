@extends('layouts.admin')

@section('title', 'Sua permission | Visitor Management')
@section('page_title', 'Sua permission '.$permission->name)
@section('page_subtitle', 'Cap nhat ten permission va slug dieu khien quyen')

@section('content')
    <form class="row g-3" method="post" action="{{ route('admin.rbac.permissions.update', $permission) }}">
        @csrf
        @method('PUT')
        <div class="col-xl-8">
            <section class="panel-card">
                <div class="panel-header">
                    <div>
                        <h3>Thong tin permission</h3>
                        <p>Slug nen theo dang module.action, vi du reports.export.</p>
                    </div>
                    @if ($isProtectedPermission)
                        <span class="status-badge status-checked-in">System permission</span>
                    @else
                        <span class="status-badge status-approved">Custom permission</span>
                    @endif
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Ten permission</label>
                        <input class="form-control" name="name" value="{{ old('name', $permission->name) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Slug</label>
                        <input class="form-control" name="slug" value="{{ old('slug', $permission->slug) }}" @readonly($isProtectedPermission) required>
                        @if ($isProtectedPermission)
                            <small class="text-secondary">Permission he thong khong cho doi slug de tranh vo middleware.</small>
                        @endif
                    </div>
                </div>
            </section>

            <section class="panel-card mt-3">
                <div class="panel-header">
                    <div>
                        <h3>Roles dang su dung</h3>
                        <p>Muon gan/bo gan permission, hay sua role hoac dung matrix o trang RBAC.</p>
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    @forelse ($permission->roles as $role)
                        <a class="status-badge status-approved text-decoration-none" href="{{ route('admin.rbac.roles.show', $role) }}">
                            {{ $role->name }}
                        </a>
                    @empty
                        <span class="text-secondary">Chua co role nao dung permission nay.</span>
                    @endforelse
                </div>
            </section>
        </div>

        <div class="col-xl-4">
            <section class="panel-card sticky-xl-top top-space">
                <div class="d-grid gap-2">
                    <button class="btn btn-brand btn-lg" type="submit">Luu thay doi</button>
                    <a class="btn btn-light" href="{{ route('admin.rbac.permissions.show', $permission) }}">Quay lai chi tiet</a>
                    <a class="btn btn-outline-secondary" href="{{ route('admin.rbac.index') }}">Ve RBAC</a>
                </div>
            </section>
        </div>
    </form>
@endsection
