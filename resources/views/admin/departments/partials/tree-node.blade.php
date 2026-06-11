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
            <button class="dept-icon-btn child" type="button" data-bs-toggle="modal" data-bs-target="#createDepartmentModal" data-parent-id="{{ $department->id }}" data-parent-name="{{ $department->name }}" title="Thêm phòng ban con" aria-label="Thêm phòng ban con"><i class="bi bi-plus-lg"></i></button>
            <a class="dept-icon-btn" href="{{ route('admin.departments.show', $department) }}" title="Xem chi tiết" aria-label="Xem chi tiết"><i class="bi bi-eye"></i></a>
            <a class="dept-icon-btn" href="{{ route('admin.departments.edit', $department) }}" title="Sửa phòng ban" aria-label="Sửa phòng ban"><i class="bi bi-pencil"></i></a>
            @if ($department->employees_count === 0 && $department->children_count === 0)
                <form method="post" action="{{ route('admin.departments.destroy', $department) }}" onsubmit="return confirm('Xóa phòng ban này?')" data-disable-on-submit>
                    @csrf
                    @method('delete')
                    <button class="dept-icon-btn danger" type="submit" title="Xóa phòng ban" aria-label="Xóa phòng ban"><i class="bi bi-trash"></i></button>
                </form>
            @endif
        </div>
    </div>

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
