@extends('layouts.admin')

@section('title', 'Phòng ban')
@section('page_title', 'Quản lý phòng ban')
@section('page_subtitle', 'Tổ chức phòng ban theo cây cha-con cho nhân viên, phê duyệt và báo cáo')

@push('styles')
<style>
.dept-shell{display:grid;gap:1rem}
.dept-summary{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:.75rem}
.dept-stat{display:flex;align-items:center;gap:.85rem;background:#ffcc00;border:1px solid #e0b400;border-radius:16px;padding:.9rem 1rem;box-shadow:none}
.dept-stat-icon{width:42px;height:42px;border-radius:12px;display:grid;place-items:center;background:rgba(255,255,255,.48);border:1px solid rgba(212,5,17,.18);color:#d40511;font-size:1.05rem}
.dept-stat span{display:block;color:#111827;font-size:.78rem}
.dept-stat strong{display:block;color:#111827;font-size:1.45rem;font-weight:700;line-height:1.1}
.dept-card{background:#fff;border:1px solid #dce9f6;border-radius:18px;overflow:hidden}
.dept-toolbar{display:flex;align-items:center;justify-content:space-between;gap:.8rem;padding:1rem 1.1rem;border-bottom:1px solid #edf3fb}
.dept-toolbar form{display:flex;align-items:center;gap:.65rem;flex:1;min-width:0}
.dept-create-btn{min-height:40px;padding:.48rem .8rem;border-radius:11px;font-size:.84rem;font-weight:500;box-shadow:0 8px 18px rgba(20,107,215,.14)}
.dept-create-btn i{font-size:.9rem}
.dept-search{position:relative;flex:1;max-width:680px}
.dept-search i{position:absolute;left:.85rem;top:50%;transform:translateY(-50%);color:#7a93b0}
.dept-search .form-control{height:42px;border-radius:12px;border-color:#d8e5f2;padding-left:2.4rem;font-size:.86rem}
.dept-tree-head{display:grid;grid-template-columns:minmax(340px,1fr) 130px 130px 124px;gap:.8rem;padding:.72rem 1.1rem;background:#fafbfc;border-bottom:1px solid #edf1f5;color:#718096;font-size:.7rem;font-weight:600;text-transform:uppercase}
.dept-tree{padding:0 1.1rem}
.dept-node{position:relative}
.dept-node-main{display:grid;grid-template-columns:minmax(340px,1fr) 130px 130px 124px;gap:.8rem;align-items:center;min-height:58px;border-bottom:1px solid #edf1f5}
.dept-node-main:hover{background:#fcfcfd}
.dept-node-name{display:flex;align-items:center;gap:.7rem;min-width:0;padding-left:calc(var(--level,0) * 28px)}
.dept-node-marker{width:30px;height:30px;border-radius:9px;display:grid;place-items:center;background:#fff8d6;color:var(--gate-blue);border:1px solid #f5e6a7;flex:0 0 auto}
.dept-node.is-child .dept-node-marker{background:#f7fbff;border:1px solid #d8e5f2}
.dept-node-text{min-width:0}
.dept-node-title{display:flex;align-items:center;gap:.5rem;min-width:0}
.dept-node-title a{color:#0b1f3a;text-decoration:none;font-weight:650;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.dept-node-title a:hover{color:var(--gate-blue)}
.dept-chip{display:inline-flex;align-items:center;height:24px;padding:0 .55rem;border-radius:999px;background:#f1f6fc;color:#526b87;font-size:.72rem;font-weight:600}
.dept-node-meta{color:#7a93b0;font-size:.75rem;margin-top:.08rem}
.dept-count{justify-self:start;display:inline-flex;align-items:center;gap:.38rem;color:#344054;font-size:.8rem;font-weight:500}
.dept-count span{min-width:28px;height:28px;border-radius:999px;display:grid;place-items:center;background:#f2f4f7;color:#344054;font-weight:600}
.dept-actions{display:flex;align-items:center;justify-content:flex-end;gap:.35rem}
.dept-icon-btn{width:32px;height:32px;border:1px solid #d8e2ec;border-radius:9px;background:#fff;color:#526b87;display:inline-grid;place-items:center;text-decoration:none}
.dept-icon-btn:hover{border-color:#f0b8bc;background:#fff7f7;color:var(--gate-blue)}
.dept-icon-btn.danger{color:#dc2626;border-color:#fecaca;background:#fffafa}
.dept-icon-btn.child{color:var(--gate-blue);border-color:#f0b8bc;background:#fff}
.dept-icon-btn.employee-toggle{color:#344054;background:#fff}
.dept-icon-btn.employee-toggle[aria-expanded="true"]{color:var(--gate-blue);border-color:#f0b8bc;background:#fff7f7}
.dept-icon-btn.employee-toggle i{transition:transform .18s ease}
.dept-icon-btn.employee-toggle[aria-expanded="true"] i{transform:rotate(180deg)}
.dept-action-menu{min-width:190px;padding:.4rem;border:1px solid #e2e8f0;border-radius:12px;box-shadow:0 14px 36px rgba(15,23,42,.12)}
.dept-action-menu .dropdown-item{display:flex;align-items:center;gap:.55rem;min-height:36px;border-radius:8px;color:#344054;font-size:.78rem}
.dept-action-menu .dropdown-item i{width:18px;color:var(--gate-blue)}
.dept-action-menu form{margin:0}
.dept-action-menu .danger{color:#b42318}
.dept-action-menu .danger i{color:#dc2626}
.dept-children{margin-left:15px;border-left:1px dashed #cfe0f3}
.dept-children .dept-node-main{padding-left:.6rem}
.dept-employees{margin:0 0 .45rem calc(var(--level,0) * 28px + 40px);border:1px solid #e5eaf0;border-radius:12px;background:#fafbfc;overflow:hidden}
.dept-employees-head{display:flex;align-items:center;justify-content:space-between;gap:.75rem;padding:.6rem .75rem;border-bottom:1px solid #e8edf2;color:#667085;font-size:.72rem}
.dept-employees-list{display:grid;grid-template-columns:repeat(auto-fill,minmax(235px,1fr));gap:0}
.dept-employee{display:flex;align-items:center;gap:.6rem;min-width:0;padding:.65rem .75rem;border-right:1px solid #edf1f5;border-bottom:1px solid #edf1f5;text-decoration:none}
.dept-employee:hover{background:#fff}
.dept-employee-avatar{width:30px;height:30px;display:grid;place-items:center;flex:0 0 auto;border-radius:9px;background:#fff1f2;color:var(--gate-blue);font-size:.72rem;font-weight:600}
.dept-employee-copy{min-width:0}
.dept-employee-copy strong{display:block;overflow:hidden;color:#1d2939;font-size:.78rem;font-weight:600;text-overflow:ellipsis;white-space:nowrap}
.dept-employee-copy span{display:block;overflow:hidden;margin-top:.05rem;color:#8492a6;font-size:.7rem;text-overflow:ellipsis;white-space:nowrap}
.dept-employee-status{width:7px;height:7px;margin-left:auto;flex:0 0 auto;border-radius:999px;background:#98a2b3}
.dept-employee-status.active{background:#12b76a}
.dept-empty{padding:2.2rem;text-align:center;color:#7a93b0}
.dept-footer{display:flex;align-items:center;justify-content:space-between;gap:.75rem;padding:.9rem 1.1rem;border-top:1px solid #edf3fb;color:#526b87}
@media(min-width:992px){.dept-shell{padding-inline:1rem}}
@media(max-width:1100px){.dept-summary{grid-template-columns:1fr}.dept-tree-head{display:none}.dept-node-main{grid-template-columns:1fr;gap:.45rem;padding:.7rem 0}.dept-count,.dept-actions{margin-left:calc(var(--level,0) * 28px + 37px)}.dept-actions{justify-content:flex-start}.dept-employees{margin-left:calc(var(--level,0) * 28px + 37px)}.dept-toolbar{align-items:stretch;flex-direction:column}.dept-toolbar form{max-width:none}.dept-search{max-width:none}}
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
                            <option value="{{ $option->id }}"
                                    data-parent-id="{{ $option->parent_id ?? '' }}"
                                    @selected((string) old('parent_id') === (string) $option->id)>
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

<div class="modal fade resource-modal" id="editDepartmentModal" tabindex="-1" aria-labelledby="editDepartmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="editDepartmentForm"
              class="modal-content"
              method="post"
              action="{{ old('department_id') ? route('admin.departments.update', old('department_id')) : '#' }}"
              data-disable-on-submit>
            @csrf
            @method('put')
            <input type="hidden" name="form_context" value="edit_department">
            <input id="editDepartmentId" type="hidden" name="department_id" value="{{ old('department_id') }}">

            <div class="modal-header">
                <div>
                    <h5 class="modal-title" id="editDepartmentModalLabel">Sửa phòng ban</h5>
                    <div class="text-secondary small">Cập nhật tên hoặc vị trí trong cây phòng ban.</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>

            <div class="modal-body d-grid gap-3">
                <div>
                    <label class="form-label">Mã phòng ban</label>
                    <div id="editDepartmentCode" class="form-control bg-light text-secondary" aria-readonly="true">-</div>
                    <div class="form-text">Mã sẽ tự cập nhật nếu tên phòng ban thay đổi.</div>
                </div>
                <div>
                    <label class="form-label" for="editDepartmentName">Tên phòng ban <span class="text-danger">*</span></label>
                    <input id="editDepartmentName"
                           class="form-control"
                           name="name"
                           value="{{ old('form_context') === 'edit_department' ? old('name') : '' }}"
                           required>
                </div>
                <div>
                    <label class="form-label" for="editDepartmentParentSelect">Phòng ban cha</label>
                    <select id="editDepartmentParentSelect" class="form-select" name="parent_id">
                        <option value="">Không có - phòng ban cấp 1</option>
                        @foreach ($departmentOptions as $option)
                            <option value="{{ $option->id }}"
                                    data-parent-id="{{ $option->parent_id ?? '' }}"
                                    @selected(old('form_context') === 'edit_department' && (string) old('parent_id') === (string) $option->id)>
                                {{ $option->name }} ({{ $option->code }})
                            </option>
                        @endforeach
                    </select>
                    <div class="form-text">Không thể chọn chính phòng ban này hoặc phòng ban cấp dưới làm cha.</div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                <button class="btn btn-brand" type="submit">
                    <i class="bi bi-check2-circle"></i>
                    <span>Lưu thay đổi</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@if ($errors->any())
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modalId = @json(old('form_context') === 'edit_department' ? 'editDepartmentModal' : 'createDepartmentModal');
            const modalElement = document.getElementById(modalId);
            if (modalElement) {
                new bootstrap.Modal(modalElement).show();
            }
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

document.addEventListener('DOMContentLoaded', () => {
    const modalEl = document.getElementById('editDepartmentModal');
    const form = document.getElementById('editDepartmentForm');
    const departmentIdInput = document.getElementById('editDepartmentId');
    const nameInput = document.getElementById('editDepartmentName');
    const codeDisplay = document.getElementById('editDepartmentCode');
    const parentSelect = document.getElementById('editDepartmentParentSelect');

    if (!modalEl || !form || !departmentIdInput || !nameInput || !codeDisplay || !parentSelect) return;

    const editButtons = Array.from(document.querySelectorAll('[data-edit-department]'));

    const findEditButton = (departmentId) => editButtons.find(
        (button) => String(button.dataset.departmentId) === String(departmentId)
    );

    const descendantIds = (departmentId) => {
        const blockedIds = new Set([String(departmentId)]);
        let foundNewDescendant = true;

        while (foundNewDescendant) {
            foundNewDescendant = false;

            Array.from(parentSelect.options).forEach((option) => {
                const optionId = String(option.value || '');
                const parentId = String(option.dataset.parentId || '');

                if (optionId && !blockedIds.has(optionId) && blockedIds.has(parentId)) {
                    blockedIds.add(optionId);
                    foundNewDescendant = true;
                }
            });
        }

        return blockedIds;
    };

    const configureParentOptions = (departmentId) => {
        const blockedIds = descendantIds(departmentId);

        Array.from(parentSelect.options).forEach((option) => {
            option.disabled = option.value !== '' && blockedIds.has(String(option.value));
        });
    };

    const populateForm = (button, preserveOldInput = false) => {
        if (!button) return;

        const departmentId = button.dataset.departmentId || '';
        form.action = button.dataset.updateUrl || '#';
        departmentIdInput.value = departmentId;
        codeDisplay.textContent = button.dataset.departmentCode || '-';
        configureParentOptions(departmentId);

        if (!preserveOldInput) {
            nameInput.value = button.dataset.departmentName || '';
            parentSelect.value = button.dataset.parentId || '';
        }
    };

    modalEl.addEventListener('show.bs.modal', (event) => {
        if (event.relatedTarget) {
            populateForm(event.relatedTarget);
            return;
        }

        const button = findEditButton(departmentIdInput.value);
        populateForm(button, true);
    });

    modalEl.addEventListener('hidden.bs.modal', () => {
        form.action = '#';
        departmentIdInput.value = '';
        nameInput.value = '';
        codeDisplay.textContent = '-';
        parentSelect.value = '';

        Array.from(parentSelect.options).forEach((option) => {
            option.disabled = false;
        });
    });
});
</script>
@endpush
