@extends('layouts.admin')

@section('title', 'Chi tiet permission | Visitor Management')
@section('page_title', $permission->name)
@section('page_subtitle', 'Thong tin permission va cac role dang duoc cap')

@section('content')
    <div class="row g-3">
        <div class="col-xl-8">
            <section class="panel-card mb-3">
                <div class="panel-header">
                    <div>
                        <h3>{{ $permission->name }}</h3>
                        <p>Slug: {{ $permission->slug }}</p>
                    </div>
                    @if ($isProtectedPermission)
                        <span class="status-badge status-checked-in">System permission</span>
                    @else
                        <span class="status-badge status-approved">Custom permission</span>
                    @endif
                </div>
                <div class="detail-grid">
                    <div class="detail-item">
                        <span>Ten permission</span>
                        <strong>{{ $permission->name }}</strong>
                    </div>
                    <div class="detail-item">
                        <span>Slug</span>
                        <strong>{{ $permission->slug }}</strong>
                    </div>
                    <div class="detail-item">
                        <span>Dang gan cho role</span>
                        <strong>{{ $permission->roles->count() }}</strong>
                    </div>
                    <div class="detail-item">
                        <span>Cap nhat</span>
                        <strong>{{ $permission->updated_at?->format('Y-m-d H:i') ?? '-' }}</strong>
                    </div>
                </div>
            </section>

            <section class="panel-card">
                <div class="panel-header">
                    <div>
                        <h3>Roles co permission nay</h3>
                        <p>Role nao co permission thi user trong role do se co quyen tuong ung.</p>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table modern-table align-middle mb-0">
                        <thead>
                        <tr>
                            <th>Role</th>
                            <th>Slug</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($permission->roles as $role)
                            <tr>
                                <td>
                                    <a class="fw-bold text-decoration-none" href="{{ route('admin.rbac.roles.show', $role) }}">
                                        {{ $role->name }}
                                    </a>
                                </td>
                                <td><code>{{ $role->slug }}</code></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-secondary">Permission nay chua gan cho role nao.</td>
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
                        <p>Quan tri permission.</p>
                    </div>
                </div>
                <div class="d-grid gap-2">
                    <a class="btn btn-brand" href="{{ route('admin.rbac.permissions.edit', $permission) }}">Sua permission</a>
                    @if (! $isProtectedPermission && $permission->roles->isEmpty())
                        <form method="post" action="{{ route('admin.rbac.permissions.destroy', $permission) }}">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-outline-danger w-100" type="submit">Xoa permission</button>
                        </form>
                    @endif
                    <a class="btn btn-light" href="{{ route('admin.rbac.index') }}">Quay lai RBAC</a>
                </div>
            </section>
        </div>
    </div>
@endsection
