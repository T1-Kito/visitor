@extends('layouts.admin')

@section('title', 'Sua nhan vien | Visitor Management')
@section('page_title', 'Sua nhan vien')
@section('page_subtitle', 'Cap nhat ho so host')

@section('content')
    <form class="row g-3" method="post" action="{{ route('admin.employees.update', $employee) }}">
        @csrf
        @method('PUT')
        <div class="col-xl-8">
            <section class="panel-card">
                <div class="panel-header">
                    <div><h3>Thong tin nhan vien</h3><p>Dung de chon nguoi tiep khach va loc bao cao.</p></div>
                </div>
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label">Ho ten</label><input class="form-control" name="name" value="{{ old('name', $employee->name) }}" required></div>
                    <div class="col-md-6"><label class="form-label">Email</label><input class="form-control" type="email" name="email" value="{{ old('email', $employee->email) }}"></div>
                    <div class="col-md-6"><label class="form-label">So dien thoai</label><input class="form-control" name="phone" value="{{ old('phone', $employee->phone) }}"></div>
                    <div class="col-md-6"><label class="form-label">Chuc danh</label><input class="form-control" name="job_title" value="{{ old('job_title', $employee->job_title) }}"></div>
                    <div class="col-md-8">
                        <label class="form-label">Phong ban</label>
                        <select class="form-select" name="department_id" required>
                            @foreach ($departments as $department)
                                <option value="{{ $department->id }}" @selected((string) old('department_id', $employee->department_id) === (string) $department->id)>{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <div class="form-check form-switch">
                            <input type="hidden" name="is_active" value="0">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive" @checked(old('is_active', $employee->is_active))>
                            <label class="form-check-label" for="isActive">Dang hoat dong</label>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <div class="col-xl-4">
            <section class="panel-card sticky-xl-top top-space">
                <div class="d-grid gap-2">
                    <button class="btn btn-brand btn-lg" type="submit">Luu thay doi</button>
                    <a class="btn btn-light" href="{{ route('admin.employees.show', $employee) }}">Quay lai chi tiet</a>
                </div>
            </section>
        </div>
    </form>
@endsection
