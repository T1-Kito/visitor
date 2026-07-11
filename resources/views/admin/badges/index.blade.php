@extends('layouts.admin')

@section('title', 'Số thẻ khách')
@section('page_title', 'Quản lý số thẻ khách')
@section('page_subtitle', 'Thêm, sửa, xóa danh sách thẻ visitor hiển thị trên kiosk')

@push('styles')
<style>
.badge-admin{display:grid;gap:1rem}
.badge-toolbar{display:flex;justify-content:flex-start;align-items:center;padding:0 1rem}
.badge-table-card{border:1px solid #e4edf8;border-radius:18px;background:#fff;overflow:hidden;box-shadow:0 8px 24px rgba(15,23,42,.04)}
.badge-table-card .modern-table{min-width:900px}
.badge-table-card .modern-table thead th{padding:1rem 1.1rem;background:#f8fafc;border-bottom:1px solid #dfe8f2}
.badge-table-card .modern-table tbody td{padding:.8rem 1.1rem;border-bottom:1px solid #edf2f7}
.badge-table-card .modern-table tbody tr:last-child td{border-bottom:0}
.badge-table-card .modern-table tbody tr:hover{background:#fbfdff}
.badge-actions{display:grid;grid-template-columns:minmax(0,1fr) 76px;gap:.55rem;align-items:center;width:100%}
.badge-actions form{margin:0}
.badge-inline-form{display:grid;grid-template-columns:repeat(2,minmax(190px,1fr)) minmax(160px,.8fr) 74px;gap:.55rem;align-items:center;min-width:0}
.badge-inline-form input{max-width:none;width:100%}
.badge-value{min-height:40px;display:flex;align-items:center;padding:.35rem .1rem;color:#172033;font-size:.93rem;line-height:1.35}
.badge-value-status{justify-content:flex-start}
.badge-edit-control,.badge-save-btn{display:none}
.badge-inline-form.is-editing .badge-value,.badge-inline-form.is-editing .badge-edit-toggle{display:none}
.badge-inline-form.is-editing .badge-edit-control,.badge-inline-form.is-editing .badge-save-btn{display:block}
.badge-edit-head{display:grid;grid-template-columns:repeat(2,minmax(190px,1fr)) minmax(160px,.8fr) 74px 76px;gap:.55rem;align-items:center;min-width:720px;text-align:left}
.badge-edit-head span{color:#334155;font-size:.75rem;font-weight:800;white-space:nowrap;text-transform:uppercase;letter-spacing:.02em}
.badge-edit-head span:last-child{text-align:center;grid-column:4/6}
.badge-inline-form .form-control,.badge-inline-form .form-select{height:40px;min-height:40px;padding:.42rem .7rem;border-radius:11px;font-size:.86rem;background-color:#fff}
.badge-actions .btn,.badge-compact-btn{height:40px;min-height:40px;width:100%;padding:.35rem .6rem;border-radius:11px;font-size:.84rem;font-weight:600;white-space:nowrap}
.badge-add-btn{min-height:38px;padding:.42rem .8rem;border:1px solid #f2b8bf;background:#fff7f7;color:#d40511;border-radius:12px;font-weight:700}
.badge-add-btn:hover,.badge-add-btn:focus{border-color:#d40511;background:#fff7f7;color:#d40511}
.badge-save-btn{border-color:#d8e5f2;background:#fff;color:#334b67}
.badge-save-btn:hover,.badge-save-btn:focus{border-color:#b8cbe0;background:#f8fafc;color:#334b67}
.badge-note{color:#7187a3;font-size:.78rem}
.badge-status-select{min-width:135px}
.badge-modal .modal-dialog{max-width:760px}
.badge-modal .modal-content{border:0;border-radius:22px;box-shadow:0 28px 80px rgba(15,23,42,.28)}
.badge-modal .modal-header{padding:1.25rem 1.35rem .9rem;border-bottom:1px solid #eef3f8}
.badge-modal .modal-title{font-size:1.25rem;font-weight:800}
.badge-modal .modal-body{padding:1.15rem 1.5rem;display:grid;gap:1rem;min-height:310px}
.badge-modal .modal-footer{padding:.9rem 1.35rem 1.15rem;border-top:1px solid #eef3f8;gap:.55rem}
.badge-modal label{font-size:.84rem;font-weight:700;color:#334155;margin-bottom:.35rem}
.badge-modal .form-control,.badge-modal .form-select{min-height:42px;border-radius:13px;font-size:.95rem}
.badge-mode-tabs{display:grid;grid-template-columns:1fr 1fr;gap:.5rem;padding:.28rem;border:1px solid #e6edf5;border-radius:15px;background:#f8fafc}
.badge-mode-tabs button{border:0;border-radius:12px;background:transparent;color:#475569;font-weight:700;padding:.55rem .7rem}
.badge-mode-tabs button.active{background:#fff;color:#d40511;box-shadow:0 6px 18px rgba(15,23,42,.08)}
.badge-mode-panel{display:none}
.badge-mode-panel.active{display:grid;gap:.75rem}
.badge-range-grid{display:grid;grid-template-columns:minmax(240px,1fr) 130px 130px;gap:.75rem}
.badge-help{color:#64748b;font-size:.8rem;line-height:1.35;margin-top:.25rem}
.badge-example{padding:.65rem .75rem;border-radius:13px;background:#fff9e6;border:1px dashed #ffcc00;color:#654400;font-size:.82rem}
.badge-modal .btn-light{border-radius:12px;padding:.45rem .9rem}
@media(max-width:900px){.badge-toolbar{padding:0}.badge-edit-head{display:none}}
@media(max-width:640px){.badge-range-grid,.badge-mode-tabs{grid-template-columns:1fr}.badge-modal .modal-dialog{margin:.75rem}}
</style>
@endpush

@section('content')
<div class="badge-admin">
    <div class="badge-toolbar">
        <button class="btn badge-add-btn"
                type="button"
                data-create-badge-trigger
                onclick="window.openBadgeModal()">
            <i class="bi bi-plus-circle"></i> Thêm thẻ
        </button>
    </div>

    <section class="badge-table-card">
        <div class="table-responsive">
            <table class="table modern-table align-middle mb-0">
                <thead>
                <tr>
                    <th>
                        <div class="badge-edit-head">
                            <span>Tên tiếng Việt</span>
                            <span>Tên English</span>
                            <span>Trạng thái</span>
                            <span>Thao tác</span>
                        </div>
                    </th>
                </tr>
                </thead>
                <tbody>
                @forelse ($badges as $badge)
                    <tr>
                        <td>
                            <div class="badge-actions">
                                <form class="badge-inline-form" method="post" action="{{ route('admin.badges.update', $badge) }}">
                                    @csrf
                                    @method('put')
                                    <input type="hidden" name="badge_no" value="{{ $badge->badge_no }}">
                                    <span class="badge-value">{{ $badge->label_vi ?: $badge->badge_no }}</span>
                                    <input class="form-control form-control-sm badge-edit-control" name="label_vi" value="{{ $badge->label_vi ?: $badge->badge_no }}" required maxlength="120" title="Tên tiếng Việt" placeholder="Tên tiếng Việt">
                                    <span class="badge-value">{{ $badge->label_en ?: $badge->badge_no }}</span>
                                    <input class="form-control form-control-sm badge-edit-control" name="label_en" value="{{ $badge->label_en ?: $badge->badge_no }}" required maxlength="120" title="English name" placeholder="English name">
                                    <span class="badge-value badge-value-status">
                                        @if ($badge->status === 'active')
                                            <span class="status-badge status-checked-in">Đang sử dụng</span>
                                        @elseif ($badge->status === 'revoked')
                                            <span class="status-badge status-checked-out">Tạm khóa</span>
                                        @else
                                            <span class="status-badge status-approved">Sẵn sàng cấp</span>
                                        @endif
                                    </span>
                                    <select class="form-select form-select-sm badge-status-select badge-edit-control" name="status" @disabled($badge->status === 'active')>
                                        <option value="available" @selected($badge->status === 'available')>Sẵn sàng cấp</option>
                                        <option value="revoked" @selected($badge->status === 'revoked')>Tạm khóa</option>
                                    </select>
                                    @if ($badge->status === 'active')
                                        <input type="hidden" name="status" value="available">
                                    @endif
                                    <button class="btn btn-sm badge-save-btn" type="submit"><i class="bi bi-check2"></i> Lưu</button>
                                    <button class="btn btn-sm badge-save-btn-outline badge-edit-toggle" type="button"><i class="bi bi-pencil"></i> Sửa</button>
                                </form>
                                <form method="post" action="{{ route('admin.badges.destroy', $badge) }}" onsubmit="return confirm('Xóa số thẻ này?')" data-disable-on-submit>
                                    @csrf
                                    @method('delete')
                                    <button class="btn btn-sm btn-outline-danger" type="submit" @disabled($badge->status === 'active')><i class="bi bi-trash"></i> Xóa</button>
                                </form>
                            </div>
                            @if ($badge->status === 'active')
                                <div class="badge-note text-end mt-1">Thẻ đang dùng chỉ nên sửa mã, không xóa.</div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="text-center text-muted py-4">Chưa có số thẻ khách. Hãy thêm thẻ để kiosk hiển thị trong danh sách chọn.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
<div class="modal fade resource-modal badge-modal" id="createBadgeModal" tabindex="-1" aria-labelledby="createBadgeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" method="post" action="{{ route('admin.badges.store') }}">
            @csrf
            <input type="hidden" name="form_context" value="create_badge">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title" id="createBadgeModalLabel">Thêm thẻ khách</h5>
                    <div class="text-secondary small">Tạo nhanh một thẻ hoặc nhiều thẻ cho kiosk.</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                <div class="badge-mode-tabs" role="tablist" aria-label="Cách thêm thẻ">
                    <button type="button" class="active" data-badge-mode="range"><i class="bi bi-magic"></i> Tạo dải số</button>
                    <button type="button" data-badge-mode="list"><i class="bi bi-card-text"></i> Thêm một thẻ</button>
                </div>

                <div class="badge-mode-panel active" data-badge-panel="range">
                    <div class="badge-range-grid">
                        <div>
                            <label class="form-label">Tiền tố</label>
                            <input class="form-control" name="badge_prefix" value="{{ old('form_context') === 'create_badge' ? old('badge_prefix', 'Visitor card') : 'Visitor card' }}" placeholder="Visitor card" maxlength="30">
                        </div>
                        <div>
                            <label class="form-label">Từ</label>
                            <input class="form-control @error('badge_range_start') is-invalid @enderror" type="number" min="1" max="9999" name="badge_range_start" value="{{ old('form_context') === 'create_badge' ? old('badge_range_start') : '' }}" placeholder="1">
                        </div>
                        <div>
                            <label class="form-label">Đến</label>
                            <input class="form-control" type="number" min="1" max="9999" name="badge_range_end" value="{{ old('form_context') === 'create_badge' ? old('badge_range_end') : '' }}" placeholder="100">
                        </div>
                    </div>
                    <div class="badge-example">Ví dụ: nhập từ <strong>1</strong> đến <strong>100</strong> sẽ tạo Visitor card 1 → Visitor card 100.</div>
                    @error('badge_range_start')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="badge-mode-panel" data-badge-panel="list">
                    <div>
                        <label class="form-label">Mã thẻ <span class="text-danger">*</span></label>
                        <input class="form-control @error('badge_no') is-invalid @enderror" name="badge_no" value="{{ old('form_context') === 'create_badge' ? old('badge_no') : '' }}" placeholder="Ví dụ: B-001" maxlength="40">
                        @error('badge_no')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Tên hiển thị tiếng Việt <span class="text-danger">*</span></label>
                            <input class="form-control @error('label_vi') is-invalid @enderror" name="label_vi" value="{{ old('label_vi') }}" placeholder="Ví dụ: Thẻ khách đặc biệt" maxlength="120">
                            @error('label_vi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tên hiển thị English <span class="text-danger">*</span></label>
                            <input class="form-control @error('label_en') is-invalid @enderror" name="label_en" value="{{ old('label_en') }}" placeholder="Example: Special visitor card" maxlength="120">
                            @error('label_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="badge-help">Hai tên này sẽ tự đổi theo ngôn ngữ đang chọn trên kiosk.</div>
                </div>

                <input type="hidden" name="status" value="available">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                <button class="btn badge-add-btn" type="submit"><i class="bi bi-plus-circle"></i> Thêm thẻ</button>
            </div>
        </form>
    </div>
</div>

@if (old('form_context') === 'create_badge')
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modalElement = document.getElementById('createBadgeModal');
            if (modalElement && window.bootstrap) {
                window.setTimeout(() => window.openBadgeModal?.(), 0);
            }
        });
    </script>
    @endpush
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const modalElement = document.getElementById('createBadgeModal');
    const createTrigger = document.querySelector('[data-create-badge-trigger]');

    if (!modalElement) return;

    let badgeBackdrop = null;

    const closeBadgeModal = () => {
        modalElement.classList.remove('show');
        modalElement.style.display = 'none';
        modalElement.setAttribute('aria-hidden', 'true');
        modalElement.removeAttribute('aria-modal');
        badgeBackdrop?.remove();
        badgeBackdrop = null;
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('overflow');
        createTrigger?.focus();
    };

    window.openBadgeModal = () => {
        document.querySelectorAll('.modal-backdrop').forEach((backdrop) => backdrop.remove());
        badgeBackdrop?.remove();

        badgeBackdrop = document.createElement('div');
        badgeBackdrop.className = 'modal-backdrop fade show';
        badgeBackdrop.addEventListener('click', closeBadgeModal);
        document.body.appendChild(badgeBackdrop);

        document.body.classList.add('modal-open');
        document.body.style.overflow = 'hidden';
        modalElement.style.display = 'block';
        modalElement.classList.add('show');
        modalElement.setAttribute('aria-hidden', 'false');
        modalElement.setAttribute('aria-modal', 'true');
        window.setTimeout(() => modalElement.querySelector('input:not([type="hidden"])')?.focus(), 0);
    };

    modalElement.querySelectorAll('[data-bs-dismiss="modal"]').forEach((button) => {
        button.removeAttribute('data-bs-dismiss');
        button.addEventListener('click', closeBadgeModal);
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && modalElement.classList.contains('show')) closeBadgeModal();
    });

    const modeButtons = modalElement.querySelectorAll('[data-badge-mode]');
    const panels = modalElement.querySelectorAll('[data-badge-panel]');

    const activateMode = (mode) => {
        modeButtons.forEach((item) => item.classList.toggle('active', item.dataset.badgeMode === mode));
        panels.forEach((panel) => panel.classList.toggle('active', panel.dataset.badgePanel === mode));
    };

    modeButtons.forEach((button) => {
        button.addEventListener('click', () => {
            activateMode(button.dataset.badgeMode);
        });
    });

    if (@json(old('form_context')) === 'create_badge' && @json((bool) old('badge_no'))) {
        activateMode('list');
    }
});

document.querySelectorAll('.badge-inline-form').forEach((form) => {
    const editButton = form.querySelector('.badge-edit-toggle');
    editButton?.addEventListener('click', () => {
        form.classList.add('is-editing');
        form.querySelector('.badge-edit-control:not([type="hidden"])')?.focus();
    });
});
</script>
@endpush
@endsection
