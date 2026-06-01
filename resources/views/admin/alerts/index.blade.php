@extends('layouts.admin')

@section('title', 'Canh bao | Visitor Management')
@section('page_title', 'Canh bao van hanh')
@section('page_subtitle', 'Theo doi qua gio, chua check-in va lich sap den gio')

@section('content')
    <section class="panel-card">
        <div class="panel-header">
            <div>
                <h3>Danh sach canh bao</h3>
                <p>{{ count($alerts) }} canh bao dang can theo doi.</p>
            </div>
            <a href="{{ route('admin.alerts.index') }}" class="btn btn-outline-dark btn-sm">
                <i class="bi bi-arrow-clockwise"></i>
                Lam moi
            </a>
        </div>

        <div class="d-grid gap-2">
            @forelse ($alerts as $alert)
                <div class="alert alert-{{ $alert['level'] === 'danger' ? 'danger' : 'warning' }} mb-0">
                    <div class="d-flex flex-wrap justify-content-between gap-2">
                        <strong>{{ $alert['title'] }}</strong>
                        <span>{{ $alert['time'] }}</span>
                    </div>
                    <div>{{ $alert['message'] }}</div>
                </div>
            @empty
                <div class="alert alert-success mb-0">
                    Khong co canh bao bat thuong trong ca truc hien tai.
                </div>
            @endforelse
        </div>
    </section>
@endsection
