@extends('layouts.admin')

@section('title', 'Chi tiet nhan vien | Visitor Management')
@section('page_title', $employee->name)
@section('page_subtitle', 'Ho so host va lich tiep khach gan day')

@section('content')
    <div class="row g-3">
        <div class="col-xl-8">
            <section class="panel-card mb-3">
                <div class="panel-header">
                    <div>
                        <h3>{{ $employee->name }}</h3>
                        <p>{{ $employee->job_title ?? 'Nhan vien' }}</p>
                    </div>
                    <span class="status-badge {{ $employee->is_active ? 'status-approved' : 'status-checked-out' }}">
                        {{ $employee->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <div class="detail-grid">
                    <div class="detail-item"><span>Email</span><strong>{{ $employee->email ?? '-' }}</strong></div>
                    <div class="detail-item"><span>So dien thoai</span><strong>{{ $employee->phone ?? '-' }}</strong></div>
                    <div class="detail-item"><span>Phong ban</span><strong>{{ $employee->department?->name ?? '-' }}</strong></div>
                    <div class="detail-item"><span>User login</span><strong>{{ $employee->user?->email ?? '-' }}</strong></div>
                </div>
            </section>

            <section class="panel-card">
                <div class="panel-header">
                    <div>
                        <h3>Lich tiep khach gan day</h3>
                        <p>20 lich moi nhat cua nhan vien nay.</p>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table modern-table align-middle mb-0">
                        <thead>
                        <tr>
                            <th>Ma lich</th>
                            <th>Khach</th>
                            <th>Gio hen</th>
                            <th>Trang thai</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($visits as $visit)
                            <tr>
                                <td><a class="fw-bold text-decoration-none" href="{{ route('admin.visits.show', $visit) }}">{{ $visit->code }}</a></td>
                                <td>{{ $visit->visitor?->full_name ?? '-' }}</td>
                                <td>{{ $visit->scheduled_at?->format('Y-m-d H:i') ?? '-' }}</td>
                                <td><x-status-badge :status="$visit->status" /></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-secondary">Chua co lich tiep khach.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <div class="col-xl-4">
            <section class="panel-card">
                <div class="panel-header">
                    <div><h3>Thao tac</h3><p>Quan tri nhan vien.</p></div>
                </div>
                <div class="d-grid gap-2">
                    <a class="btn btn-brand" href="{{ route('admin.employees.edit', $employee) }}">Sua nhan vien</a>
                    @if ($visits->isEmpty())
                        <form method="post" action="{{ route('admin.employees.destroy', $employee) }}">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-outline-danger w-100" type="submit">Xoa nhan vien</button>
                        </form>
                    @endif
                    <a class="btn btn-light" href="{{ route('admin.employees.index') }}">Quay lai danh sach</a>
                </div>
            </section>
        </div>
    </div>
@endsection
