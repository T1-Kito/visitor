@php
    $childDepartments = $childrenByParent[$department->id] ?? collect();
    $hasChildren = $childDepartments->isNotEmpty();
@endphp

<div class="dept-node {{ $level > 0 ? 'is-child' : '' }}" style="--level: {{ $level }}">
    <div class="dept-node-main">
        <div class="dept-node-name">
            <div class="dept-node-marker">
                <i class="bi {{ $hasChildren ? 'bi-folder2-open' : 'bi-building' }}"></i>
            </div>
            <div class="dept-node-text">
                <div class="dept-node-title">
                    <a href="{{ route('admin.departments.show', $department) }}">{{ $department->name }}</a>
                    @if ($level === 0)
                        <span class="dept-chip">Cấp 1</span>
                    @endif
                </div>
                <div class="dept-node-meta">
                    {{ $department->code }}
                    @if ($department->parent)
                        · Thuộc {{ $department->parent->name }}
                    @endif
                </div>
            </div>
        </div>

        <div class="dept-count">
            <span>{{ $department->employees_count }}</span>
            <small>nhân viên</small>
        </div>

        <div class="dept-count">
            <span>{{ $department->children_count }}</span>
            <small>cấp dưới</small>
        </div>

        <div class="dept-actions">
            @if ($department->employees_count > 0)
                <button class="dept-icon-btn employee-toggle"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#departmentEmployees{{ $department->id }}"
                        aria-expanded="false"
                        aria-controls="departmentEmployees{{ $department->id }}"
                        title="Xem nhân viên"
                        aria-label="Xem nhân viên của {{ $department->name }}">
                    <i class="bi bi-chevron-down"></i>
                </button>
            @endif
            <button class="dept-icon-btn child" type="button" data-bs-toggle="modal" data-bs-target="#createDepartmentModal" data-parent-id="{{ $department->id }}" data-parent-name="{{ $department->name }}" title="Thêm phòng ban con" aria-label="Thêm phòng ban con"><i class="bi bi-plus-lg"></i></button>
            <div class="dropdown">
                <button class="dept-icon-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Tùy chọn" aria-label="Tùy chọn phòng ban">
                    <i class="bi bi-three-dots"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end dept-action-menu">
                    <a class="dropdown-item" href="{{ route('admin.departments.show', $department) }}"><i class="bi bi-eye"></i>Xem chi tiết</a>
                    <button class="dropdown-item"
                            type="button"
                            data-bs-toggle="modal"
                            data-bs-target="#editDepartmentModal"
                            data-edit-department
                            data-department-id="{{ $department->id }}"
                            data-department-name="{{ $department->name }}"
                            data-department-code="{{ $department->code }}"
                            data-parent-id="{{ $department->parent_id ?? '' }}"
                            data-update-url="{{ route('admin.departments.update', $department) }}">
                        <i class="bi bi-pencil"></i>Sửa phòng ban
                    </button>
                    @if ($department->employees_count === 0 && $department->children_count === 0)
                        <div class="dropdown-divider"></div>
                        <form method="post" action="{{ route('admin.departments.destroy', $department) }}" onsubmit="return confirm('Xóa phòng ban này?')" data-disable-on-submit>
                            @csrf
                            @method('delete')
                            <button class="dropdown-item danger" type="submit"><i class="bi bi-trash"></i>Xóa phòng ban</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if ($department->employees_count > 0)
        <div class="collapse" id="departmentEmployees{{ $department->id }}">
            <div class="dept-employees">
                <div class="dept-employees-head">
                    <span><i class="bi bi-people me-1"></i>Nhân viên thuộc {{ $department->name }}</span>
                    <span>{{ $department->employees_count }} người</span>
                </div>
                <div class="dept-employees-list">
                    @foreach ($department->employees as $employee)
                        <a class="dept-employee" href="{{ route('admin.employees.show', $employee) }}">
                            <span class="dept-employee-avatar">{{ mb_strtoupper(mb_substr($employee->name, 0, 1)) }}</span>
                            <span class="dept-employee-copy">
                                <strong>{{ $employee->name }}</strong>
                                <span>{{ $employee->job_title ?: ($employee->email ?: 'Chưa có chức danh') }}</span>
                            </span>
                            <span class="dept-employee-status {{ $employee->is_active ? 'active' : '' }}" title="{{ $employee->is_active ? 'Đang hoạt động' : 'Ngừng hoạt động' }}"></span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    @if ($hasChildren)
        <div class="dept-children">
            @foreach ($childDepartments as $childDepartment)
                @include('admin.departments.partials.tree-node', [
                    'department' => $childDepartment,
                    'childrenByParent' => $childrenByParent,
                    'level' => $level + 1,
                ])
            @endforeach
        </div>
    @endif
</div>
