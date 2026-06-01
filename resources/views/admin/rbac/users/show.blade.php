@extends('layouts.admin')

@section('title', 'Chi tiet user | Visitor Management')
@section('page_title', $user->name)
@section('page_subtitle', 'Thong tin dang nhap, role va audit gan day')

@section('content')
    <div class="row g-3">
        <div class="col-xl-8">
            <section class="panel-card mb-3">
                <div class="panel-header">
                    <div>
                        <h3>{{ $user->name }}</h3>
                        <p>{{ $user->email }}</p>
                    </div>
                    <span class="status-badge {{ $user->is_active ? 'status-approved' : 'status-checked-out' }}">
                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <div class="detail-grid">
                    <div class="detail-item">
                        <span>Email</span>
                        <strong>{{ $user->email }}</strong>
                    </div>
                    <div class="detail-item">
                        <span>Role</span>
                        <strong>{{ $user->roles->first()?->name ?? 'No role' }}</strong>
                    </div>
                    <div class="detail-item">
                        <span>Ho so nhan vien</span>
                        <strong>{{ $user->employeeProfile?->name ?? '-' }}</strong>
                    </div>
                    <div class="detail-item">
                        <span>Phong ban</span>
                        <strong>{{ $user->employeeProfile?->department?->name ?? '-' }}</strong>
                    </div>
                    <div class="detail-item">
                        <span>Ngay tao</span>
                        <strong>{{ $user->created_at?->format('Y-m-d H:i') ?? '-' }}</strong>
                    </div>
                    <div class="detail-item">
                        <span>Cap nhat</span>
                        <strong>{{ $user->updated_at?->format('Y-m-d H:i') ?? '-' }}</strong>
                    </div>
                </div>
            </section>

            <section class="panel-card">
                <div class="panel-header">
                    <div>
                        <h3>Audit gan day</h3>
                        <p>20 hanh dong moi nhat cua user nay.</p>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table modern-table align-middle mb-0">
                        <thead>
                        <tr>
                            <th>Thoi gian</th>
                            <th>Action</th>
                            <th>Doi tuong</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($logs as $log)
                            <tr>
                                <td>{{ $log->created_at?->format('Y-m-d H:i') }}</td>
                                <td>{{ $log->action }}</td>
                                <td>{{ $log->entity_type }} #{{ $log->entity_id }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-secondary">Chua co audit log.</td>
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
                        <p>Quan tri tai khoan dang nhap.</p>
                    </div>
                </div>
                <div class="d-grid gap-2">
                    <a class="btn btn-brand" href="{{ route('admin.rbac.users.edit', $user) }}">Sua user</a>
                    @if ((int) auth()->id() !== $user->id)
                        <form method="post" action="{{ route('admin.rbac.users.destroy', $user) }}">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-outline-danger w-100" type="submit">Xoa user</button>
                        </form>
                    @endif
                    <a class="btn btn-light" href="{{ route('admin.rbac.index') }}">Quay lai RBAC</a>
                </div>
            </section>
        </div>
    </div>
@endsection
