@extends('layouts.admin')

@section('title', 'Khách | Quản lý khách')
@section('page_title', 'Quản lý khách')
@section('page_subtitle', 'Theo dõi hồ sơ khách, thông tin liên hệ và lịch sử ra vào')

@push('styles')
<style>
.visitor-shell{display:grid;gap:1rem}.visitor-hero{position:relative;overflow:hidden;border:1px solid #dce9f8;border-radius:26px;background:linear-gradient(135deg,#061525 0%,#0b2f55 48%,#0cb4d8 100%);box-shadow:0 18px 46px rgba(11,31,58,.16);min-height:178px;color:#fff}.visitor-hero:before{content:"";position:absolute;inset:auto 8% -70px auto;width:360px;height:360px;border-radius:42px;background:linear-gradient(135deg,rgba(20,107,215,.9),rgba(16,185,229,.75));transform:rotate(45deg);opacity:.82}.visitor-hero:after{content:"";position:absolute;inset:-120px auto auto 48%;width:340px;height:340px;border-radius:50%;background:radial-gradient(circle,rgba(255,255,255,.2),transparent 65%)}.visitor-hero-content{position:relative;z-index:1;display:flex;justify-content:space-between;gap:1rem;padding:1.5rem}.visitor-hero h3{margin:0;color:#fff;font-size:1.55rem;font-weight:900}.visitor-hero p{max-width:620px;margin:.35rem 0 0;color:#cfe8ff;font-size:.88rem}.visitor-actions{display:flex;gap:.65rem;align-items:flex-start}.visitor-add-btn{min-height:44px;border:0;border-radius:14px;color:#fff;font-weight:900;background:linear-gradient(135deg,#146bd7,#0cb4d8);box-shadow:0 14px 30px rgba(12,180,216,.28)}.visitor-export-btn{min-height:44px;border:1px solid rgba(255,255,255,.28);border-radius:14px;color:#fff;background:rgba(255,255,255,.08);font-weight:900;text-decoration:none}.visitor-stats{position:relative;z-index:1;display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:.75rem;padding:0 1.5rem 1.5rem}.visitor-stat{padding:.85rem;border:1px solid rgba(255,255,255,.14);border-radius:18px;background:rgba(255,255,255,.1);backdrop-filter:blur(14px)}.visitor-stat span{display:block;color:#b8d8f8;font-size:.72rem;font-weight:900}.visitor-stat strong{display:block;margin:.12rem 0;color:#fff;font-size:1.35rem;font-weight:900}
.visitor-card{background:#fff;border:1px solid #e3edf8;border-radius:24px;box-shadow:0 14px 36px rgba(17,39,68,.05);overflow:hidden}.visitor-toolbar{display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:1rem 1.15rem;border-bottom:1px solid #edf3fb}.visitor-search{position:relative;flex:1;max-width:520px}.visitor-search i{position:absolute;left:.9rem;top:50%;transform:translateY(-50%);color:#7a93b0}.visitor-search .form-control{min-height:44px;padding-left:2.4rem;border-color:#d8e5f2;border-radius:14px}.visitor-table{width:100%;border-collapse:separate;border-spacing:0}.visitor-table th{padding:.85rem 1rem;color:#6f88a4;font-size:.72rem;font-weight:900;text-transform:uppercase;border-bottom:1px solid #edf3fb;background:#fbfdff}.visitor-table td{padding:.9rem 1rem;color:#29435f;font-size:.84rem;border-bottom:1px solid #edf3fb;vertical-align:middle}.visitor-table tbody tr{transition:.15s}.visitor-table tbody tr:hover{background:#f6fbff}.visitor-person{display:flex;align-items:center;gap:.75rem}.visitor-avatar{width:38px;height:38px;display:grid;place-items:center;border-radius:50%;background:#e0e7ff;color:#4f46e5;font-weight:900}.visitor-name{display:block;color:#0b1f3a;font-weight:900;text-decoration:none}.visitor-note{display:block;color:#7a93b0;font-size:.72rem}.visitor-contact{display:inline-flex;align-items:center;gap:.4rem;color:#29435f}.visitor-count{display:inline-flex;min-width:30px;height:28px;align-items:center;justify-content:center;border-radius:999px;background:#edf5ff;color:#146bd7;font-weight:900}.visitor-row-actions{display:flex;justify-content:flex-end;gap:.45rem}.visitor-icon-btn{width:34px;height:34px;display:grid;place-items:center;border:1px solid #d8e5f2;border-radius:11px;background:#fff;color:#146bd7;text-decoration:none}.visitor-icon-btn:hover{background:#eff6ff}.visitor-icon-btn.danger{color:#dc2626;border-color:#fecaca;background:#fff7f7}.visitor-empty{padding:3rem;text-align:center;color:#7a93b0}.visitor-pagination{display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:1rem 1.15rem}.visitor-page-size{display:flex;align-items:center;gap:.5rem;color:#6f88a4;font-size:.8rem}.visitor-page-size .form-select{width:76px;border-radius:12px;border-color:#d8e5f2}.visitor-pages{display:flex;gap:.4rem}.visitor-pages span,.visitor-pages a{width:36px;height:36px;display:grid;place-items:center;border:1px solid #d8e5f2;border-radius:11px;color:#29435f;text-decoration:none;font-weight:900}.visitor-pages .active{border-color:#146bd7;background:#146bd7;color:#fff}.visitor-modal .modal-content{border:0;border-radius:24px;box-shadow:0 24px 70px rgba(11,31,58,.24)}.visitor-modal .modal-header{padding:1.2rem 1.35rem;border-bottom:1px solid #edf3fb}.visitor-modal .modal-title{font-weight:900;color:#0b1f3a}.visitor-modal .modal-body{padding:1.25rem}.visitor-form-grid{display:grid;grid-template-columns:1fr 1fr;gap:1rem}.visitor-field label{margin-bottom:.35rem;color:#29435f;font-size:.76rem;font-weight:900}.visitor-field label em{color:#e11d48;font-style:normal}.visitor-field .form-control{min-height:46px;border-color:#d8e5f2;border-radius:13px}.visitor-field textarea.form-control{min-height:86px}.visitor-wide{grid-column:1/-1}.visitor-modal .modal-footer{padding:1rem 1.35rem;border-top:1px solid #edf3fb}.visitor-save-btn{min-height:44px;border:0;border-radius:13px;color:#fff;font-weight:900;background:linear-gradient(135deg,#146bd7,#0cb4d8)}
@media(max-width:992px){.visitor-hero-content{flex-direction:column}.visitor-stats{grid-template-columns:1fr}.visitor-toolbar{align-items:stretch;flex-direction:column}.visitor-search{max-width:none}.visitor-table{min-width:780px}}@media(max-width:576px){.visitor-form-grid{grid-template-columns:1fr}.visitor-pagination{align-items:stretch;flex-direction:column}}
</style>
@endpush

@section('content')
@php
    $visitorCollection = collect($visitors);
    $totalVisitors = $visitorCollection->count();
    $totalVisits = $visitorCollection->sum('visits_count');
    $withCompany = $visitorCollection->filter(fn ($visitor) => filled($visitor->company))->count();
@endphp

<div class="visitor-shell">
    <section class="visitor-hero">
        <div class="visitor-hero-content">
            <div>
                <h3>Danh sách khách</h3>
                <p>Quản lý hồ sơ khách đã từng liên hệ hoặc ra vào công ty. Admin có thể tạo nhanh hồ sơ khách mới bằng modal ngay trên trang.</p>
            </div>
            <div class="visitor-actions">
                <button class="btn visitor-add-btn" type="button" data-bs-toggle="modal" data-bs-target="#createVisitorModal">
                    <i class="bi bi-plus-circle"></i>
                    Tạo khách
                </button>
                <a class="btn visitor-export-btn" href="{{ route('admin.visits.create') }}">
                    <i class="bi bi-calendar-plus"></i>
                    Tạo lịch hẹn
                </a>
            </div>
        </div>
        <div class="visitor-stats">
            <div class="visitor-stat"><span>Tổng hồ sơ</span><strong>{{ $totalVisitors }}</strong></div>
            <div class="visitor-stat"><span>Tổng lượt ghé</span><strong>{{ $totalVisits }}</strong></div>
            <div class="visitor-stat"><span>Có thông tin công ty</span><strong>{{ $withCompany }}</strong></div>
        </div>
    </section>

    <section class="visitor-card">
        <div class="visitor-toolbar">
            <form class="visitor-search" method="get" action="{{ route('admin.visitors.index') }}">
                <i class="bi bi-search"></i>
                <input class="form-control" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Tìm tên khách, số điện thoại, email, công ty...">
            </form>
            <button class="btn btn-light" type="button" onclick="this.closest('.visitor-toolbar').querySelector('.visitor-search').submit()">
                <i class="bi bi-funnel"></i>
                Lọc
            </button>
        </div>

        <div class="table-responsive">
            <table class="visitor-table">
                <thead>
                <tr>
                    <th>Khách</th>
                    <th>Số điện thoại</th>
                    <th>Email</th>
                    <th>Công ty</th>
                    <th class="text-center">Số lượt</th>
                    <th class="text-end">Thao tác</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($visitors as $visitor)
                    <tr>
                        <td>
                            <div class="visitor-person">
                                <div class="visitor-avatar">{{ strtoupper(mb_substr($visitor->full_name, 0, 1)) }}</div>
                                <div>
                                    <a class="visitor-name" href="{{ route('admin.visitors.show', $visitor) }}">{{ $visitor->full_name }}</a>
                                    <span class="visitor-note">{{ $visitor->note ?: 'Chưa có ghi chú' }}</span>
                                </div>
                            </div>
                        </td>
                        <td><span class="visitor-contact"><i class="bi bi-telephone"></i>{{ $visitor->phone ?? '-' }}</span></td>
                        <td>{{ $visitor->email ?? '-' }}</td>
                        <td><span class="visitor-contact"><i class="bi bi-building"></i>{{ $visitor->company ?? '-' }}</span></td>
                        <td class="text-center"><span class="visitor-count">{{ $visitor->visits_count }}</span></td>
                        <td>
                            <div class="visitor-row-actions">
                                <a class="visitor-icon-btn" href="{{ route('admin.visitors.show', $visitor) }}" title="Xem chi tiết"><i class="bi bi-eye"></i></a>
                                <a class="visitor-icon-btn" href="{{ route('admin.visitors.edit', $visitor) }}" title="Sửa hồ sơ"><i class="bi bi-pencil"></i></a>
                                @if ($visitor->visits_count === 0)
                                    <form method="post" action="{{ route('admin.visitors.destroy', $visitor) }}" onsubmit="return confirm('Xóa hồ sơ khách này?')">
                                        @csrf
                                        @method('delete')
                                        <button class="visitor-icon-btn danger" type="submit" title="Xóa hồ sơ"><i class="bi bi-trash"></i></button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="visitor-empty" colspan="6">
                            <i class="bi bi-person-lines-fill d-block fs-1 mb-2"></i>
                            Chưa có hồ sơ khách phù hợp.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="visitor-pagination">
            <div class="visitor-page-size">
                <span>Hiển thị</span>
                <select class="form-select form-select-sm" disabled>
                    <option>{{ min(10, max(1, $totalVisitors)) }}</option>
                </select>
                <span>1 - {{ min(10, $totalVisitors) }} của {{ $totalVisitors }}</span>
            </div>
            <div class="visitor-pages">
                <span class="active">1</span>
                @if ($totalVisitors > 10)<span>2</span>@endif
                @if ($totalVisitors > 20)<span>3</span>@endif
                <span><i class="bi bi-chevron-right"></i></span>
            </div>
        </div>
    </section>
</div>

<div class="modal fade visitor-modal" id="createVisitorModal" tabindex="-1" aria-labelledby="createVisitorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form class="modal-content" method="post" action="{{ route('admin.visitors.store') }}">
            @csrf
            <div class="modal-header">
                <div>
                    <h5 class="modal-title" id="createVisitorModalLabel">Tạo hồ sơ khách</h5>
                    <div class="text-secondary small">Nhập thông tin khách để dùng lại khi tạo lịch hẹn.</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                <div class="visitor-form-grid">
                    <div class="visitor-field">
                        <label>Họ và tên <em>*</em></label>
                        <input class="form-control" name="full_name" value="{{ old('full_name') }}" placeholder="Ví dụ: Nguyễn Văn A" required>
                    </div>
                    <div class="visitor-field">
                        <label>Số điện thoại</label>
                        <input class="form-control" name="phone" value="{{ old('phone') }}" placeholder="0909 xxx xxx">
                    </div>
                    <div class="visitor-field">
                        <label>Email</label>
                        <input class="form-control" type="email" name="email" value="{{ old('email') }}" placeholder="visitor@company.com">
                    </div>
                    <div class="visitor-field">
                        <label>Công ty / tổ chức</label>
                        <input class="form-control" name="company" value="{{ old('company') }}" placeholder="Tên công ty">
                    </div>
                    <div class="visitor-field visitor-wide">
                        <label>Số giấy tờ</label>
                        <input class="form-control" name="identity_no" value="{{ old('identity_no') }}" placeholder="CCCD / hộ chiếu nếu cần">
                    </div>
                    <div class="visitor-field visitor-wide">
                        <label>Ghi chú</label>
                        <textarea class="form-control" name="note" rows="3" placeholder="Ghi chú thêm cho lễ tân / bảo vệ">{{ old('note') }}</textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                <button class="btn visitor-save-btn" type="submit">
                    <i class="bi bi-check2-circle"></i>
                    Lưu hồ sơ
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
            const modal = new bootstrap.Modal(document.getElementById('createVisitorModal'));
            modal.show();
        });
    </script>
    @endpush
@endif
