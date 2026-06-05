@extends('layouts.mobile')

@section('title', 'Danh sách ra/vào')

@section('content')
    @php
        $typeLabels = [
            'inside' => 'Đang trong công ty',
            'in' => 'Vào hôm nay',
            'out' => 'Ra hôm nay',
            'all' => 'Tất cả',
        ];
        $activeType = $filters['type'] ?? 'inside';
    @endphp

    <div class="m-page-head">
        <a href="{{ route('mobile.home') }}" aria-label="Quay lại"><i class="bi bi-chevron-left"></i></a>
        <div>
            <h1>Danh sách ra/vào</h1>
            <p>Tra cứu nhanh khách đang ở công ty và lịch sử trong ngày.</p>
        </div>
    </div>

    @if (session('status'))
        <div class="m-toast"><i class="bi bi-check-circle"></i><span>{{ session('status') }}</span></div>
    @endif

    <section class="m-stat-strip">
        <a class="m-soft-stat blue {{ $activeType === 'inside' ? 'active' : '' }}" href="{{ route('mobile.access-lists', ['type' => 'inside', 'date' => $filters['date']]) }}">
            <span>Trong công ty</span>
            <strong>{{ $stats['inside'] }}</strong>
        </a>
        <a class="m-soft-stat green {{ $activeType === 'in' ? 'active' : '' }}" href="{{ route('mobile.access-lists', ['type' => 'in', 'date' => now()->toDateString()]) }}">
            <span>Vào hôm nay</span>
            <strong>{{ $stats['in_today'] }}</strong>
        </a>
        <a class="m-soft-stat orange {{ $activeType === 'out' ? 'active' : '' }}" href="{{ route('mobile.access-lists', ['type' => 'out', 'date' => now()->toDateString()]) }}">
            <span>Ra hôm nay</span>
            <strong>{{ $stats['out_today'] }}</strong>
        </a>
    </section>

    <section class="m-section">
        <div class="m-filter-tabs" aria-label="Bộ lọc danh sách ra vào">
            @foreach ($typeLabels as $type => $label)
                <a class="m-filter-tab {{ $activeType === $type ? 'active' : '' }}" href="{{ route('mobile.access-lists', ['type' => $type, 'date' => $filters['date'], 'q' => $filters['q']]) }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <form class="m-filter-form" action="{{ route('mobile.access-lists') }}" method="get">
            <input type="hidden" name="type" value="{{ $activeType }}">
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
    </section>

    <section class="m-section">
        <div class="m-section-head">
            <div>
                <h2>{{ $typeLabels[$activeType] ?? 'Danh sách' }}</h2>
                <span>{{ $visits->total() }} khách, mới nhất ở trên</span>
            </div>
        </div>

        <div class="m-access-list">
            @forelse ($visits as $visit)
                <a class="m-access-card" href="{{ $visit['url'] }}">
                    <span class="m-avatar">{{ mb_substr($visit['visitor'], 0, 1) }}</span>
                    <span class="m-access-main">
                        <strong>{{ $visit['visitor'] }}</strong>
                        <small>{{ $visit['company'] }} · {{ $visit['phone'] }}</small>
                        <em class="m-access-badge {{ $visit['badge_type'] }}">{{ $visit['label'] }}</em>
                    </span>
                    <span class="m-access-more"><i class="bi bi-chevron-right"></i></span>
                    <span class="m-access-meta">
                        <span><i class="bi bi-person-badge"></i>{{ $visit['host'] }}</span>
                        <span><i class="bi bi-building"></i>{{ $visit['department'] }}</span>
                    </span>
                    <span class="m-access-times">
                        <span><i class="bi bi-box-arrow-in-right"></i>{{ $visit['checkin_at'] }}</span>
                        <span><i class="bi bi-box-arrow-left"></i>{{ $visit['checkout_at'] }}</span>
                    </span>
                </a>
            @empty
                <div class="m-empty">
                    <i class="bi bi-people"></i>
                    <span>Chưa có dữ liệu phù hợp với bộ lọc này.</span>
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
