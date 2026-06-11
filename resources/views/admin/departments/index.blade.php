@extends('layouts.admin')

@section('title', 'Phòng ban')
@section('page_title', 'Quản lý phòng ban')
@section('page_subtitle', 'Tổ chức phòng ban theo cây cha-con cho nhân viên, phê duyệt và báo cáo')

@push('styles')
<style>
.dept-shell{display:grid;gap:1rem}
.dept-summary{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:.75rem}
.dept-stat{display:flex;align-items:center;gap:.85rem;background:#fff;border:1px solid #dce9f6;border-radius:16px;padding:.9rem 1rem}
.dept-stat-icon{width:42px;height:42px;border-radius:12px;display:grid;place-items:center;background:#eef6ff;color:#146bd7;font-size:1.05rem}
.dept-stat span{display:block;color:#647d99;font-size:.78rem}
.dept-stat strong{display:block;color:#0b1f3a;font-size:1.45rem;font-weight:700;line-height:1.1}
.dept-card{background:#fff;border:1px solid #dce9f6;border-radius:18px;overflow:hidden}
.dept-toolbar{display:flex;align-items:center;justify-content:space-between;gap:.8rem;padding:1rem 1.1rem;border-bottom:1px solid #edf3fb}
.dept-toolbar form{display:flex;align-items:center;gap:.65rem;flex:1;min-width:0}
.dept-create-btn{min-height:40px;padding:.48rem .8rem;border-radius:11px;font-size:.84rem;font-weight:500;box-shadow:0 8px 18px rgba(20,107,215,.14)}
.dept-create-btn i{font-size:.9rem}
.dept-search{position:relative;flex:1;max-width:680px}
.dept-search i{position:absolute;left:.85rem;top:50%;transform:translateY(-50%);color:#7a93b0}
.dept-search .form-control{height:42px;border-radius:12px;border-color:#d8e5f2;padding-left:2.4rem;font-size:.86rem}
.dept-tree-head{display:grid;grid-template-columns:minmax(300px,1fr) 140px 140px 110px;gap:.8rem;padding:.72rem 1.1rem;background:#f8fbff;border-bottom:1px solid #edf3fb;color:#6f86a3;font-size:.72rem;font-weight:700;text-transform:uppercase}
.dept-tree{padding:.65rem 1.1rem 1rem}
.dept-node{position:relative}
.dept-node-main{display:grid;grid-template-columns:minmax(300px,1fr) 140px 140px 110px;gap:.8rem;align-items:center;min-height:58px;border-bottom:1px solid #edf3fb}
.dept-node-main:hover{background:#fbfdff}
.dept-node-name{display:flex;align-items:center;gap:.7rem;min-width:0;padding-left:calc(var(--level,0) * 28px)}
.dept-node-marker{width:30px;height:30px;border-radius:10px;display:grid;place-items:center;background:#eaf3ff;color:#146bd7;flex:0 0 auto}
.dept-node.is-child .dept-node-marker{background:#f7fbff;border:1px solid #d8e5f2}
.dept-node-text{min-width:0}
.dept-node-title{display:flex;align-items:center;gap:.5rem;min-width:0}
.dept-node-title a{color:#0b1f3a;text-decoration:none;font-weight:650;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.dept-node-title a:hover{color:#146bd7}
.dept-chip{display:inline-flex;align-items:center;height:24px;padding:0 .55rem;border-radius:999px;background:#f1f6fc;color:#526b87;font-size:.72rem;font-weight:600}
.dept-node-meta{color:#7a93b0;font-size:.75rem;margin-top:.08rem}
.dept-count{justify-self:start;display:inline-flex;align-items:center;gap:.35rem;color:#0b1f3a;font-size:.82rem;font-weight:600}
.dept-count span{min-width:30px;height:30px;border-radius:999px;display:grid;place-items:center;background:#eef6ff;color:#146bd7}
.dept-actions{display:flex;justify-content:flex-end;gap:.45rem}
.dept-icon-btn{width:34px;height:34px;border:1px solid #d8e5f2;border-radius:10px;background:#fff;color:#146bd7;display:inline-grid;place-items:center;text-decoration:none}
.dept-icon-btn:hover{background:#eef6ff}
.dept-icon-btn.danger{color:#dc2626;border-color:#fecaca;background:#fffafa}
.dept-icon-btn.child{color:#0f766e;border-color:#bbf7d0;background:#f0fdf4}
.dept-children{margin-left:15px;border-left:1px dashed #cfe0f3}
.dept-children .dept-node-main{padding-left:.6rem}
.dept-empty{padding:2.2rem;text-align:center;color:#7a93b0}
.dept-footer{display:flex;align-items:center;justify-content:space-between;gap:.75rem;padding:.9rem 1.1rem;border-top:1px solid #edf3fb;color:#526b87}
@media(max-width:1100px){.dept-summary{grid-template-columns:1fr}.dept-tree-head{display:none}.dept-node-main{grid-template-columns:1fr;gap:.45rem;padding:.7rem 0}.dept-count,.dept-actions{margin-left:calc(var(--level,0) * 28px + 37px)}.dept-actions{justify-content:flex-start}.dept-toolbar{align-items:stretch;flex-direction:column}.dept-toolbar form{max-width:none}.dept-search{max-width:none}}
</style>
@endpush

@section('content')
@php
    $departmentCollection = collect($departments);
    $departmentOptions = collect($departmentOptions ?? $departments);
    $totalDepartments = $departmentCollection->count();
    $totalEmployees = $departmentCollection->sum('employees_count');
    $rootCount = $departmentCollection->whereNull('parent_id')->count();
    $visibleIds = $departmentCollection->pluck('id')->all();
    $rootDepartments = $departmentCollection
        ->filter(fn ($department) => blank($department->parent_id) || ! in_array($department->parent_id, $visibleIds, true))
        ->sortBy('name')
        ->values();
    $childrenByParent = $departmentCollection
        ->filter(fn ($department) => filled($department->parent_id))
        ->groupBy('parent_id')
        ->map(fn ($items) => $items->sortBy('name')->values());
@endphp

<div class="dept-shell">
    <section class="dept-summary">
        <div class="dept-stat">
            <div class="dept-stat-icon"><i class="bi bi-building"></i></div>
            <div><span>Tổng phòng ban</span><strong>{{ $totalDepartments }}</strong></div>
        </div>
        <div class="dept-stat">
            <div class="dept-stat-icon"><i class="bi bi-diagram-3"></i></div>
            <div><span>Phòng ban cấp 1</span><strong>{{ $rootCount }}</strong></div>
        </div>
        <div class="dept-stat">
            <div class="dept-stat-icon"><i class="bi bi-people"></i></div>
            <div><span>Tổng nhân viên</span><strong>{{ $totalEmployees }}</strong></div>
        </div>
    </section>

    <section class="dept-card">
        <div class="dept-toolbar">
            <form method="get" action="{{ route('admin.departments.index') }}">
                <div class="dept-search">
                    <i class="bi bi-search"></i>
                    <input class="form-control" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Tìm mã hoặc tên phòng ban...">
                </div>
                <button class="btn btn-light" type="submit"><i class="bi bi-funnel"></i> Lọc</button>
            </form>
            <button class="btn btn-brand dept-create-btn" type="button" data-bs-toggle="modal" data-bs-target="#createDepartmentModal">
                <i class="bi bi-building-add"></i>
                Tạo phòng ban cấp 1
            </button>
        </div>

        <div class="dept-tree-head">
            <span>Cấu trúc phòng ban</span>
            <span>Nhân viên</span>
            <span>Phòng ban con</span>
            <span class="text-end">Thao tác</span>
        </div>

        <div class="dept-tree">
            @forelse ($rootDepartments as $department)
                @include('admin.departments.partials.tree-node', [
                    'department' => $department,
                    'childrenByParent' => $childrenByParent,
                    'level' => 0,
                ])
            @empty
                <div class="dept-empty">
                    <i class="bi bi-diagram-3 d-block fs-1 mb-2"></i>
                    Chưa có phòng ban phù hợp.
                </div>
            @endforelse
        </div>

        <div class="dept-footer">
            <span>Hiển thị {{ $totalDepartments }} phòng ban theo dạng cây</span>
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
                    <div class="text-secondary small" id="createDepartmentModalHelp">Chọn “Không có” để tạo phòng ban cấp 1.</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body d-grid gap-3">
                <div>
                    <label class="form-label">Tên phòng ban <span class="text-danger">*</span></label>
                    <input class="form-control" name="name" value="{{ old('name') }}" placeholder="Nhân sự" required>
                    <div class="form-text">Mã phòng ban sẽ được hệ thống tự sinh từ tên phòng ban.</div>
                </div>
                <div>
                    <label class="form-label">Phòng ban cha</label>
                    <select id="departmentParentSelect" class="form-select" name="parent_id">
                        <option value="">Không có - phòng ban cấp 1</option>
                        @foreach ($departmentOptions as $option)
                            <option value="{{ $option->id }}" @selected((string) old('parent_id') === (string) $option->id)>
                                {{ $option->name }} ({{ $option->code }})
                            </option>
                        @endforeach
                    </select>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const modalEl = document.getElementById('createDepartmentModal');
    const parentSelect = document.getElementById('departmentParentSelect');
    const modalTitle = document.getElementById('createDepartmentModalLabel');
    const modalHelp = document.getElementById('createDepartmentModalHelp');
    const defaultTitle = 'Tạo phòng ban';
    const defaultHelp = 'Chọn “Không có” để tạo phòng ban cấp 1.';

    if (!modalEl || !parentSelect || !modalTitle || !modalHelp) return;

    modalEl.addEventListener('show.bs.modal', (event) => {
        const button = event.relatedTarget;
        if (!button) {
            modalTitle.textContent = defaultTitle;
            modalHelp.textContent = defaultHelp;
            return;
        }

        const parentId = button?.dataset?.parentId || '';
        const parentName = button?.dataset?.parentName || '';

        parentSelect.value = parentId;
        modalTitle.textContent = parentId ? 'Tạo phòng ban con' : defaultTitle;
        modalHelp.textContent = parentId
            ? `Phòng ban mới sẽ nằm dưới “${parentName}”.`
            : defaultHelp;
    });

    modalEl.addEventListener('hidden.bs.modal', () => {
        parentSelect.value = '';
        modalTitle.textContent = defaultTitle;
        modalHelp.textContent = defaultHelp;
    });
});
</script>
@endpush
