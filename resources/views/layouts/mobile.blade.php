<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#edf4f2">
    <title>@yield('title', 'VMS Mobile')</title>
    @if (! empty($adminBrand['favicon_url']))
        <link rel="icon" href="{{ $adminBrand['favicon_url'] }}">
        <link rel="shortcut icon" href="{{ $adminBrand['favicon_url'] }}">
    @endif
    <link rel="manifest" href="{{ \App\Support\AssetVersion::url('manifest.webmanifest') }}">
    <link rel="apple-touch-icon" href="{{ \App\Support\AssetVersion::url('icons/vms-pwa.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ \App\Support\AssetVersion::url('css/mobile-ui.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body>
    @php
        $mobileAuthUser = auth()->user();
        $canScanAccess = $mobileAuthUser?->hasPermission('checkin.manage');
    @endphp

    <div class="m-app">

        <main class="m-content">
            @yield('content')
        </main>

        <nav class="m-bottom-nav" aria-label="Mobile navigation">
            <a class="{{ request()->routeIs('mobile.home') ? 'active' : '' }}" href="{{ route('mobile.home') }}">
                <i class="bi bi-house-fill"></i>
                <span>Trang chủ</span>
            </a>
            <a class="{{ request()->routeIs('mobile.approvals') ? 'active' : '' }}" href="{{ route('mobile.approvals') }}">
                <i class="bi bi-patch-check"></i>
                <span>Duyệt</span>
            </a>
            <a class="scan {{ request()->routeIs('mobile.checkin', 'mobile.checkout') ? 'active' : '' }}" href="{{ $canScanAccess ? route('mobile.checkin') : route('mobile.home') }}">
                <i class="bi bi-qr-code-scan"></i>
                <span>Quét QR</span>
            </a>
            <a class="{{ request()->routeIs('mobile.notifications') ? 'active' : '' }}" href="{{ route('mobile.notifications') }}">
                <span class="m-bottom-nav-icon">
                    <i class="bi bi-bell"></i>
                    @if (($notificationUnreadCount ?? 0) > 0)
                        <em class="m-bottom-nav-badge">
                            {{ $notificationUnreadCount > 99 ? '99+' : $notificationUnreadCount }}
                        </em>
                    @endif
                </span>
                <span>Thông báo</span>
            </a>
            <a class="{{ request()->routeIs('mobile.profile') ? 'active' : '' }}" href="{{ route('mobile.profile') }}">
                <i class="bi bi-person-circle"></i>
                <span>Tôi</span>
            </a>
        </nav>
    </div>

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('{{ \App\Support\AssetVersion::url('sw.js') }}').catch(() => {});
            });
        }
    </script>
    @stack('scripts')
</body>
</html>
