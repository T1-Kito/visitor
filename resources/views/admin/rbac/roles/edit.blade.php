@extends('layouts.admin')

@section('title', 'Sua role | Visitor Management')
@section('page_title', 'Sua role '.$role->name)
@section('page_subtitle', 'Cap nhat ten role, slug va permissions')

@section('content')
    <form class="row g-3" method="post" action="{{ route('admin.rbac.roles.update', $role) }}">
        @csrf
        @method('PUT')
        <div class="col-xl-8">
            <section class="panel-card">
                <div class="panel-header">
                    <div>
                        <h3>Thong tin role</h3>
                        <p>Slug dung trong middleware va dieu kien hien thi menu.</p>
                    </div>
                    @if ($isProtectedRole)
                        <span class="status-badge status-checked-in">System role</span>
                    @else
                        <span class="status-badge status-approved">Custom role</span>
                    @endif
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Ten role</label>
                        <input class="form-control" name="name" value="{{ old('name', $role->name) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Slug</label>
                        <input class="form-control" name="slug" value="{{ old('slug', $role->slug) }}" @readonly($isProtectedRole) required>
                        @if ($isProtectedRole)
                            <small class="text-secondary">Role he thong khong cho doi slug de tranh vo middleware.</small>
                        @endif
                    </div>
                </div>
            </section>

            <section class="panel-card mt-3">
                <div class="panel-header">
                    <div>
                        <h3>Permissions</h3>
                        <p>Tick cac quyen role nay duoc phep su dung.</p>
                    </div>
                </div>
                <div class="row g-2">
                    @foreach ($permissions as $permission)
                        <div class="col-md-6">
                            <label class="d-flex align-items-center gap-2">
                                <input
                                    type="checkbox"
                                    name="permission_ids[]"
                                    value="{{ $permission->id }}"
                                    @checked($role->permissions->contains('id', $permission->id))
                                >
                                <span>{{ $permission->name }} <small class="text-secondary">({{ $permission->slug }})</small></span>
                            </label>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>

        <div class="col-xl-4">
            <section class="panel-card sticky-xl-top top-space">
                <div class="d-grid gap-2">
                    <button class="btn btn-brand btn-lg" type="submit">Luu thay doi</button>
                    <a class="btn btn-light" href="{{ route('admin.rbac.roles.show', $role) }}">Quay lai chi tiet</a>
                    <a class="btn btn-outline-secondary" href="{{ route('admin.rbac.index') }}">Ve RBAC</a>
                </div>
            </section>
        </div>
    </form>
@endsection
