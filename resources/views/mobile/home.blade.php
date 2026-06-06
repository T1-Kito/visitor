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
            <span data-vietnam-clock>{{ now()->format('H:i') }} - {{ now()->format('d/m/Y') }}</span>
        </div>
        <div class="m-hero-actions">
            <a class="m-hero-scan" href="{{ $canScanAccess ? route('mobile.checkin') : route('mobile.approvals') }}">
                <i class="bi bi-qr-code-scan"></i>
                Quét QR
            </a>
        </div>
    </section>

    <section class="m-section">
        <div class="m-section-head">
            <div>
                <h2>Module yêu thích</h2>
                <span>{{ $modules->count() }} mục đang ghim</span>
            </div>
            <button class="m-link-btn" type="button" data-mobile-favorites-open>
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

    <div class="m-favorites-sheet" data-mobile-favorites-sheet hidden>
        <button class="m-favorites-backdrop" type="button" data-mobile-favorites-close aria-label="Đóng cài đặt yêu thích"></button>
        <section class="m-favorites-dialog" role="dialog" aria-modal="true" aria-labelledby="mobile-favorites-title">
            <span class="m-sheet-handle" aria-hidden="true"></span>
            <form action="{{ route('mobile.favorites.update') }}" method="post">
                @csrf
                <div class="m-favorites-head">
                    <div>
                        <h2 id="mobile-favorites-title">Cài đặt yêu thích</h2>
                        <span>Chọn module muốn ghim lên trang chủ.</span>
                    </div>
                    <button class="m-favorites-close" type="button" data-mobile-favorites-close aria-label="Đóng">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div class="m-favorites-body">
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
                </div>
                <div class="m-favorites-footer">
                    <button class="m-save-btn" type="submit"><i class="bi bi-check2-circle"></i>Lưu yêu thích</button>
                </div>
            </form>
        </section>
    </div>

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
        const vietnamClock = document.querySelector('[data-vietnam-clock]');
        const updateVietnamClock = () => {
            if (!vietnamClock) return;

            const parts = new Intl.DateTimeFormat('en-GB', {
                timeZone: 'Asia/Ho_Chi_Minh',
                hour: '2-digit',
                minute: '2-digit',
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hourCycle: 'h23',
            }).formatToParts(new Date());
            const value = (type) => parts.find((part) => part.type === type)?.value ?? '';

            vietnamClock.textContent = `${value('hour')}:${value('minute')} - ${value('day')}/${value('month')}/${value('year')}`;
        };

        updateVietnamClock();
        window.setInterval(updateVietnamClock, 30000);

        const favoritesSheet = document.querySelector('[data-mobile-favorites-sheet]');
        const openFavorites = () => {
            if (!favoritesSheet) return;
            favoritesSheet.hidden = false;
            document.body.classList.add('m-sheet-open');
        };
        const closeFavorites = () => {
            if (!favoritesSheet) return;
            favoritesSheet.hidden = true;
            document.body.classList.remove('m-sheet-open');
        };

        document.querySelector('[data-mobile-favorites-open]')?.addEventListener('click', openFavorites);
        document.querySelectorAll('[data-mobile-favorites-close]').forEach((button) => {
            button.addEventListener('click', closeFavorites);
        });
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && favoritesSheet && !favoritesSheet.hidden) closeFavorites();
        });
    </script>
@endpush
