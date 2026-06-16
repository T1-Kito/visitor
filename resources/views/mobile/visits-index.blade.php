@extends('layouts.mobile')

@section('title', 'Lịch hẹn')

@push('styles')
    <style>
        .m-visit-status-strip {
            display: flex;
            gap: 8px;
            overflow-x: auto;
            padding-bottom: 2px;
            scrollbar-width: none;
        }

        .m-visit-status-strip::-webkit-scrollbar {
            display: none;
        }

        .m-visit-status-tab {
            flex: 0 0 auto;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            min-height: 38px;
            padding: 0 12px;
            border: 1px solid #d8e6f5;
            border-radius: 999px;
            background: #fff;
            color: #526a88;
            font-size: .84rem;
            text-decoration: none;
        }

        .m-visit-status-tab.active {
            border-color: #f0cf43;
            background: #fff8d6;
            color: #111827;
        }

        .m-visit-quick-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            margin-top: 10px;
        }

        .m-visit-action {
            min-height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: 1px solid #cfe0f5;
            border-radius: 15px;
            background: #fff;
            color: #365270;
            font-size: .88rem;
            text-decoration: none;
        }

        .m-visit-action.primary {
            border-color: #e8b900;
            background: var(--m-secondary);
            color: #111827;
        }

        .m-visit-action.primary i {
            color: var(--m-primary);
        }

        .m-visit-card-list {
            display: grid;
            gap: 10px;
        }

        .m-visit-card {
            display: grid;
            grid-template-columns: auto 1fr auto;
            gap: 10px;
            padding: 12px;
            border: 1px solid #e2edf8;
            border-radius: 18px;
            background: #fff;
            color: inherit;
            text-decoration: none;
            box-shadow: 0 10px 24px rgba(21, 34, 54, .045);
        }

        .m-visit-card-main {
            min-width: 0;
        }

        .m-visit-card-main strong {
            display: block;
            color: #0f172a;
            font-size: .98rem;
            font-weight: 500;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .m-visit-card-main small {
            display: block;
            margin-top: 2px;
            color: #7186a3;
            font-size: .78rem;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .m-visit-card-meta {
            grid-column: 1 / -1;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            padding-top: 8px;
            border-top: 1px solid #eef4fb;
        }

        .m-visit-card-meta span {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            min-width: 0;
            color: #58708e;
            font-size: .78rem;
        }

        .m-visit-card-meta i {
            color: var(--m-primary);
        }

        .m-status-pill {
            align-self: start;
            padding: 5px 9px;
            border-radius: 999px;
            background: #fff8d6;
            color: var(--m-primary);
            font-size: .72rem;
            white-space: nowrap;
        }

        .m-status-pill.pending { background: #fff7df; color: #b45309; }
        .m-status-pill.approved { background: #edfdf5; color: #087443; }
        .m-status-pill.checked_in { background: #eaf3ff; color: #0f6eea; }
        .m-status-pill.checked_out { background: #f1f5f9; color: #475569; }
        .m-status-pill.rejected { background: #fff1f2; color: #be123c; }
    </style>
@endpush

@section('content')
    @php
        $statusLabels = [
            'all' => 'Tất cả',
            'pending' => 'Chờ duyệt',
            'approved' => 'Đã duyệt',
            'checked_in' => 'Đang trong công ty',
            'checked_out' => 'Đã ra',
            'rejected' => 'Từ chối',
        ];
        $activeStatus = $filters['status'] ?? 'all';
    @endphp

    <section class="m-page-head">
        <a href="{{ route('mobile.home') }}" aria-label="Quay lại"><i class="bi bi-chevron-left"></i></a>
        <div>
            <h1>Lịch hẹn</h1>
            <p>Mặc định hiển thị lịch mới nhất, có thể lọc theo ngày khi cần.</p>
        </div>
    </section>

    <section class="m-visit-quick-actions">
        <a class="m-visit-action primary" href="{{ route('mobile.visits.create') }}">
            <i class="bi bi-plus-circle"></i>
            Tạo lịch hẹn
        </a>
        <a class="m-visit-action" href="{{ route('mobile.visits.index') }}">
            <i class="bi bi-arrow-clockwise"></i>
            Mới nhất
        </a>
    </section>

    <section class="m-stat-strip">
        <article class="m-soft-stat blue">
            <span>Hôm nay</span>
            <strong>{{ $stats['today'] }}</strong>
        </article>
        <article class="m-soft-stat orange">
            <span>Chờ duyệt</span>
            <strong>{{ $stats['pending'] }}</strong>
        </article>
        <article class="m-soft-stat green">
            <span>Sẵn sàng vào</span>
            <strong>{{ $stats['approved'] }}</strong>
        </article>
    </section>

    <section class="m-section">
        <div class="m-visit-status-strip" aria-label="Lọc trạng thái lịch hẹn">
            @foreach ($statusLabels as $status => $label)
                <a
                    class="m-visit-status-tab {{ $activeStatus === $status ? 'active' : '' }}"
                    href="{{ route('mobile.visits.index', array_filter(['status' => $status, 'date' => $filters['date'], 'q' => $filters['q']], fn ($value) => $value !== '' && $value !== null)) }}"
                >
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <form class="m-filter-form" action="{{ route('mobile.visits.index') }}" method="get">
            <input type="hidden" name="status" value="{{ $activeStatus }}">
            <label>
                <i class="bi bi-search"></i>
                <input type="search" name="q" value="{{ $filters['q'] }}" placeholder="Tên khách, mã lịch, công ty...">
            </label>
            <label>
                <i class="bi bi-calendar3"></i>
                <input type="date" name="date" value="{{ $filters['date'] }}">
            </label>
            <button type="submit"><i class="bi bi-funnel"></i>Lọc</button>
        </form>

        <div class="m-visit-status-strip" aria-label="Lọc nhanh theo ngày">
            <a class="m-visit-status-tab {{ $filters['date'] === '' ? 'active' : '' }}" href="{{ route('mobile.visits.index', array_filter(['status' => $activeStatus, 'q' => $filters['q']], fn ($value) => $value !== '' && $value !== null)) }}">
                Mới nhất
            </a>
            <a class="m-visit-status-tab {{ $filters['date'] === now()->toDateString() ? 'active' : '' }}" href="{{ route('mobile.visits.index', array_filter(['status' => $activeStatus, 'date' => now()->toDateString(), 'q' => $filters['q']], fn ($value) => $value !== '' && $value !== null)) }}">
                Hôm nay
            </a>
        </div>
    </section>

    <section class="m-section">
        <div class="m-section-head">
            <div>
                <h2>{{ $statusLabels[$activeStatus] ?? 'Lịch hẹn' }}</h2>
                <span>{{ $visits->total() }} lịch, {{ $filters['date'] === '' ? 'mới nhất ở trên' : 'lọc ngày '.$filters['date'] }}</span>
            </div>
        </div>

        <div class="m-visit-card-list">
            @forelse ($visits as $visit)
                <a class="m-visit-card" href="{{ $visit['url'] }}">
                    <span class="m-avatar">{{ mb_substr($visit['visitor'], 0, 1) }}</span>
                    <span class="m-visit-card-main">
                        <strong>{{ $visit['visitor'] }}</strong>
                        <small>{{ $visit['code'] }} · {{ $visit['company'] }}</small>
                    </span>
                    <span class="m-status-pill {{ $visit['status'] }}">{{ $visit['status_label'] }}</span>
                    <span class="m-visit-card-meta">
                        <span><i class="bi bi-clock"></i>{{ $visit['time'] }} · {{ $visit['date'] }}</span>
                        <span><i class="bi bi-person-badge"></i>{{ $visit['host'] }}</span>
                        <span><i class="bi bi-building"></i>{{ $visit['department'] }}</span>
                        <span><i class="bi bi-chat-square-text"></i>{{ $visit['purpose'] }}</span>
                    </span>
                </a>
            @empty
                <div class="m-empty">
                    <i class="bi bi-calendar2-check"></i>
                    <span>Chưa có lịch phù hợp với bộ lọc này.</span>
                </div>
            @endforelse
        </div>

        @if ($visits->hasPages())
            <div class="m-pager">
                @if ($visits->onFirstPage())
                    <span>Trước</span>
                @else
                    <a href="{{ $visits->previousPageUrl() }}">Trước</a>
                @endif

                <strong>{{ $visits->currentPage() }}/{{ $visits->lastPage() }}</strong>

                @if ($visits->hasMorePages())
                    <a href="{{ $visits->nextPageUrl() }}">Sau</a>
                @else
                    <span>Sau</span>
                @endif
            </div>
        @endif
    </section>
@endsection
