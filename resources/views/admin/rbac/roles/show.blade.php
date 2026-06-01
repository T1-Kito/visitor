@extends('layouts.admin')

@section('title', 'Chi tiet role | Visitor Management')
@section('page_title', $role->name)
@section('page_subtitle', 'Thong tin role, permission va user dang su dung')

@section('content')
    <div class="row g-3">
        <div class="col-xl-8">
            <section class="panel-card mb-3">
                <div class="panel-header">
                    <div>
                        <h3>{{ $role->name }}</h3>
                        <p>Slug: {{ $role->slug }}</p>
                    </div>
                    @if ($isProtectedRole)
                        <span class="status-badge status-checked-in">System role</span>
                    @else
                        <span class="status-badge status-approved">Custom role</span>
                    @endif
                </div>
                <div class="detail-grid">
                    <div class="detail-item">
                        <span>Ten role</span>
                        <strong>{{ $role->name }}</strong>
                    </div>
                    <div class="detail-item">
                        <span>Slug</span>
                        <strong>{{ $role->slug }}</strong>
                    </div>
                    <div class="detail-item">
                        <span>So permission</span>
                        <strong>{{ $role->permissions->count() }}</strong>
                    </div>
                    <div class="detail-item">
                        <span>So user</span>
                        <strong>{{ $role->users->count() }}</strong>
                    </div>
                </div>
            </section>

            <section class="panel-card mb-3">
                <div class="panel-header">
                    <div>
                        <h3>Permissions</h3>
                        <p>Quyen hien dang gan cho role nay.</p>
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    @forelse ($role->permissions as $permission)
                        <a class="status-badge status-approved text-decoration-none" href="{{ route('admin.rbac.permissions.show', $permission) }}">
                            {{ $permission->name }}
                        </a>
                    @empty
                        <span class="text-secondary">Role nay chua co permission.</span>
                    @endforelse
                </div>
            </section>

            <section class="panel-card">
                <div class="panel-header">
                    <div>
                        <h3>Users dang dung role</h3>
                        <p>Danh sach tai khoan se bi anh huong neu role doi quyen.</p>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table modern-table align-middle mb-0">
                        <thead>
                        <tr>
                            <th>User</th>
                            <th>Email</th>
                            <th>Trang thai</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($role->users as $user)
                            <tr>
                                <td>
                                    <a class="fw-bold text-decoration-none" href="{{ route('admin.rbac.users.show', $user) }}">
                                        {{ $user->name }}
                                    </a>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <span class="status-badge {{ $user->is_active ? 'status-approved' : 'status-checked-out' }}">
                                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-secondary">Chua co user nao gan role nay.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <div class="col-xl-4">
            <section class="panel-card">
                <div class="panel-header">
                    <div>
                        <h3>Thao tac</h3>
                        <p>Quan tri role va matrix permission.</p>
                    </div>
                </div>
                <div class="d-grid gap-2">
                    <a class="btn btn-brand" href="{{ route('admin.rbac.roles.edit', $role) }}">Sua role</a>
                    @if (! $isProtectedRole && $role->users->isEmpty())
                        <form method="post" action="{{ route('admin.rbac.roles.destroy', $role) }}">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-outline-danger w-100" type="submit">Xoa role</button>
                        </form>
                    @endif
                    <a class="btn btn-light" href="{{ route('admin.rbac.index') }}">Quay lai RBAC</a>
                </div>
            </section>
        </div>
    </div>
@endsection
