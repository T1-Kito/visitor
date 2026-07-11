@extends('layouts.admin')

@section('title', 'Chi tiết phòng ban | Visitor Management')
@section('page_title', $department->name)
@section('page_subtitle', 'Thông tin phòng ban và danh sách nhân viên')

@section('content')
    <section class="entity-detail">
        <header class="entity-detail-head">
            <div class="entity-detail-identity">
                <div class="entity-detail-avatar"><i class="bi bi-building"></i></div>
                <div>
                    <h2 class="entity-detail-title">{{ $department->name }}</h2>
                    <p class="entity-detail-subtitle">{{ $department->code }}</p>
                </div>
            </div>

            <div class="entity-detail-actions">
                <span class="status-badge status-approved">{{ $department->employees_count }} nhân viên</span>
                <a class="btn btn-light" href="{{ route('admin.departments.index') }}">
                    <i class="bi bi-arrow-left"></i>
                    Quay lại
                </a>
                <button class="btn btn-brand" type="button" data-bs-toggle="modal" data-bs-target="#editDepartmentDetailModal">
                    <i class="bi bi-pencil"></i>
                    Sửa
                </button>
                @if ($department->employees_count === 0 && $department->children_count === 0)
                    <form method="post" action="{{ route('admin.departments.destroy', $department) }}" onsubmit="return confirm('Xóa phòng ban này?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-outline-danger" type="submit" title="Xóa phòng ban">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                @endif
            </div>
        </header>

        <div class="entity-detail-fields">
            <div class="entity-detail-field">
                <span>Mã phòng ban</span>
                <strong>{{ $department->code }}</strong>
            </div>
            <div class="entity-detail-field">
                <span>Phòng ban cha</span>
                <strong>{{ $department->parent?->name ?? 'Phòng ban cấp 1' }}</strong>
            </div>
            <div class="entity-detail-field">
                <span>Phòng ban con</span>
                <strong>{{ $department->children_count }}</strong>
            </div>
            <div class="entity-detail-field">
                <span>Cập nhật</span>
                <strong>{{ $department->updated_at?->format('H:i d/m/Y') ?? '-' }}</strong>
            </div>
        </div>

        <div class="entity-detail-section-head">
            <div>
                <h3>Nhân viên trong phòng ban</h3>
                <p>Danh sách nhân viên có thể được chọn làm người tiếp khách.</p>
            </div>
            <span class="entity-detail-count">{{ $department->employees_count }} nhân viên</span>
        </div>

        <div class="table-responsive">
            <table class="entity-detail-table">
                <thead>
                <tr>
                    <th>Nhân viên</th>
                    <th>Email</th>
                    <th>Chức danh</th>
                    <th>Số lịch</th>
                    <th>Trạng thái</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($department->employees as $employee)
                    <tr>
                        <td>
                            <a class="entity-detail-link" href="{{ route('admin.employees.show', $employee) }}">
                                {{ $employee->name }}
                            </a>
                        </td>
                        <td>{{ $employee->email ?? '-' }}</td>
                        <td>{{ $employee->job_title ?? '-' }}</td>
                        <td>{{ $employee->hosted_visits_count }}</td>
                        <td>
                            <span class="status-badge {{ $employee->is_active ? 'status-approved' : 'status-checked-out' }}">
                                {{ $employee->is_active ? 'Đang hoạt động' : 'Ngừng hoạt động' }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-secondary py-4">Chưa có nhân viên trong phòng ban này.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div class="modal fade resource-modal" id="editDepartmentDetailModal" tabindex="-1" aria-labelledby="editDepartmentDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content" method="post" action="{{ route('admin.departments.update', $department) }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="form_context" value="edit_department_detail">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="editDepartmentDetailModalLabel">Sửa phòng ban</h5>
                        <div class="text-secondary small">Cập nhật tên hoặc vị trí trong cây phòng ban.</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body d-grid gap-3">
                    <div>
                        <label class="form-label">Mã phòng ban</label>
                        <input class="form-control" value="{{ $department->code }}" readonly>
                    </div>
                    <div>
                        <label class="form-label">Tên phòng ban <span class="text-danger">*</span></label>
                        <input class="form-control" name="name_vi" value="{{ old('name_vi', $department->name_vi ?: $department->name) }}" required>
                    </div>
                    <div>
                        <label class="form-label">Tên phòng ban (English)</label>
                        <input class="form-control" name="name_en" value="{{ old('name_en', $department->name_en ?: $department->name) }}" required>
                        @error('name')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="form-label">Phòng ban cha</label>
                        <select class="form-select" name="parent_id">
                            <option value="">Không có - phòng ban cấp 1</option>
                            @foreach ($departmentOptions as $option)
                                <option value="{{ $option->id }}" @selected((string) old('parent_id', $department->parent_id) === (string) $option->id)>
                                    {{ $option->name }} ({{ $option->code }})
                                </option>
                            @endforeach
                        </select>
                        @error('parent_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                    <button class="btn btn-brand" type="submit">
                        <i class="bi bi-check2"></i>
                        Lưu thay đổi
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@if ($errors->any() && old('form_context') === 'edit_department_detail')
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const modalElement = document.getElementById('editDepartmentDetailModal');
                if (modalElement) {
                    bootstrap.Modal.getOrCreateInstance(modalElement).show();
                }
            });
        </script>
    @endpush
@endif
