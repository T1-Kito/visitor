@extends('layouts.mobile')

@section('title', 'VMS Mobile')

@section('content')
    @php
        $canScanAccess = auth()->user()?->hasPermission('checkin.manage');
        $selectedKeys = $favoriteKeys->isEmpty() ? $availableModules->pluck('key') : $favoriteKeys;
        $statusLabels = [
            'pending' => 'Chờ duyệt',
            'approved' => 'Đã duyệt',
            'checked_in' => 'Đang trong công ty',
            'checked_out' => 'Đã ra',
            'rejected' => 'Từ chối',
            'cancelled' => 'Đã hủy',
        ];
    @endphp

    @if (session('status'))
        <div class="m-toast"><i class="bi bi-check-circle"></i><span>{{ session('status') }}</span></div>
    @endif

    <section class="m-hero">
        <div>
            <p>Hôm nay</p>
            <h1>Điều phối khách ra/vào</h1>
            <span>{{ now()->format('H:i') }} · {{ now()->format('d/m/Y') }}</span>
        </div>
        <a href="{{ $canScanAccess ? route('mobile.checkin') : route('mobile.approvals') }}"><i class="bi bi-qr-code-scan"></i>Quét QR</a>
    </section>

    <section class="m-section">
        <div class="m-section-head">
            <div>
                <h2>Module yêu thích</h2>
                <span>{{ $modules->count() }} mục đang ghim</span>
            </div>
            <button class="m-link-btn" type="button" data-mobile-favorites-toggle>
                <i class="bi bi-plus-circle"></i>Tùy chỉnh
            </button>
        </div>
        <div class="m-module-grid">
            @foreach ($modules as $module)
                <a class="m-module m-tone-{{ $module['tone'] }}" href="{{ $module['route'] }}">
                    <span class="m-module-icon">
                        <i class="bi {{ $module['icon'] }}"></i>
                        @if (($module['count'] ?? 0) > 0)
                            <em>{{ $module['count'] }}</em>
                        @endif
                    </span>
                    <strong>{{ $module['label'] }}</strong>
                    <small>{{ $module['hint'] }}</small>
                </a>
            @endforeach
        </div>
    </section>

    <section class="m-section m-favorites-panel" data-mobile-favorites-panel hidden>
        <form action="{{ route('mobile.favorites.update') }}" method="post">
            @csrf
            <div class="m-section-head">
                <div>
                    <h2>Cài đặt yêu thích</h2>
                    <span>Chọn module muốn ghim lên trang chủ.</span>
                </div>
                <button class="m-link-btn muted" type="button" data-mobile-favorites-toggle>Đóng</button>
            </div>
            <div class="m-favorite-grid">
                @foreach ($availableModules as $module)
                    @php $checked = $selectedKeys->contains($module['key']); @endphp
                    <label class="m-favorite-option m-tone-{{ $module['tone'] }}">
                        <input type="checkbox" name="modules[]" value="{{ $module['key'] }}" @checked($checked)>
                        <span class="m-module-icon"><i class="bi {{ $module['icon'] }}"></i></span>
                        <span><strong>{{ $module['label'] }}</strong><small>{{ $module['hint'] }}</small></span>
                        <i class="bi bi-plus-circle m-favorite-add"></i>
                        <i class="bi bi-check-circle-fill m-favorite-check"></i>
                    </label>
                @endforeach
            </div>
            <button class="m-save-btn" type="submit"><i class="bi bi-check2-circle"></i>Lưu yêu thích</button>
        </form>
    </section>

    <section class="m-split">
        <article class="m-card">
            <div class="m-card-head"><h2>Hôm nay</h2><i class="bi bi-info-circle"></i></div>
            <div class="m-mini-stats">
                <div><strong>{{ $stats['today'] }}</strong><span>Lịch</span></div>
                <div><strong>{{ $stats['pending_checkin'] }}</strong><span>Chờ vào</span></div>
            </div>
        </article>
        <article class="m-card">
            <div class="m-card-head"><h2>Tóm tắt</h2><span>Hôm nay</span></div>
            <div class="m-ring-row">
                <div class="m-ring blue"><strong>{{ $stats['pending'] }}</strong><span>Chờ duyệt</span></div>
                <div class="m-ring green"><strong>{{ $stats['in_company'] }}</strong><span>Trong CTY</span></div>
                <div class="m-ring orange"><strong>{{ $stats['checked_out_today'] }}</strong><span>Đã ra</span></div>
            </div>
        </article>
    </section>

    <section class="m-section">
        <div class="m-section-head">
            <h2>Lịch gần đây</h2>
            <a href="{{ route('mobile.access-lists', ['type' => 'all']) }}">Ra/vào</a>
        </div>
        <div class="m-visit-list">
            @forelse ($visits as $visit)
                <a class="m-visit" href="{{ route('mobile.visits.show', $visit['id']) }}">
                    <span class="m-avatar">{{ mb_substr($visit['visitor'], 0, 1) }}</span>
                    <span class="m-visit-main">
                        <strong>{{ $visit['visitor'] }}</strong>
                        <small>{{ $visit['code'] }} · {{ $visit['host'] }}</small>
                    </span>
                    <span class="m-visit-side">
                        <strong>{{ $visit['time'] }}</strong>
                        <small>{{ $statusLabels[$visit['status']] ?? $visit['status'] }}</small>
                    </span>
                </a>
            @empty
                <div class="m-empty"><i class="bi bi-calendar2-check"></i><span>Chưa có lịch hẹn trong hôm nay.</span></div>
            @endforelse
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('[data-mobile-favorites-toggle]').forEach((button) => {
            button.addEventListener('click', () => {
                const panel = document.querySelector('[data-mobile-favorites-panel]');
                if (!panel) return;
                panel.hidden = !panel.hidden;
                if (!panel.hidden) panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        });
    </script>
@endpush
