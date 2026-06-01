@extends('layouts.admin')

@section('title', 'Thong bao | Visitor Management')
@section('page_title', 'Notification Center')
@section('page_subtitle', 'Theo doi thong bao tu lich hen, phe duyet, check-in/check-out va canh bao')

@section('content')
    <section class="panel-card mb-3">
        <div class="panel-header">
            <div>
                <h3>Hop thong bao</h3>
                <p>{{ $unreadCount }} thong bao chua doc.</p>
            </div>
            <form method="post" action="{{ route('admin.notifications.read-all') }}">
                @csrf
                @method('PATCH')
                <button class="btn btn-outline-primary btn-sm" type="submit">
                    <i class="bi bi-check2-all"></i>
                    Danh dau da doc tat ca
                </button>
            </form>
        </div>

        <form class="row g-2" method="get" action="{{ route('admin.notifications.index') }}">
            <div class="col-md-4">
                <select class="form-select" name="status">
                    <option value="all" @selected($filters['status'] === 'all')>Tat ca thong bao</option>
                    <option value="unread" @selected($filters['status'] === 'unread')>Chua doc</option>
                    <option value="read" @selected($filters['status'] === 'read')>Da doc</option>
                </select>
            </div>
            <div class="col-md-2 d-grid">
                <button class="btn btn-brand" type="submit">Loc</button>
            </div>
        </form>
    </section>

    <section class="panel-card">
        <div class="d-grid gap-2">
            @forelse ($notifications as $notification)
                @php
                    $alertClass = match ($notification->level) {
                        'danger' => 'border-danger-subtle bg-danger-subtle',
                        'warning' => 'border-warning-subtle bg-warning-subtle',
                        'success' => 'border-success-subtle bg-success-subtle',
                        default => 'border-light bg-white',
                    };
                    $iconClass = match ($notification->level) {
                        'danger' => 'bi-exclamation-octagon-fill text-danger',
                        'warning' => 'bi-exclamation-triangle-fill text-warning',
                        'success' => 'bi-check-circle-fill text-success',
                        default => 'bi-info-circle-fill text-primary',
                    };
                @endphp
                <div class="border rounded-4 p-3 {{ $alertClass }}">
                    <div class="d-flex flex-wrap justify-content-between gap-3">
                        <div class="d-flex gap-3">
                            <i class="bi {{ $iconClass }} fs-4"></i>
                            <div>
                                <div class="d-flex flex-wrap align-items-center gap-2">
                                    <h3 class="h6 mb-0">{{ $notification->title }}</h3>
                                    @if ($notification->read_at === null)
                                        <span class="status-badge status-pending">Chua doc</span>
                                    @else
                                        <span class="status-badge status-checked-out">Da doc</span>
                                    @endif
                                </div>
                                <p class="mb-1 mt-1">{{ $notification->message }}</p>
                                <small class="text-secondary">
                                    {{ $notification->created_at?->format('Y-m-d H:i') }}
                                    @if ($notification->type)
                                        - {{ $notification->type }}
                                    @endif
                                </small>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap gap-2 align-items-start">
                            @if ($notification->action_url)
                                @if ($notification->read_at === null)
                                    <form method="post" action="{{ route('admin.notifications.read', $notification) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn-sm btn-brand" type="submit">Mo va danh dau doc</button>
                                    </form>
                                @else
                                    <a class="btn btn-sm btn-light" href="{{ $notification->action_url }}">Mo chi tiet</a>
                                @endif
                            @elseif ($notification->read_at === null)
                                <form method="post" action="{{ route('admin.notifications.read', $notification) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="btn btn-sm btn-outline-primary" type="submit">Danh dau da doc</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="alert alert-success mb-0">
                    Khong co thong bao nao theo bo loc hien tai.
                </div>
            @endforelse
        </div>

        <div class="mt-3">
            {{ $notifications->links() }}
        </div>
    </section>
@endsection
