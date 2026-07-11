@extends('layouts.admin')

@section('title', 'Sua phong ban | Visitor Management')
@section('page_title', 'Sửa phòng ban '.$department->code)
@section('page_subtitle', 'Cập nhật tên phòng ban, mã sẽ được tự đồng bộ')

@section('content')
    <form class="row g-3" method="post" action="{{ route('admin.departments.update', $department) }}">
        @csrf
        @method('PUT')
        <div class="col-xl-8">
            <section class="panel-card">
                <div class="panel-header">
                    <div>
                        <h3>Thong tin phong ban</h3>
                        <p>Mã phòng ban được hệ thống tự sinh từ tên.</p>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Mã phòng ban</label>
                        <div class="form-control bg-light">{{ $department->code }}</div>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Tên phòng ban</label>
                        <input class="form-control" name="name_vi" value="{{ old('name_vi', $department->name_vi ?: $department->name) }}" required>
                    </div>
                    <div>
                        <label class="form-label">Tên phòng ban (English)</label>
                        <input class="form-control" name="name_en" value="{{ old('name_en', $department->name_en ?: $department->name) }}" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Phòng ban cha</label>
                        <select class="form-select" name="parent_id">
                            <option value="">Không có - phòng ban cấp 1</option>
                            @foreach (($departmentOptions ?? collect()) as $option)
                                @continue((int) $option->id === (int) $department->id)
                                <option value="{{ $option->id }}" @selected((string) old('parent_id', $department->parent_id) === (string) $option->id)>
                                    {{ $option->name }} ({{ $option->code }})
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">Chọn phòng ban cha nếu muốn đưa phòng ban này vào cây tổ chức.</div>
                    </div>
                </div>
            </section>
        </div>
        <div class="col-xl-4">
            <section class="panel-card sticky-xl-top top-space">
                <div class="d-grid gap-2">
                    <button class="btn btn-brand btn-lg" type="submit">Luu thay doi</button>
                    <a class="btn btn-light" href="{{ route('admin.departments.show', $department) }}">Quay lai chi tiet</a>
                </div>
            </section>
        </div>
    </form>
@endsection
