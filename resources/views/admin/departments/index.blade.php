@extends('layouts.admin')

@section('title', 'Phòng ban')
@section('page_title', 'Quản lý phòng ban')
@section('page_subtitle', 'Danh mục phòng ban dùng cho nhân viên, phê duyệt và báo cáo')

@section('content')
@php
    $departmentCollection = collect($departments);
    $totalDepartments = $departmentCollection->count();
    $totalEmployees = $departmentCollection->sum('employees_count');
    $largestDepartment = $departmentCollection->sortByDesc('employees_count')->first();
@endphp

<div class="resource-shell">
    <section class="resource-summary">
        <div class="resource-stat">
            <div class="resource-stat-icon"><i class="bi bi-building"></i></div>
            <div><span>Tổng phòng ban</span><strong>{{ $totalDepartments }}</strong></div>
        </div>
        <div class="resource-stat">
            <div class="resource-stat-icon"><i class="bi bi-people"></i></div>
            <div><span>Tổng nhân viên</span><strong>{{ $totalEmployees }}</strong></div>
        </div>
        <div class="resource-stat">
            <div class="resource-stat-icon"><i class="bi bi-diagram-3"></i></div>
            <div><span>Phòng ban lớn nhất</span><strong>{{ $largestDepartment?->employees_count ?? 0 }}</strong></div>
        </div>
    </section>

    <section class="resource-card">
        <div class="resource-toolbar">
            <form method="get" action="{{ route('admin.departments.index') }}">
                <div class="resource-search">
                    <i class="bi bi-search"></i>
                    <input class="form-control" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Tìm mã hoặc tên phòng ban...">
                </div>
                <button class="btn btn-light" type="submit"><i class="bi bi-funnel"></i> Lọc</button>
            </form>
            <button class="btn btn-brand" type="button" data-bs-toggle="modal" data-bs-target="#createDepartmentModal">
                <i class="bi bi-building-add"></i>
                Tạo phòng ban
            </button>
        </div>

        <div class="table-responsive">
            <table class="resource-table">
                <thead>
                <tr>
                    <th>Mã phòng ban</th>
                    <th>Tên phòng ban</th>
                    <th class="text-center">Số nhân viên</th>
                    <th class="text-end">Thao tác</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($departments as $department)
                    <tr>
                        <td><a class="resource-pill" href="{{ route('admin.departments.show', $department) }}">{{ $department->code }}</a></td>
                        <td>
                            <div class="resource-person">
                                <div class="resource-avatar"><i class="bi bi-building"></i></div>
                                <div>
                                    <a class="resource-name" href="{{ route('admin.departments.show', $department) }}">{{ $department->name }}</a>
                                    <span class="resource-muted">Dùng cho nhân viên và báo cáo phòng ban</span>
                                </div>
                            </div>
                        </td>
                        <td class="text-center"><span class="resource-pill">{{ $department->employees_count }}</span></td>
                        <td>
                            <div class="resource-actions">
                                <a class="resource-icon-btn" href="{{ route('admin.departments.show', $department) }}" title="Xem chi tiết"><i class="bi bi-eye"></i></a>
                                <a class="resource-icon-btn" href="{{ route('admin.departments.edit', $department) }}" title="Sửa phòng ban"><i class="bi bi-pencil"></i></a>
                                @if ($department->employees_count === 0)
                                    <form method="post" action="{{ route('admin.departments.destroy', $department) }}" onsubmit="return confirm('Xóa phòng ban này?')">
                                        @csrf
                                        @method('delete')
                                        <button class="resource-icon-btn danger" type="submit" title="Xóa phòng ban"><i class="bi bi-trash"></i></button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="resource-empty" colspan="4">
                            <i class="bi bi-building d-block fs-1 mb-2"></i>
                            Chưa có phòng ban phù hợp.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="resource-footer">
            <span>Hiển thị {{ $totalDepartments }} phòng ban</span>
            <a class="btn btn-light btn-sm" href="{{ route('admin.employees.index') }}"><i class="bi bi-people"></i> Quản lý nhân viên</a>
        </div>
    </section>
</div>

<div class="modal fade resource-modal" id="createDepartmentModal" tabindex="-1" aria-labelledby="createDepartmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" method="post" action="{{ route('admin.departments.store') }}">
            @csrf
            <div class="modal-header">
                <div>
                    <h5 class="modal-title" id="createDepartmentModalLabel">Tạo phòng ban</h5>
                    <div class="text-secondary small">Thêm phòng ban mới để gán cho nhân viên.</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                <div>
                    <label class="form-label">Tên phòng ban <span class="text-danger">*</span></label>
                    <input class="form-control" name="name" value="{{ old('name') }}" placeholder="Nhân sự" required>
                    <div class="form-text">Mã phòng ban sẽ được hệ thống tự sinh từ tên phòng ban.</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                <button class="btn btn-brand" type="submit"><i class="bi bi-check2-circle"></i> Lưu phòng ban</button>
            </div>
        </form>
    </div>
</div>
@endsection

@if ($errors->any())
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            new bootstrap.Modal(document.getElementById('createDepartmentModal')).show();
        });
    </script>
    @endpush
@endif
