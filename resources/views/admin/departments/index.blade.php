@extends('layouts.admin')

@section('title', 'Phòng ban | Quản lý khách')
@section('page_title', 'Quản lý phòng ban')
@section('page_subtitle', 'Danh mục phòng ban dùng cho nhân viên, phê duyệt và báo cáo')

@push('styles')
<style>
.dept-shell{display:grid;gap:1rem}.dept-hero{position:relative;overflow:hidden;border:1px solid #dce9f8;border-radius:26px;background:linear-gradient(135deg,#061525 0%,#0b2f55 48%,#0cb4d8 100%);box-shadow:0 18px 46px rgba(11,31,58,.16);color:#fff}.dept-hero:before{content:"";position:absolute;right:9%;bottom:-82px;width:360px;height:360px;border-radius:42px;background:linear-gradient(135deg,rgba(20,107,215,.9),rgba(16,185,229,.72));transform:rotate(45deg);opacity:.84}.dept-hero-content{position:relative;z-index:1;display:flex;justify-content:space-between;gap:1rem;padding:1.5rem}.dept-hero h3{margin:0;color:#fff;font-size:1.55rem;font-weight:900}.dept-hero p{max-width:650px;margin:.35rem 0 0;color:#cfe8ff;font-size:.88rem}.dept-actions{display:flex;gap:.65rem;align-items:flex-start}.dept-primary-btn{min-height:44px;border:0;border-radius:14px;color:#fff;font-weight:900;background:linear-gradient(135deg,#146bd7,#0cb4d8);box-shadow:0 14px 30px rgba(12,180,216,.28)}.dept-secondary-btn{min-height:44px;border:1px solid rgba(255,255,255,.28);border-radius:14px;color:#fff;background:rgba(255,255,255,.08);font-weight:900;text-decoration:none}.dept-stats{position:relative;z-index:1;display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:.75rem;padding:0 1.5rem 1.5rem}.dept-stat{padding:.85rem;border:1px solid rgba(255,255,255,.14);border-radius:18px;background:rgba(255,255,255,.1);backdrop-filter:blur(14px)}.dept-stat span{display:block;color:#b8d8f8;font-size:.72rem;font-weight:900}.dept-stat strong{display:block;margin:.12rem 0;color:#fff;font-size:1.35rem;font-weight:900}
.dept-card{background:#fff;border:1px solid #e3edf8;border-radius:24px;box-shadow:0 14px 36px rgba(17,39,68,.05);overflow:hidden}.dept-toolbar{display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:1rem 1.15rem;border-bottom:1px solid #edf3fb}.dept-search{position:relative;flex:1;max-width:520px}.dept-search i{position:absolute;left:.9rem;top:50%;transform:translateY(-50%);color:#7a93b0}.dept-search .form-control{min-height:44px;padding-left:2.4rem;border-color:#d8e5f2;border-radius:14px}.dept-table{width:100%;border-collapse:separate;border-spacing:0}.dept-table th{padding:.85rem 1rem;color:#6f88a4;font-size:.72rem;font-weight:900;text-transform:uppercase;border-bottom:1px solid #edf3fb;background:#fbfdff}.dept-table td{padding:.95rem 1rem;color:#29435f;font-size:.84rem;border-bottom:1px solid #edf3fb;vertical-align:middle}.dept-table tbody tr{transition:.15s}.dept-table tbody tr:hover{background:#f6fbff}.dept-main{display:flex;align-items:center;gap:.8rem}.dept-icon{width:42px;height:42px;display:grid;place-items:center;border-radius:15px;background:#eaf3ff;color:#146bd7;font-size:1.1rem}.dept-code{display:inline-flex;padding:.28rem .6rem;border-radius:999px;background:#edf5ff;color:#146bd7;font-weight:900;text-decoration:none}.dept-name{display:block;color:#0b1f3a;font-weight:900;text-decoration:none}.dept-note{display:block;color:#7a93b0;font-size:.72rem}.dept-count{display:inline-flex;min-width:34px;height:30px;align-items:center;justify-content:center;border-radius:999px;background:#ecfdf5;color:#059669;font-weight:900}.dept-actions-row{display:flex;justify-content:flex-end;gap:.45rem}.dept-icon-btn{width:34px;height:34px;display:grid;place-items:center;border:1px solid #d8e5f2;border-radius:11px;background:#fff;color:#146bd7;text-decoration:none}.dept-icon-btn:hover{background:#eff6ff}.dept-icon-btn.danger{color:#dc2626;border-color:#fecaca;background:#fff7f7}.dept-empty{padding:3rem;text-align:center;color:#7a93b0}.dept-footer{display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:1rem 1.15rem;color:#6f88a4;font-size:.82rem}.dept-pages{display:flex;gap:.4rem}.dept-pages span{width:36px;height:36px;display:grid;place-items:center;border:1px solid #d8e5f2;border-radius:11px;color:#29435f;font-weight:900}.dept-pages .active{border-color:#146bd7;background:#146bd7;color:#fff}
.dept-modal .modal-content{border:0;border-radius:24px;box-shadow:0 24px 70px rgba(11,31,58,.24)}.dept-modal .modal-header{padding:1.2rem 1.35rem;border-bottom:1px solid #edf3fb}.dept-modal .modal-title{font-weight:900;color:#0b1f3a}.dept-modal .modal-body{padding:1.25rem}.dept-form-grid{display:grid;grid-template-columns:1fr 1.5fr;gap:1rem}.dept-field label{margin-bottom:.35rem;color:#29435f;font-size:.76rem;font-weight:900}.dept-field label em{color:#e11d48;font-style:normal}.dept-field .form-control{min-height:46px;border-color:#d8e5f2;border-radius:13px;text-transform:none}.dept-modal .modal-footer{padding:1rem 1.35rem;border-top:1px solid #edf3fb}.dept-save-btn{min-height:44px;border:0;border-radius:13px;color:#fff;font-weight:900;background:linear-gradient(135deg,#146bd7,#0cb4d8)}
@media(max-width:992px){.dept-hero-content,.dept-toolbar{flex-direction:column;align-items:stretch}.dept-stats,.dept-form-grid{grid-template-columns:1fr}.dept-search{max-width:none}.dept-table{min-width:700px}}@media(max-width:576px){.dept-footer{flex-direction:column;align-items:stretch}}
</style>
@endpush

@section('content')
@php
    $departmentCollection = collect($departments);
    $totalDepartments = $departmentCollection->count();
    $totalEmployees = $departmentCollection->sum('employees_count');
    $largestDepartment = $departmentCollection->sortByDesc('employees_count')->first();
@endphp

<div class="dept-shell">
    <section class="dept-hero">
        <div class="dept-hero-content">
            <div>
                <h3>Danh sách phòng ban</h3>
                <p>Quản lý cấu trúc phòng ban để phân quyền xem dữ liệu, chọn người tiếp khách và tổng hợp báo cáo.</p>
            </div>
            <div class="dept-actions">
                <button class="btn dept-primary-btn" type="button" data-bs-toggle="modal" data-bs-target="#createDepartmentModal">
                    <i class="bi bi-building-add"></i>
                    Tạo phòng ban
                </button>
                <a class="btn dept-secondary-btn" href="{{ route('admin.employees.index') }}">
                    <i class="bi bi-people"></i>
                    Nhân viên
                </a>
            </div>
        </div>
        <div class="dept-stats">
            <div class="dept-stat"><span>Tổng phòng ban</span><strong>{{ $totalDepartments }}</strong></div>
            <div class="dept-stat"><span>Tổng nhân viên</span><strong>{{ $totalEmployees }}</strong></div>
            <div class="dept-stat"><span>Phòng ban lớn nhất</span><strong>{{ $largestDepartment?->employees_count ?? 0 }}</strong></div>
        </div>
    </section>

    <section class="dept-card">
        <div class="dept-toolbar">
            <form class="dept-search" method="get" action="{{ route('admin.departments.index') }}">
                <i class="bi bi-search"></i>
                <input class="form-control" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Tìm mã hoặc tên phòng ban...">
            </form>
            <button class="btn btn-light" type="button" onclick="this.closest('.dept-toolbar').querySelector('.dept-search').submit()">
                <i class="bi bi-funnel"></i>
                Lọc
            </button>
        </div>

        <div class="table-responsive">
            <table class="dept-table">
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
                        <td><a class="dept-code" href="{{ route('admin.departments.show', $department) }}">{{ $department->code }}</a></td>
                        <td>
                            <div class="dept-main">
                                <div class="dept-icon"><i class="bi bi-building"></i></div>
                                <div>
                                    <a class="dept-name" href="{{ route('admin.departments.show', $department) }}">{{ $department->name }}</a>
                                    <span class="dept-note">Dùng cho nhân viên và báo cáo phòng ban</span>
                                </div>
                            </div>
                        </td>
                        <td class="text-center"><span class="dept-count">{{ $department->employees_count }}</span></td>
                        <td>
                            <div class="dept-actions-row">
                                <a class="dept-icon-btn" href="{{ route('admin.departments.show', $department) }}" title="Xem chi tiết"><i class="bi bi-eye"></i></a>
                                <a class="dept-icon-btn" href="{{ route('admin.departments.edit', $department) }}" title="Sửa phòng ban"><i class="bi bi-pencil"></i></a>
                                @if ($department->employees_count === 0)
                                    <form method="post" action="{{ route('admin.departments.destroy', $department) }}" onsubmit="return confirm('Xóa phòng ban này?')">
                                        @csrf
                                        @method('delete')
                                        <button class="dept-icon-btn danger" type="submit" title="Xóa phòng ban"><i class="bi bi-trash"></i></button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="dept-empty" colspan="4">
                            <i class="bi bi-building d-block fs-1 mb-2"></i>
                            Chưa có phòng ban phù hợp.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="dept-footer">
            <span>Hiển thị {{ $totalDepartments }} phòng ban</span>
            <div class="dept-pages"><span class="active">1</span><span><i class="bi bi-chevron-right"></i></span></div>
        </div>
    </section>
</div>

<div class="modal fade dept-modal" id="createDepartmentModal" tabindex="-1" aria-labelledby="createDepartmentModalLabel" aria-hidden="true">
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
                <div class="dept-form-grid">
                    <div class="dept-field">
                        <label>Mã phòng ban <em>*</em></label>
                        <input class="form-control" name="code" value="{{ old('code') }}" placeholder="Ví dụ: HR" required>
                    </div>
                    <div class="dept-field">
                        <label>Tên phòng ban <em>*</em></label>
                        <input class="form-control" name="name" value="{{ old('name') }}" placeholder="Nhân sự" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                <button class="btn dept-save-btn" type="submit"><i class="bi bi-check2-circle"></i> Lưu phòng ban</button>
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
