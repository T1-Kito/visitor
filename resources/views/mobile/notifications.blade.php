@extends('layouts.mobile')

@section('title', 'Thông báo')

@section('content')
    @php
        $statusLabels = [
            'all' => 'Tất cả',
            'unread' => 'Chưa đọc',
            'read' => 'Đã đọc',
        ];
        $activeStatus = $filters['status'] ?? 'all';
    @endphp

    <div class="m-page-head">
        <a href="{{ route('mobile.home') }}" aria-label="Quay lại"><i class="bi bi-chevron-left"></i></a>
        <div>
            <h1>Thông báo</h1>
            <p>Theo dõi các việc cần xử lý trong ca trực.</p>
        </div>
    </div>

    @if (session('status'))
        <div class="m-toast"><i class="bi bi-check-circle"></i><span>{{ session('status') }}</span></div>
    @endif

    <section class="m-section">
        <div class="m-filter-tabs" aria-label="Lọc thông báo">
            @foreach ($statusLabels as $status => $label)
                <a class="m-filter-tab {{ $activeStatus === $status ? 'active' : '' }}" href="{{ route('mobile.notifications', ['status' => $status]) }}">
                    {{ $label }}
                    @if ($status === 'unread' && $unreadCount > 0)
                        <em>{{ $unreadCount }}</em>
                    @endif
                </a>
            @endforeach
        </div>
    </section>

    <section class="m-section">
        <div class="m-section-head">
            <div>
                <h2>{{ $statusLabels[$activeStatus] ?? 'Thông báo' }}</h2>
                <span>{{ $notifications->total() }} thông báo, mới nhất ở trên</span>
            </div>
        </div>

        <div class="m-notification-list">
            @forelse ($notifications as $notification)
                <form class="m-notification-form" action="{{ route('mobile.notifications.read', $notification) }}" method="post">
                    @csrf
                    @method('PATCH')
                    <button class="m-notification-card {{ $notification->read_at === null ? 'unread' : '' }} level-{{ $notification->level ?? 'info' }}" type="submit">
                        <span class="m-notification-icon">
                            <i class="bi {{ match ($notification->level) {
                                'success' => 'bi-check-circle',
                                'warning' => 'bi-hourglass-split',
                                'danger' => 'bi-exclamation-triangle',
                                default => 'bi-info-circle',
                            } }}"></i>
                        </span>
                        <span>
                            <strong>{{ $notification->title }}</strong>
                            <small>{{ $notification->message }}</small>
                            <em>{{ $notification->created_at?->format('H:i d/m/Y') }}</em>
                        </span>
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </form>
            @empty
                <div class="m-empty">
                    <i class="bi bi-bell"></i>
                    <span>Chưa có thông báo nào.</span>
                </div>
            @endforelse
        </div>

        @if ($notifications->hasPages())
            <div class="m-pager">
                @if ($notifications->onFirstPage())
                    <span>Trước</span>
                @else
                    <a href="{{ $notifications->previousPageUrl() }}">Trước</a>
                @endif

                <strong>{{ $notifications->currentPage() }}/{{ $notifications->lastPage() }}</strong>

                @if ($notifications->hasMorePages())
                    <a href="{{ $notifications->nextPageUrl() }}">Sau</a>
                @else
                    <span>Sau</span>
                @endif
            </div>
        @endif
    </section>
@endsection
