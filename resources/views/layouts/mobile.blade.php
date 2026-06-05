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
        <header class="m-topbar">
            <a class="m-brand" href="{{ route('mobile.home') }}">
                <span class="m-brand-logo {{ ! empty($adminBrand['logo_url']) ? 'has-logo' : '' }}">
                    @if (! empty($adminBrand['logo_url']))
                        <img src="{{ $adminBrand['logo_url'] }}" alt="{{ $adminBrand['name'] ?? 'Logo' }}">
                    @else
                        {{ $adminBrand['initials'] ?? 'VMS' }}
                    @endif
                </span>
                <span>
                    <strong>{{ $adminBrand['name'] ?? 'VMS' }}</strong>
                    <small>{{ $currentUser['role'] ?? 'Mobile' }}</small>
                </span>
            </a>
            <a class="m-icon-btn" href="{{ route('mobile.notifications') }}" aria-label="Thông báo">
                <i class="bi bi-bell"></i>
                @if (($notificationUnreadCount ?? 0) > 0)
                    <em>{{ $notificationUnreadCount }}</em>
                @endif
            </a>
        </header>

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
                <i class="bi bi-bell"></i>
                <span>Thông báo</span>
            </a>
            <form action="{{ route('admin.logout') }}" method="post">
                @csrf
                <button type="submit">
                    <i class="bi bi-person-circle"></i>
                    <span>Tôi</span>
                </button>
            </form>
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
