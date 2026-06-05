@extends('layouts.mobile')

@section('title', 'Chi tiết lịch')

@section('content')
    @php
        $statusLabels = [
            'pending' => 'Chờ duyệt',
            'approved' => 'Đã duyệt',
            'checked_in' => 'Đang trong công ty',
            'checked_out' => 'Đã ra',
            'rejected' => 'Từ chối',
            'cancelled' => 'Đã hủy',
        ];
    @endphp

    <section class="m-page-head">
        <a href="{{ url()->previous() }}"><i class="bi bi-chevron-left"></i></a>
        <div><h1>Chi tiết lịch</h1><p>{{ $visit->code }} · {{ $statusLabels[$visit->status] ?? $visit->status }}</p></div>
    </section>

    <section class="m-section">
        <div class="m-detail-card">
            <div class="m-person-row">
                <span class="m-avatar">{{ mb_substr($visit->visitor?->full_name ?? '-', 0, 1) }}</span>
                <div>
                    <strong>{{ $visit->visitor?->full_name ?? '-' }}</strong>
                    <small>{{ $visit->visitor?->company ?? '-' }}</small>
                </div>
            </div>
            <div class="m-detail-lines">
                <div><span>Mã lịch</span><strong>{{ $visit->code }}</strong></div>
                <div><span>Số điện thoại</span><strong>{{ $visit->visitor?->phone ?? '-' }}</strong></div>
                <div><span>Email</span><strong>{{ $visit->visitor?->email ?? '-' }}</strong></div>
                <div><span>Người cần gặp</span><strong>{{ $visit->hostEmployee?->name ?? '-' }}</strong></div>
                <div><span>Phòng ban</span><strong>{{ $visit->hostEmployee?->department?->name ?? '-' }}</strong></div>
                <div><span>Giờ hẹn</span><strong>{{ $visit->scheduled_at?->format('H:i - d/m/Y') ?? '-' }}</strong></div>
                <div><span>Dự kiến ra</span><strong>{{ $visit->expected_checkout_at?->format('H:i - d/m/Y') ?? '-' }}</strong></div>
                <div><span>Mục đích</span><strong>{{ $visit->purpose }}</strong></div>
            </div>
        </div>
    </section>

    <section class="m-section">
        <div class="m-section-head">
            <h2>Lịch sử xử lý</h2>
            <span>{{ $activityLogs->count() }} dòng</span>
        </div>
        <div class="m-timeline">
            @forelse ($activityLogs as $log)
                <div>
                    <i class="bi bi-check2-circle"></i>
                    <span><strong>{{ $log->action }}</strong><small>{{ $log->created_at?->format('H:i - d/m/Y') }}</small></span>
                </div>
            @empty
                <div class="m-empty"><i class="bi bi-clock-history"></i><span>Chưa có lịch sử xử lý.</span></div>
            @endforelse
        </div>
    </section>
@endsection
