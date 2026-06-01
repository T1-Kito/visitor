@extends('layouts.admin')

@section('title', 'Chi tiet watchlist | Visitor Management')
@section('page_title', 'Watchlist '.$watchlist->keyword)
@section('page_subtitle', 'Thong tin rule va cac lich hen dang match')

@section('content')
    <div class="row g-3">
        <div class="col-xl-4">
            <section class="panel-card">
                <div class="panel-header">
                    <div>
                        <h3>{{ $watchlist->keyword }}</h3>
                        <p>{{ $watchlist->reason }}</p>
                    </div>
                    <span class="status-badge {{ $watchlist->level === 'critical' ? 'status-rejected' : 'status-pending' }}">
                        {{ $watchlist->level }}
                    </span>
                </div>
                <div class="detail-grid">
                    <div class="detail-item"><span>Match type</span><strong>{{ $watchlist->match_type }}</strong></div>
                    <div class="detail-item"><span>Status</span><strong>{{ $watchlist->status }}</strong></div>
                    <div class="detail-item"><span>Visitor</span><strong>{{ $watchlist->visitor?->full_name ?? '-' }}</strong></div>
                    <div class="detail-item"><span>Nguoi tao</span><strong>{{ $watchlist->creator?->email ?? '-' }}</strong></div>
                    <div class="detail-item detail-wide"><span>Ghi chu</span><strong>{{ $watchlist->note ?? '-' }}</strong></div>
                </div>
                <div class="d-grid gap-2 mt-3">
                    <a class="btn btn-brand" href="{{ route('admin.watchlists.edit', $watchlist) }}">Sua rule</a>
                    <a class="btn btn-light" href="{{ route('admin.watchlists.index') }}">Quay lai danh sach</a>
                </div>
            </section>
        </div>

        <div class="col-xl-8">
            <section class="panel-card">
                <div class="panel-header">
                    <div>
                        <h3>Lich hen match rule</h3>
                        <p>{{ $matches->count() }} ket qua gan nhat.</p>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table modern-table align-middle mb-0">
                        <thead>
                        <tr>
                            <th>Ma lich</th>
                            <th>Khach</th>
                            <th>Host</th>
                            <th>Gio hen</th>
                            <th>Trang thai</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($matches as $visit)
                            <tr>
                                <td><a class="fw-bold text-decoration-none" href="{{ route('admin.visits.show', $visit) }}">{{ $visit->code }}</a></td>
                                <td>{{ $visit->visitor?->full_name ?? '-' }}</td>
                                <td>{{ $visit->hostEmployee?->name ?? '-' }}</td>
                                <td>{{ $visit->scheduled_at?->format('Y-m-d H:i') ?? '-' }}</td>
                                <td><x-status-badge :status="$visit->status" /></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-secondary">Chua co lich nao match rule nay.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
@endsection
