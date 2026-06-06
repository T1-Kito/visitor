@extends('layouts.admin')

@section('title', 'Sua khach | Visitor Management')
@section('page_title', 'Sua ho so khach')
@section('page_subtitle', 'Cap nhat thong tin khach')

@section('content')
    <form class="row g-3" method="post" action="{{ route('admin.visitors.update', $visitor) }}">
        @csrf
        @method('PUT')
        <div class="col-xl-8">
            <section class="panel-card">
                <div class="panel-header"><div><h3>Thong tin khach</h3><p>Du lieu dung lai khi tao lich hen moi.</p></div></div>
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label">Ma khach</label><input class="form-control" value="{{ $visitor->visitor_code }}" readonly></div>
                    <div class="col-md-6"><label class="form-label">Ho ten</label><input class="form-control" name="full_name" value="{{ old('full_name', $visitor->full_name) }}" required></div>
                    <div class="col-md-6"><label class="form-label">So dien thoai</label><input class="form-control" name="phone" value="{{ old('phone', $visitor->phone) }}"></div>
                    <div class="col-md-6"><label class="form-label">Email</label><input class="form-control" type="email" name="email" value="{{ old('email', $visitor->email) }}"></div>
                    <div class="col-md-6"><label class="form-label">Cong ty</label><input class="form-control" name="company" value="{{ old('company', $visitor->company) }}"></div>
                    <div class="col-md-6"><label class="form-label">So giay to</label><input class="form-control" name="identity_no" value="{{ old('identity_no', $visitor->identity_no) }}"></div>
                    <div class="col-12"><label class="form-label">Ghi chu</label><textarea class="form-control" name="note" rows="3">{{ old('note', $visitor->note) }}</textarea></div>
                </div>
            </section>
        </div>
        <div class="col-xl-4">
            <section class="panel-card sticky-xl-top top-space">
                <div class="d-grid gap-2">
                    <button class="btn btn-brand btn-lg" type="submit">Luu thay doi</button>
                    <a class="btn btn-light" href="{{ route('admin.visitors.show', $visitor) }}">Quay lai chi tiet</a>
                </div>
            </section>
        </div>
    </form>
@endsection
