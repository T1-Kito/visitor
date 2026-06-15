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

<div class="resource-shell employee-resource-shell">
    <section class="resource-summary resource-summary-dhl">
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
            <div class="d-flex gap-2 flex-wrap justify-content-end">
                <a class="btn btn-light" href="{{ route('admin.employees.import-template') }}">
                    <i class="bi bi-download"></i>
                    Tải mẫu
                </a>
                <button class="btn btn-light" type="button" data-bs-toggle="modal" data-bs-target="#importEmployeeModal">
                    <i class="bi bi-upload"></i>
                    Import
                </button>
                <button class="btn btn-brand" type="button" data-bs-toggle="modal" data-bs-target="#createEmployeeModal">
                    <i class="bi bi-person-plus"></i>
                    Tạo nhân viên
                </button>
            </div>
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
                                <button class="resource-icon-btn"
                                        type="button"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editEmployeeModal"
                                        data-edit-employee
                                        data-employee-id="{{ $employee->id }}"
                                        data-employee-name="{{ $employee->name }}"
                                        data-employee-email="{{ $employee->email }}"
                                        data-employee-phone="{{ $employee->phone }}"
                                        data-employee-job-title="{{ $employee->job_title }}"
                                        data-department-id="{{ $employee->department_id }}"
                                        data-employee-active="{{ $employee->is_active ? '1' : '0' }}"
                                        data-update-url="{{ route('admin.employees.update', $employee) }}"
                                        title="Sửa nhân viên"
                                        aria-label="Sửa nhân viên {{ $employee->name }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
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

<div class="modal fade resource-modal" id="importEmployeeModal" tabindex="-1" aria-labelledby="importEmployeeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" method="post" action="{{ route('admin.employees.import') }}" enctype="multipart/form-data">
            @csrf
            <div class="modal-header">
                <div>
                    <h5 class="modal-title" id="importEmployeeModalLabel">Import nhân viên</h5>
                    <div class="text-secondary small">Tải file mẫu, điền dữ liệu rồi import lại vào hệ thống.</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info small">
                    File Excel cần có các cột:
                    <strong>Họ và tên, Email, Số điện thoại, Chức danh, Phòng ban, Đang hoạt động</strong>.
                    Phòng ban chưa có sẽ được tự tạo. Nếu email đã tồn tại, hệ thống sẽ cập nhật nhân viên đó.
                </div>
                <label class="form-label">File Excel/CSV <span class="text-danger">*</span></label>
                <input class="form-control" type="file" name="import_file" accept=".xlsx,.csv,text/csv,text/plain" required>
                @error('import_file', 'importEmployees')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
            </div>
            <div class="modal-footer">
                <a class="btn btn-light" href="{{ route('admin.employees.import-template') }}"><i class="bi bi-download"></i> Tải file mẫu</a>
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                <button class="btn btn-brand" type="submit"><i class="bi bi-upload"></i> Import nhân viên</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade resource-modal" id="createEmployeeModal" tabindex="-1" aria-labelledby="createEmployeeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form class="modal-content" method="post" action="{{ route('admin.employees.store') }}">
            @csrf
            <input type="hidden" name="form_context" value="create_employee">
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

@include('admin.employees.partials.edit-modal')
@endsection

@if ($errors->any())
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            @if ($errors->importEmployees->any())
                new bootstrap.Modal(document.getElementById('importEmployeeModal')).show();
            @elseif (old('form_context') === 'edit_employee')
                new bootstrap.Modal(document.getElementById('editEmployeeModal')).show();
            @else
                new bootstrap.Modal(document.getElementById('createEmployeeModal')).show();
            @endif
        });
    </script>
    @endpush
@endif
