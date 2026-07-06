@extends('layouts.admin')

@section('title', 'Số thẻ khách')
@section('page_title', 'Quản lý số thẻ khách')
@section('page_subtitle', 'Thêm, sửa, xóa danh sách thẻ visitor hiển thị trên kiosk')

@push('styles')
<style>
.badge-admin{display:grid;gap:1rem}.badge-form{display:grid;grid-template-columns:minmax(220px,1fr) 180px auto;gap:.6rem;align-items:end;padding:.75rem 1rem;border:1px solid #e4edf8;border-radius:16px;background:#fff}.badge-form label{color:#526b87;font-size:.78rem;font-weight:600}.badge-form .form-control,.badge-form .form-select{min-height:42px;padding:.45rem .8rem;border-radius:12px;font-size:.92rem}.badge-table-card{border:1px solid #e4edf8;border-radius:16px;background:#fff;overflow:hidden}.badge-actions{display:flex;gap:.4rem;justify-content:flex-end;align-items:center}.badge-actions form{margin:0}.badge-inline-form{display:flex;gap:.45rem;align-items:center}.badge-inline-form input{max-width:150px}.badge-inline-form .form-control,.badge-inline-form .form-select{min-height:34px;padding:.28rem .55rem;border-radius:10px;font-size:.84rem}.badge-actions .btn,.badge-compact-btn{min-height:34px;padding:.28rem .6rem;border-radius:10px;font-size:.84rem;font-weight:600}.badge-add-btn{min-height:42px;padding:.45rem .85rem;border:1px solid #f2b8bf;background:#fff7f7;color:#d40511}.badge-add-btn:hover,.badge-add-btn:focus{border-color:#d40511;background:#fff7f7;color:#d40511}.badge-save-btn{border-color:#d8e5f2;background:#fff;color:#334b67}.badge-save-btn:hover,.badge-save-btn:focus{border-color:#b8cbe0;background:#f8fafc;color:#334b67}.badge-note{color:#7187a3;font-size:.78rem}.badge-status-select{min-width:135px}@media(max-width:900px){.badge-form{grid-template-columns:1fr}.badge-inline-form{flex-wrap:wrap}.badge-actions{justify-content:flex-start}}
</style>
@endpush

@section('content')
<div class="badge-admin">
    <form class="badge-form" method="post" action="{{ route('admin.badges.store') }}">
        @csrf
        <div>
            <label class="form-label">Số thẻ khách</label>
            <input class="form-control" name="badge_no" value="{{ old('badge_no') }}" placeholder="Ví dụ: Visitor card 1 hoặc B-001" required maxlength="40">
            @error('badge_no')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
        </div>
        <div>
            <label class="form-label">Trạng thái</label>
            <select class="form-select" name="status">
                <option value="available" @selected(old('status', 'available') === 'available')>Sẵn sàng cấp</option>
                <option value="revoked" @selected(old('status') === 'revoked')>Tạm khóa/thu hồi</option>
            </select>
        </div>
        <button class="btn badge-add-btn" type="submit"><i class="bi bi-plus-circle"></i> Thêm thẻ</button>
    </form>

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
@endsection