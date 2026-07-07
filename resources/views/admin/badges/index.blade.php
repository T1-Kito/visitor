@extends('layouts.admin')

@section('title', 'Số thẻ khách')
@section('page_title', 'Quản lý số thẻ khách')
@section('page_subtitle', 'Thêm, sửa, xóa danh sách thẻ visitor hiển thị trên kiosk')

@push('styles')
<style>
.badge-admin{display:grid;gap:1rem}
.badge-toolbar{display:flex;justify-content:flex-start;align-items:center;padding:0 1rem}
.badge-table-card{border:1px solid #e4edf8;border-radius:16px;background:#fff;overflow:hidden}
.badge-actions{display:flex;gap:.4rem;justify-content:flex-end;align-items:center}
.badge-actions form{margin:0}
.badge-inline-form{display:flex;gap:.45rem;align-items:center}
.badge-inline-form input{max-width:150px}
.badge-inline-form .form-control,.badge-inline-form .form-select{min-height:34px;padding:.28rem .55rem;border-radius:10px;font-size:.84rem}
.badge-actions .btn,.badge-compact-btn{min-height:34px;padding:.28rem .6rem;border-radius:10px;font-size:.84rem;font-weight:600}
.badge-add-btn{min-height:38px;padding:.42rem .8rem;border:1px solid #f2b8bf;background:#fff7f7;color:#d40511;border-radius:12px;font-weight:700}
.badge-add-btn:hover,.badge-add-btn:focus{border-color:#d40511;background:#fff7f7;color:#d40511}
.badge-save-btn{border-color:#d8e5f2;background:#fff;color:#334b67}
.badge-save-btn:hover,.badge-save-btn:focus{border-color:#b8cbe0;background:#f8fafc;color:#334b67}
.badge-note{color:#7187a3;font-size:.78rem}
.badge-status-select{min-width:135px}
.badge-modal .modal-dialog{max-width:560px}
.badge-modal .modal-content{border:0;border-radius:22px;box-shadow:0 28px 80px rgba(15,23,42,.28)}
.badge-modal .modal-header{padding:1.25rem 1.35rem .9rem;border-bottom:1px solid #eef3f8}
.badge-modal .modal-title{font-size:1.25rem;font-weight:800}
.badge-modal .modal-body{padding:1.05rem 1.35rem;display:grid;gap:.9rem}
.badge-modal .modal-footer{padding:.9rem 1.35rem 1.15rem;border-top:1px solid #eef3f8;gap:.55rem}
.badge-modal label{font-size:.84rem;font-weight:700;color:#334155;margin-bottom:.35rem}
.badge-modal .form-control,.badge-modal .form-select{min-height:42px;border-radius:13px;font-size:.95rem}
.badge-mode-tabs{display:grid;grid-template-columns:1fr 1fr;gap:.5rem;padding:.28rem;border:1px solid #e6edf5;border-radius:15px;background:#f8fafc}
.badge-mode-tabs button{border:0;border-radius:12px;background:transparent;color:#475569;font-weight:700;padding:.55rem .7rem}
.badge-mode-tabs button.active{background:#fff;color:#d40511;box-shadow:0 6px 18px rgba(15,23,42,.08)}
.badge-mode-panel{display:none}
.badge-mode-panel.active{display:grid;gap:.75rem}
.badge-range-grid{display:grid;grid-template-columns:1fr 100px 100px;gap:.65rem}
.badge-help{color:#64748b;font-size:.8rem;line-height:1.35;margin-top:.25rem}
.badge-example{padding:.65rem .75rem;border-radius:13px;background:#fff9e6;border:1px dashed #ffcc00;color:#654400;font-size:.82rem}
.badge-modal .btn-light{border-radius:12px;padding:.45rem .9rem}
@media(max-width:900px){.badge-inline-form{flex-wrap:wrap}.badge-actions{justify-content:flex-start}}
@media(max-width:640px){.badge-range-grid,.badge-mode-tabs{grid-template-columns:1fr}.badge-modal .modal-dialog{margin:.75rem}}
</style>
@endpush

@section('content')
<div class="badge-admin">
    <div class="badge-toolbar">
        <button class="btn badge-add-btn" type="button" data-bs-toggle="modal" data-bs-target="#createBadgeModal" data-create-badge-trigger><i class="bi bi-plus-circle"></i> Thêm thẻ</button>
    </div>

    <section class="badge-table-card">
        <div class="table-responsive">
            <table class="table modern-table align-middle mb-0">
                <thead>
                <tr>
                    <th>Số thẻ</th>
                    <th>Trạng thái</th>
                    <th class="text-end">Thao tác</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($badges as $badge)
                    <tr>
                        <td><strong>{{ $badge->badge_no }}</strong></td>
                        <td>
                            @if ($badge->status === 'active')
                                <span class="status-badge status-checked-in">Đang sử dụng</span>
                            @elseif ($badge->status === 'revoked')
                                <span class="status-badge status-checked-out">Tạm khóa/thu hồi</span>
                            @else
                                <span class="status-badge status-approved">Sẵn sàng cấp</span>
                            @endif
                        </td>
                        <td>
                            <div class="badge-actions">
                                <form class="badge-inline-form" method="post" action="{{ route('admin.badges.update', $badge) }}">
                                    @csrf
                                    @method('put')
                                    <input class="form-control form-control-sm" name="badge_no" value="{{ $badge->badge_no }}" required maxlength="40">
                                    <select class="form-select form-select-sm badge-status-select" name="status" @disabled($badge->status === 'active')>
                                        <option value="available" @selected($badge->status === 'available')>Sẵn sàng cấp</option>
                                        <option value="revoked" @selected($badge->status === 'revoked')>Tạm khóa</option>
                                    </select>
                                    @if ($badge->status === 'active')
                                        <input type="hidden" name="status" value="available">
                                    @endif
                                    <button class="btn btn-sm badge-save-btn" type="submit"><i class="bi bi-save"></i> Lưu</button>
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
                        <td colspan="3" class="text-center text-muted py-4">Chưa có số thẻ khách. Hãy thêm thẻ để kiosk hiển thị trong danh sách chọn.</td>
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
                    <button type="button" data-badge-mode="list"><i class="bi bi-list-ul"></i> Dán danh sách</button>
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
                        <label class="form-label">Một thẻ lẻ</label>
                        <input class="form-control @error('badge_no') is-invalid @enderror" name="badge_no" value="{{ old('form_context') === 'create_badge' ? old('badge_no') : '' }}" placeholder="Ví dụ: B-001" maxlength="40">
                        @error('badge_no')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label class="form-label">Danh sách thẻ</label>
                        <textarea class="form-control @error('badge_numbers') is-invalid @enderror" name="badge_numbers" rows="4" placeholder="Visitor card 1&#10;Visitor card 2&#10;Visitor card 3">{{ old('form_context') === 'create_badge' ? old('badge_numbers') : '' }}</textarea>
                        <div class="badge-help">Mỗi dòng một thẻ, hoặc dán danh sách cách nhau bằng dấu phẩy.</div>
                        @error('badge_numbers')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="form-label">Trạng thái</label>
                    <select class="form-select" name="status">
                        <option value="available" @selected(old('status', 'available') === 'available')>Sẵn sàng cấp</option>
                        <option value="revoked" @selected(old('status') === 'revoked')>Ngưng sử dụng</option>
                    </select>
                </div>
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
                bootstrap.Modal.getOrCreateInstance(modalElement).show();
            }
        });
    </script>
    @endpush
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const modalElement = document.getElementById('createBadgeModal');

    if (!modalElement) return;

    const modeButtons = modalElement.querySelectorAll('[data-badge-mode]');
    const panels = modalElement.querySelectorAll('[data-badge-panel]');

    modeButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const mode = button.dataset.badgeMode;
            modeButtons.forEach((item) => item.classList.toggle('active', item === button));
            panels.forEach((panel) => panel.classList.toggle('active', panel.dataset.badgePanel === mode));
        });
    });
});
</script>
@endpush
@endsection
