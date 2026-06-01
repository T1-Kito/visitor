@extends('layouts.admin')

@section('title', 'Nhân viên')
@section('page_title', 'Quản lý nhân viên')
@section('page_subtitle', 'Danh sách nhân viên tiếp khách, phòng ban và trạng thái hoạt động')

@section('content')
@php
    $employeeCollection = collect($employees);
    $totalEmployees = $employeeCollection->count();
    $activeEmployees = $employeeCollection->where('is_active', true)->count();
    $totalHostedVisits = $employeeCollection->sum('hosted_visits_count');
@endphp

<div class="resource-shell">
    <section class="resource-summary">
        <div class="resource-stat">
            <div class="resource-stat-icon"><i class="bi bi-people"></i></div>
            <div><span>Tổng nhân viên</span><strong>{{ $totalEmployees }}</strong></div>
        </div>
        <div class="resource-stat">
            <div class="resource-stat-icon"><i class="bi bi-check2-circle"></i></div>
            <div><span>Đang hoạt động</span><strong>{{ $activeEmployees }}</strong></div>
        </div>
        <div class="resource-stat">
            <div class="resource-stat-icon"><i class="bi bi-calendar2-check"></i></div>
            <div><span>Lượt tiếp khách</span><strong>{{ $totalHostedVisits }}</strong></div>
        </div>
    </section>

    <section class="resource-card">
        <div class="resource-toolbar">
            <form method="get" action="{{ route('admin.employees.index') }}">
                <div class="resource-search">
                    <i class="bi bi-search"></i>
                    <input class="form-control" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Tìm tên, email, số điện thoại, chức danh...">
                </div>
                <select class="form-select" name="department_id">
                    <option value="all">Tất cả phòng ban</option>
                    @foreach ($departments as $department)
                        <option value="{{ $department->id }}" @selected(($filters['department_id'] ?? 'all') === (string) $department->id)>
                            {{ $department->name }}
                        </option>
                    @endforeach
                </select>
                <button class="btn btn-light" type="submit"><i class="bi bi-funnel"></i> Lọc</button>
            </form>
            <button class="btn btn-brand" type="button" data-bs-toggle="modal" data-bs-target="#createEmployeeModal">
                <i class="bi bi-person-plus"></i>
                Tạo nhân viên
            </button>
        </div>

        <div class="table-responsive">
            <table class="resource-table">
                <thead>
                <tr>
                    <th>Nhân viên</th>
                    <th>Liên hệ</th>
                    <th>Phòng ban</th>
                    <th>Chức danh</th>
                    <th class="text-center">Số lịch</th>
                    <th>Trạng thái</th>
                    <th class="text-end">Thao tác</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($employees as $employee)
                    <tr>
                        <td>
                            <div class="resource-person">
                                <div class="resource-avatar">{{ strtoupper(mb_substr($employee->name, 0, 1)) }}</div>
                                <div>
                                    <a class="resource-name" href="{{ route('admin.employees.show', $employee) }}">{{ $employee->name }}</a>
                                    <span class="resource-muted">{{ $employee->job_title ?: 'Chưa có chức danh' }}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span>{{ $employee->email ?? '-' }}</span>
                            <span class="resource-muted">{{ $employee->phone ?? '-' }}</span>
                        </td>
                        <td>{{ $employee->department?->name ?? '-' }}</td>
                        <td>{{ $employee->job_title ?? '-' }}</td>
                        <td class="text-center"><span class="resource-pill">{{ $employee->hosted_visits_count }}</span></td>
                        <td>
                            <span class="status-badge {{ $employee->is_active ? 'status-approved' : 'status-checked-out' }}">
                                {{ $employee->is_active ? 'Đang hoạt động' : 'Ngừng hoạt động' }}
                            </span>
                        </td>
                        <td>
                            <div class="resource-actions">
                                <a class="resource-icon-btn" href="{{ route('admin.employees.show', $employee) }}" title="Xem chi tiết"><i class="bi bi-eye"></i></a>
                                <a class="resource-icon-btn" href="{{ route('admin.employees.edit', $employee) }}" title="Sửa nhân viên"><i class="bi bi-pencil"></i></a>
                                @if ($employee->hosted_visits_count === 0)
                                    <form method="post" action="{{ route('admin.employees.destroy', $employee) }}" onsubmit="return confirm('Xóa nhân viên này?')">
                                        @csrf
                                        @method('delete')
                                        <button class="resource-icon-btn danger" type="submit" title="Xóa nhân viên"><i class="bi bi-trash"></i></button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="resource-empty" colspan="7">
                            <i class="bi bi-people d-block fs-1 mb-2"></i>
                            Chưa có nhân viên phù hợp.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="resource-footer">
            <span>Hiển thị {{ $totalEmployees }} nhân viên</span>
            <a class="btn btn-light btn-sm" href="{{ route('admin.departments.index') }}"><i class="bi bi-building"></i> Quản lý phòng ban</a>
        </div>
    </section>
</div>

<div class="modal fade resource-modal" id="createEmployeeModal" tabindex="-1" aria-labelledby="createEmployeeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form class="modal-content" method="post" action="{{ route('admin.employees.store') }}">
            @csrf
            <div class="modal-header">
                <div>
                    <h5 class="modal-title" id="createEmployeeModalLabel">Tạo nhân viên</h5>
                    <div class="text-secondary small">Nhân viên có thể được chọn làm người tiếp khách.</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                <div class="resource-form-grid">
                    <div>
                        <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
                        <input class="form-control" name="name" value="{{ old('name') }}" placeholder="Ví dụ: Nguyễn Văn A" required>
                    </div>
                    <div>
                        <label class="form-label">Email</label>
                        <input class="form-control" type="email" name="email" value="{{ old('email') }}" placeholder="name@company.com">
                    </div>
                    <div>
                        <label class="form-label">Số điện thoại</label>
                        <input class="form-control" name="phone" value="{{ old('phone') }}" placeholder="0909 xxx xxx">
                    </div>
                    <div>
                        <label class="form-label">Chức danh</label>
                        <input class="form-control" name="job_title" value="{{ old('job_title') }}" placeholder="Trưởng nhóm, nhân viên...">
                    </div>
                    <div class="resource-field-wide">
                        <label class="form-label">Phòng ban <span class="text-danger">*</span></label>
                        <select class="form-select" name="department_id" required>
                            <option value="">Chọn phòng ban</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->id }}" @selected((string) old('department_id') === (string) $department->id)>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                <button class="btn btn-brand" type="submit"><i class="bi bi-check2-circle"></i> Lưu nhân viên</button>
            </div>
        </form>
    </div>
</div>
@endsection

@if ($errors->any())
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            new bootstrap.Modal(document.getElementById('createEmployeeModal')).show();
        });
    </script>
    @endpush
@endif
