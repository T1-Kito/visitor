@extends('layouts.admin')

@section('title', 'Chi tiet phong ban | Visitor Management')
@section('page_title', 'Phong ban '.$department->code)
@section('page_subtitle', 'Thong tin phong ban va danh sach nhan vien')

@section('content')
    <div class="row g-3">
        <div class="col-xl-8">
            <section class="panel-card mb-3">
                <div class="panel-header">
                    <div>
                        <h3>{{ $department->name }}</h3>
                        <p>Code: {{ $department->code }}</p>
                    </div>
                    <span class="status-badge status-approved">{{ $department->employees_count }} nhan vien</span>
                </div>
                <div class="detail-grid">
                    <div class="detail-item">
                        <span>Code</span>
                        <strong>{{ $department->code }}</strong>
                    </div>
                    <div class="detail-item">
                        <span>Ten phong ban</span>
                        <strong>{{ $department->name }}</strong>
                    </div>
                    <div class="detail-item">
                        <span>Ngay tao</span>
                        <strong>{{ $department->created_at?->format('Y-m-d H:i') ?? '-' }}</strong>
                    </div>
                    <div class="detail-item">
                        <span>Cap nhat</span>
                        <strong>{{ $department->updated_at?->format('Y-m-d H:i') ?? '-' }}</strong>
                    </div>
                </div>
            </section>

            <section class="panel-card">
                <div class="panel-header">
                    <div>
                        <h3>Nhan vien trong phong ban</h3>
                        <p>Danh sach nguoi co the duoc chon lam host.</p>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table modern-table align-middle mb-0">
                        <thead>
                        <tr>
                            <th>Ten</th>
                            <th>Email</th>
                            <th>Chuc danh</th>
                            <th>So lich</th>
                            <th>Trang thai</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($department->employees as $employee)
                            <tr>
                                <td><a class="fw-bold text-decoration-none" href="{{ route('admin.employees.show', $employee) }}">{{ $employee->name }}</a></td>
                                <td>{{ $employee->email ?? '-' }}</td>
                                <td>{{ $employee->job_title ?? '-' }}</td>
                                <td>{{ $employee->hosted_visits_count }}</td>
                                <td>
                                    <span class="status-badge {{ $employee->is_active ? 'status-approved' : 'status-checked-out' }}">
                                        {{ $employee->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-secondary">Chua co nhan vien trong phong ban nay.</td>
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
                        <p>Quan tri phong ban.</p>
                    </div>
                </div>
                <div class="d-grid gap-2">
                    <a class="btn btn-brand" href="{{ route('admin.departments.edit', $department) }}">Sua phong ban</a>
                    @if ($department->employees_count === 0)
                        <form method="post" action="{{ route('admin.departments.destroy', $department) }}">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-outline-danger w-100" type="submit">Xoa phong ban</button>
                        </form>
                    @endif
                    <a class="btn btn-light" href="{{ route('admin.departments.index') }}">Quay lai danh sach</a>
                </div>
            </section>
        </div>
    </div>
@endsection
