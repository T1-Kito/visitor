@extends('layouts.admin')

@section('title', 'Chi tiết danh sách cảnh báo | Visitor Management')
@section('page_title', 'Cảnh báo '.$watchlist->keyword)
@section('page_subtitle', 'Thông tin điều kiện cảnh báo và các lịch hẹn trùng khớp')

@section('content')
    <section class="entity-detail">
        <header class="entity-detail-head">
            <div class="entity-detail-identity">
                <div class="entity-detail-avatar"><i class="bi bi-shield-exclamation"></i></div>
                <div>
                    <h2 class="entity-detail-title">{{ $watchlist->keyword }}</h2>
                    <p class="entity-detail-subtitle">{{ $watchlist->reason ?: 'Chưa có lý do' }}</p>
                </div>
            </div>
            <div class="entity-detail-actions">
                <span class="status-badge {{ $watchlist->level === 'critical' ? 'status-rejected' : 'status-pending' }}">
                    {{ $watchlist->level === 'critical' ? 'Nghiêm trọng' : 'Cảnh báo' }}
                </span>
                <a class="btn btn-light" href="{{ route('admin.watchlists.index') }}"><i class="bi bi-arrow-left"></i>Quay lại</a>
                <a class="btn btn-brand" href="{{ route('admin.watchlists.edit', $watchlist) }}"><i class="bi bi-pencil"></i>Sửa</a>
            </div>
        </header>

        <div class="entity-detail-fields">
            <div class="entity-detail-field"><span>Kiểu đối chiếu</span><strong>{{ $watchlist->match_type }}</strong></div>
            <div class="entity-detail-field"><span>Trạng thái</span><strong>{{ $watchlist->status }}</strong></div>
            <div class="entity-detail-field"><span>Khách liên quan</span><strong>{{ $watchlist->visitor?->full_name ?? '-' }}</strong></div>
            <div class="entity-detail-field"><span>Người tạo</span><strong>{{ $watchlist->creator?->email ?? '-' }}</strong></div>
        </div>

        @if ($watchlist->note)
            <div class="entity-detail-note"><span>Ghi chú</span>{{ $watchlist->note }}</div>
        @endif

        <div class="entity-detail-section-head">
            <div><h3>Lịch hẹn trùng cảnh báo</h3><p>Các kết quả đối chiếu gần nhất.</p></div>
            <span class="entity-detail-count">{{ $matches->count() }} kết quả</span>
        </div>
        <div class="table-responsive">
            <table class="entity-detail-table">
                <thead><tr><th>Mã lịch</th><th>Khách</th><th>Người tiếp</th><th>Giờ hẹn</th><th>Trạng thái</th></tr></thead>
                <tbody>
                @forelse ($matches as $visit)
                    <tr>
                        <td><a class="entity-detail-link" href="{{ route('admin.visits.show', $visit) }}">{{ $visit->code }}</a></td>
                        <td>{{ $visit->visitor?->full_name ?? '-' }}</td>
                        <td>{{ $visit->hostEmployee?->name ?? '-' }}</td>
                        <td>{{ $visit->scheduled_at?->format('H:i d/m/Y') ?? '-' }}</td>
                        <td><x-status-badge :status="$visit->status" /></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-secondary py-4">Chưa có lịch nào trùng điều kiện cảnh báo.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
