@extends('layouts.admin')

@section('title', 'Nhân viên | Quản lý khách')
@section('page_title', 'Quản lý nhân viên')
@section('page_subtitle', 'Danh sách nhân viên tiếp khách, phòng ban và trạng thái hoạt động')

@push('styles')
<style>
.directory-shell{display:grid;gap:1rem}.directory-hero{position:relative;overflow:hidden;border:1px solid #dce9f8;border-radius:26px;background:linear-gradient(135deg,#061525 0%,#0b2f55 48%,#0cb4d8 100%);box-shadow:0 18px 46px rgba(11,31,58,.16);color:#fff}.directory-hero:before{content:"";position:absolute;right:8%;bottom:-80px;width:360px;height:360px;border-radius:42px;background:linear-gradient(135deg,rgba(20,107,215,.9),rgba(16,185,229,.72));transform:rotate(45deg);opacity:.84}.directory-hero-content{position:relative;z-index:1;display:flex;justify-content:space-between;gap:1rem;padding:1.5rem}.directory-hero h3{margin:0;color:#fff;font-size:1.55rem;font-weight:900}.directory-hero p{max-width:650px;margin:.35rem 0 0;color:#cfe8ff;font-size:.88rem}.directory-actions{display:flex;gap:.65rem;align-items:flex-start}.directory-primary-btn{min-height:44px;border:0;border-radius:14px;color:#fff;font-weight:900;background:linear-gradient(135deg,#146bd7,#0cb4d8);box-shadow:0 14px 30px rgba(12,180,216,.28)}.directory-secondary-btn{min-height:44px;border:1px solid rgba(255,255,255,.28);border-radius:14px;color:#fff;background:rgba(255,255,255,.08);font-weight:900;text-decoration:none}.directory-stats{position:relative;z-index:1;display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:.75rem;padding:0 1.5rem 1.5rem}.directory-stat{padding:.85rem;border:1px solid rgba(255,255,255,.14);border-radius:18px;background:rgba(255,255,255,.1);backdrop-filter:blur(14px)}.directory-stat span{display:block;color:#b8d8f8;font-size:.72rem;font-weight:900}.directory-stat strong{display:block;margin:.12rem 0;color:#fff;font-size:1.35rem;font-weight:900}
.directory-card{background:#fff;border:1px solid #e3edf8;border-radius:24px;box-shadow:0 14px 36px rgba(17,39,68,.05);overflow:hidden}.directory-toolbar{display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:1rem 1.15rem;border-bottom:1px solid #edf3fb}.directory-filter{display:flex;gap:.65rem;align-items:center;flex:1}.directory-search{position:relative;flex:1;max-width:520px}.directory-search i{position:absolute;left:.9rem;top:50%;transform:translateY(-50%);color:#7a93b0}.directory-search .form-control{min-height:44px;padding-left:2.4rem;border-color:#d8e5f2;border-radius:14px}.directory-filter .form-select{max-width:210px;min-height:44px;border-color:#d8e5f2;border-radius:14px}.directory-table{width:100%;border-collapse:separate;border-spacing:0}.directory-table th{padding:.85rem 1rem;color:#6f88a4;font-size:.72rem;font-weight:900;text-transform:uppercase;border-bottom:1px solid #edf3fb;background:#fbfdff}.directory-table td{padding:.9rem 1rem;color:#29435f;font-size:.84rem;border-bottom:1px solid #edf3fb;vertical-align:middle}.directory-table tbody tr{transition:.15s}.directory-table tbody tr:hover{background:#f6fbff}.directory-person{display:flex;align-items:center;gap:.75rem}.directory-avatar{width:40px;height:40px;display:grid;place-items:center;border-radius:50%;background:#e0e7ff;color:#4f46e5;font-weight:900}.directory-name{display:block;color:#0b1f3a;font-weight:900;text-decoration:none}.directory-note{display:block;color:#7a93b0;font-size:.72rem}.directory-contact{display:inline-flex;align-items:center;gap:.4rem;color:#29435f}.directory-count{display:inline-flex;min-width:30px;height:28px;align-items:center;justify-content:center;border-radius:999px;background:#edf5ff;color:#146bd7;font-weight:900}.directory-actions-row{display:flex;justify-content:flex-end;gap:.45rem}.directory-icon-btn{width:34px;height:34px;display:grid;place-items:center;border:1px solid #d8e5f2;border-radius:11px;background:#fff;color:#146bd7;text-decoration:none}.directory-icon-btn:hover{background:#eff6ff}.directory-icon-btn.danger{color:#dc2626;border-color:#fecaca;background:#fff7f7}.directory-empty{padding:3rem;text-align:center;color:#7a93b0}.directory-footer{display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:1rem 1.15rem;color:#6f88a4;font-size:.82rem}.directory-pages{display:flex;gap:.4rem}.directory-pages span{width:36px;height:36px;display:grid;place-items:center;border:1px solid #d8e5f2;border-radius:11px;color:#29435f;font-weight:900}.directory-pages .active{border-color:#146bd7;background:#146bd7;color:#fff}
.directory-modal .modal-content{border:0;border-radius:24px;box-shadow:0 24px 70px rgba(11,31,58,.24)}.directory-modal .modal-header{padding:1.2rem 1.35rem;border-bottom:1px solid #edf3fb}.directory-modal .modal-title{font-weight:900;color:#0b1f3a}.directory-modal .modal-body{padding:1.25rem}.directory-form-grid{display:grid;grid-template-columns:1fr 1fr;gap:1rem}.directory-field label{margin-bottom:.35rem;color:#29435f;font-size:.76rem;font-weight:900}.directory-field label em{color:#e11d48;font-style:normal}.directory-field .form-control,.directory-field .form-select{min-height:46px;border-color:#d8e5f2;border-radius:13px}.directory-wide{grid-column:1/-1}.directory-modal .modal-footer{padding:1rem 1.35rem;border-top:1px solid #edf3fb}.directory-save-btn{min-height:44px;border:0;border-radius:13px;color:#fff;font-weight:900;background:linear-gradient(135deg,#146bd7,#0cb4d8)}
@media(max-width:992px){.directory-hero-content,.directory-toolbar{flex-direction:column;align-items:stretch}.directory-stats{grid-template-columns:1fr}.directory-filter{flex-direction:column;align-items:stretch}.directory-search,.directory-filter .form-select{max-width:none}.directory-table{min-width:860px}}@media(max-width:576px){.directory-form-grid{grid-template-columns:1fr}.directory-footer{flex-direction:column;align-items:stretch}}
</style>
@endpush

@section('content')
@php
    $employeeCollection = collect($employees);
    $totalEmployees = $employeeCollection->count();
    $activeEmployees = $employeeCollection->where('is_active', true)->count();
    $totalHostedVisits = $employeeCollection->sum('hosted_visits_count');
@endphp

<div class="directory-shell">
    <section class="directory-hero">
        <div class="directory-hero-content">
            <div>
                <h3>Danh sách nhân viên</h3>
                <p>Quản lý người tiếp khách, phòng ban phụ trách và trạng thái hoạt động của nhân viên trong hệ thống.</p>
            </div>
            <div class="directory-actions">
                <button class="btn directory-primary-btn" type="button" data-bs-toggle="modal" data-bs-target="#createEmployeeModal">
                    <i class="bi bi-person-plus"></i>
                    Tạo nhân viên
                </button>
                <a class="btn directory-secondary-btn" href="{{ route('admin.departments.index') }}">
                    <i class="bi bi-building"></i>
                    Phòng ban
                </a>
            </div>
        </div>
        <div class="directory-stats">
            <div class="directory-stat"><span>Tổng nhân viên</span><strong>{{ $totalEmployees }}</strong></div>
            <div class="directory-stat"><span>Đang hoạt động</span><strong>{{ $activeEmployees }}</strong></div>
            <div class="directory-stat"><span>Lượt tiếp khách</span><strong>{{ $totalHostedVisits }}</strong></div>
        </div>
    </section>

    <section class="directory-card">
        <div class="directory-toolbar">
            <form class="directory-filter" method="get" action="{{ route('admin.employees.index') }}">
                <div class="directory-search">
                    <i class="bi bi-search"></i>
                    <input class="form-control" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Tìm tên, email, số điện thoại, chức danh...">
                </div>
                <select class="form-select" name="department_id">
                    <option value="all">Tất cả phòng ban</option>
                    @foreach ($departments as $department)
                        <option value="{{ $department->id }}" @selected(($filters['department_id'] ?? 'all') === (string) $department->id)>
                            {{ $department->name }}
                        </option>
                    @endforeach
                </select>
                <button class="btn btn-light" type="submit"><i class="bi bi-funnel"></i> Lọc</button>
            </form>
        </div>

        <div class="table-responsive">
            <table class="directory-table">
                <thead>
                <tr>
                    <th>Nhân viên</th>
                    <th>Liên hệ</th>
                    <th>Phòng ban</th>
                    <th>Chức danh</th>
                    <th class="text-center">Số lịch</th>
                    <th>Trạng thái</th>
                    <th class="text-end">Thao tác</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($employees as $employee)
                    <tr>
                        <td>
                            <div class="directory-person">
                                <div class="directory-avatar">{{ strtoupper(mb_substr($employee->name, 0, 1)) }}</div>
                                <div>
                                    <a class="directory-name" href="{{ route('admin.employees.show', $employee) }}">{{ $employee->name }}</a>
                                    <span class="directory-note">{{ $employee->job_title ?: 'Chưa có chức danh' }}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="directory-contact"><i class="bi bi-envelope"></i>{{ $employee->email ?? '-' }}</span><br>
                            <span class="directory-contact"><i class="bi bi-telephone"></i>{{ $employee->phone ?? '-' }}</span>
                        </td>
                        <td><span class="directory-contact"><i class="bi bi-building"></i>{{ $employee->department?->name ?? '-' }}</span></td>
                        <td>{{ $employee->job_title ?? '-' }}</td>
                        <td class="text-center"><span class="directory-count">{{ $employee->hosted_visits_count }}</span></td>
                        <td>
                            <span class="status-badge {{ $employee->is_active ? 'status-approved' : 'status-checked-out' }}">
                                {{ $employee->is_active ? 'Đang hoạt động' : 'Ngừng hoạt động' }}
                            </span>
                        </td>
                        <td>
                            <div class="directory-actions-row">
                                <a class="directory-icon-btn" href="{{ route('admin.employees.show', $employee) }}" title="Xem chi tiết"><i class="bi bi-eye"></i></a>
                                <a class="directory-icon-btn" href="{{ route('admin.employees.edit', $employee) }}" title="Sửa nhân viên"><i class="bi bi-pencil"></i></a>
                                @if ($employee->hosted_visits_count === 0)
                                    <form method="post" action="{{ route('admin.employees.destroy', $employee) }}" onsubmit="return confirm('Xóa nhân viên này?')">
                                        @csrf
                                        @method('delete')
                                        <button class="directory-icon-btn danger" type="submit" title="Xóa nhân viên"><i class="bi bi-trash"></i></button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="directory-empty" colspan="7">
                            <i class="bi bi-people d-block fs-1 mb-2"></i>
                            Chưa có nhân viên phù hợp.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="directory-footer">
            <span>Hiển thị {{ $totalEmployees }} nhân viên</span>
            <div class="directory-pages"><span class="active">1</span><span><i class="bi bi-chevron-right"></i></span></div>
        </div>
    </section>
</div>

<div class="modal fade directory-modal" id="createEmployeeModal" tabindex="-1" aria-labelledby="createEmployeeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form class="modal-content" method="post" action="{{ route('admin.employees.store') }}">
            @csrf
            <div class="modal-header">
                <div>
                    <h5 class="modal-title" id="createEmployeeModalLabel">Tạo nhân viên</h5>
                    <div class="text-secondary small">Nhân viên có thể được chọn làm người tiếp khách.</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                <div class="directory-form-grid">
                    <div class="directory-field">
                        <label>Họ và tên <em>*</em></label>
                        <input class="form-control" name="name" value="{{ old('name') }}" placeholder="Ví dụ: Nguyễn Văn A" required>
                    </div>
                    <div class="directory-field">
                        <label>Email</label>
                        <input class="form-control" type="email" name="email" value="{{ old('email') }}" placeholder="name@company.com">
                    </div>
                    <div class="directory-field">
                        <label>Số điện thoại</label>
                        <input class="form-control" name="phone" value="{{ old('phone') }}" placeholder="0909 xxx xxx">
                    </div>
                    <div class="directory-field">
                        <label>Chức danh</label>
                        <input class="form-control" name="job_title" value="{{ old('job_title') }}" placeholder="Trưởng nhóm, Nhân viên...">
                    </div>
                    <div class="directory-field directory-wide">
                        <label>Phòng ban <em>*</em></label>
                        <select class="form-select" name="department_id" required>
                            <option value="">Chọn phòng ban</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->id }}" @selected((string) old('department_id') === (string) $department->id)>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                <button class="btn directory-save-btn" type="submit"><i class="bi bi-check2-circle"></i> Lưu nhân viên</button>
            </div>
        </form>
    </div>
</div>
@endsection

@if ($errors->any())
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            new bootstrap.Modal(document.getElementById('createEmployeeModal')).show();
        });
    </script>
    @endpush
@endif
