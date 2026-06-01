@extends('layouts.admin')

@section('title', 'RBAC | Visitor Management')
@section('page_title', 'Quan tri phan quyen')
@section('page_subtitle', 'Cap role cho user va quan ly permissions theo role')

@section('content')
    @php
        $protectedRoleSlugs = ['super_admin', 'admin', 'receptionist', 'guard', 'employee', 'department_manager', 'security_admin'];
        $protectedPermissionSlugs = ['dashboard.view', 'visits.manage', 'approvals.manage', 'checkin.manage', 'reports.export', 'system.manage', 'departments.manage', 'employees.manage', 'visitors.manage', 'badges.manage', 'alerts.view'];
    @endphp

    <div class="row g-3">
        <div class="col-xl-4">
            <section class="panel-card h-100">
                <div class="panel-header">
                    <div>
                        <h3>Tao user moi</h3>
                        <p>User moi can co role de vao dung workspace.</p>
                    </div>
                </div>
                <form class="d-grid gap-2" method="post" action="{{ route('admin.rbac.users.store') }}">
                    @csrf
                    <input class="form-control" name="name" value="{{ old('name') }}" placeholder="Ho ten" required>
                    <input class="form-control" type="email" name="email" value="{{ old('email') }}" placeholder="Email dang nhap" required>
                    <input class="form-control" type="password" name="password" placeholder="Mat khau tam thoi" required>
                    <select class="form-select" name="role_id" required>
                        <option value="">Chon role</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}" @selected((string) old('role_id') === (string) $role->id)>{{ $role->name }}</option>
                        @endforeach
                    </select>
                    <input type="hidden" name="is_active" value="0">
                    <label class="form-check d-flex gap-2 align-items-center">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
                        <span class="form-check-label">Cho phep dang nhap</span>
                    </label>
                    <button class="btn btn-brand" type="submit">Tao user</button>
                </form>
            </section>
        </div>

        <div class="col-xl-8">
            <section class="panel-card h-100">
                <div class="panel-header">
                    <div>
                        <h3>Gan role cho user</h3>
                        <p>Moi user trong MVP dung 1 role chinh.</p>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table modern-table align-middle mb-0">
                        <thead>
                        <tr>
                            <th>User</th>
                            <th>Email</th>
                            <th>Role hien tai</th>
                            <th>Trang thai</th>
                            <th class="text-end">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>
                                    <a class="fw-bold text-decoration-none" href="{{ route('admin.rbac.users.show', $user) }}">
                                        {{ $user->name }}
                                    </a>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->roles->first()?->name ?? 'No role' }}</td>
                                <td>
                                    <span class="status-badge {{ $user->is_active ? 'status-approved' : 'status-checked-out' }}">
                                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex flex-wrap gap-2 justify-content-end">
                                        <form class="d-inline-flex gap-2 align-items-center" method="post" action="{{ route('admin.rbac.user-role.update', $user->id) }}">
                                            @csrf
                                            <select name="role_id" class="form-select form-select-sm">
                                                @foreach ($roles as $role)
                                                    <option value="{{ $role->id }}" @selected($user->roles->first()?->id === $role->id)>
                                                        {{ $role->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <button class="btn btn-sm btn-brand" type="submit">Luu</button>
                                        </form>
                                        <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.rbac.users.show', $user) }}">Xem</a>
                                        <a class="btn btn-sm btn-light" href="{{ route('admin.rbac.users.edit', $user) }}">Sua</a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <div class="col-xl-5">
            <section class="panel-card h-100">
                <div class="panel-header">
                    <div>
                        <h3>Tao role moi</h3>
                        <p>Dung cho nhom quyen rieng nhu nha thau, quan ly tang, le tan ca dem.</p>
                    </div>
                </div>
                <form class="d-grid gap-3" method="post" action="{{ route('admin.rbac.roles.store') }}">
                    @csrf
                    <div class="row g-2">
                        <div class="col-md-6">
                            <input class="form-control" name="role_name" value="{{ old('role_name') }}" placeholder="Ten role" required>
                        </div>
                        <div class="col-md-6">
                            <input class="form-control" name="role_slug" value="{{ old('role_slug') }}" placeholder="slug_vi_du" required>
                        </div>
                    </div>
                    <div class="d-grid gap-2">
                        @foreach ($permissions as $permission)
                            <label class="d-flex align-items-center gap-2">
                                <input
                                    type="checkbox"
                                    name="permission_ids[]"
                                    value="{{ $permission->id }}"
                                    @checked(collect(old('permission_ids', []))->contains((string) $permission->id) || collect(old('permission_ids', []))->contains($permission->id))
                                >
                                <span>{{ $permission->name }} <small class="text-secondary">({{ $permission->slug }})</small></span>
                            </label>
                        @endforeach
                    </div>
                    <button class="btn btn-brand" type="submit">Tao role</button>
                </form>
            </section>
        </div>

        <div class="col-xl-7">
            <section class="panel-card h-100">
                <div class="panel-header">
                    <div>
                        <h3>Quan ly permissions</h3>
                        <p>Permission la quyen nho dung de bat/tat menu va action trong he thong.</p>
                    </div>
                </div>
                <form class="row g-2 mb-3" method="post" action="{{ route('admin.rbac.permissions.store') }}">
                    @csrf
                    <div class="col-md-5">
                        <input class="form-control" name="permission_name" value="{{ old('permission_name') }}" placeholder="Ten permission" required>
                    </div>
                    <div class="col-md-5">
                        <input class="form-control" name="permission_slug" value="{{ old('permission_slug') }}" placeholder="module.action" required>
                    </div>
                    <div class="col-md-2 d-grid">
                        <button class="btn btn-brand" type="submit">Tao</button>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table modern-table align-middle mb-0">
                        <thead>
                        <tr>
                            <th>Permission</th>
                            <th>Slug</th>
                            <th>Roles</th>
                            <th class="text-end">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($permissions as $permission)
                            <tr>
                                <td>
                                    <a class="fw-bold text-decoration-none" href="{{ route('admin.rbac.permissions.show', $permission) }}">
                                        {{ $permission->name }}
                                    </a>
                                </td>
                                <td><code>{{ $permission->slug }}</code></td>
                                <td>{{ $permission->roles_count ?? 0 }}</td>
                                <td class="text-end">
                                    <div class="d-inline-flex flex-wrap gap-2 justify-content-end">
                                        <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.rbac.permissions.show', $permission) }}">Xem</a>
                                        <a class="btn btn-sm btn-light" href="{{ route('admin.rbac.permissions.edit', $permission) }}">Sua</a>
                                        @if (! in_array($permission->slug, $protectedPermissionSlugs, true) && (int) ($permission->roles_count ?? 0) === 0)
                                            <form method="post" action="{{ route('admin.rbac.permissions.destroy', $permission) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger" type="submit">Xoa</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <div class="col-12">
            <section class="panel-card">
                <div class="panel-header">
                    <div>
                        <h3>Phan quyen theo role</h3>
                        <p>Cap nhat permission cho tung role trong he thong.</p>
                    </div>
                </div>
            </section>
        </div>

        @foreach ($roles as $role)
            <div class="col-xl-6">
                <section class="panel-card h-100">
                    <div class="panel-header">
                        <div>
                            <h3>{{ $role->name }}</h3>
                            <p>Slug: {{ $role->slug }} - {{ $role->users_count ?? 0 }} user</p>
                        </div>
                        <div class="d-inline-flex flex-wrap gap-2 justify-content-end">
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.rbac.roles.show', $role) }}">Xem</a>
                            <a class="btn btn-sm btn-light" href="{{ route('admin.rbac.roles.edit', $role) }}">Sua</a>
                            @if (! in_array($role->slug, $protectedRoleSlugs, true) && (int) ($role->users_count ?? 0) === 0)
                                <form method="post" action="{{ route('admin.rbac.roles.destroy', $role) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit">Xoa</button>
                                </form>
                            @endif
                        </div>
                    </div>
                    <form method="post" action="{{ route('admin.rbac.role-permissions.update', $role->id) }}">
                        @csrf
                        <div class="d-grid gap-2 mb-3">
                            @foreach ($permissions as $permission)
                                <label class="d-flex align-items-center gap-2">
                                    <input
                                        type="checkbox"
                                        name="permission_ids[]"
                                        value="{{ $permission->id }}"
                                        @checked($role->permissions->contains('id', $permission->id))
                                    >
                                    <span>{{ $permission->name }} <small class="text-secondary">({{ $permission->slug }})</small></span>
                                </label>
                            @endforeach
                        </div>
                        <button class="btn btn-sm btn-brand" type="submit">Cap nhat permissions</button>
                    </form>
                </section>
            </div>
        @endforeach
    </div>
@endsection
