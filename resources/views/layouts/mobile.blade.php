<!doctype html>
<html lang="vi">
<head>
    @php
        $mobileThemeNavbar = $adminTheme['admin.navbar_color'] ?? '#ffcc00';
        $mobileThemeContent = $adminTheme['admin.content_background'] ?? '#f8fafc';
        $mobileThemePrimary = $adminTheme['admin.primary_color'] ?? '#d40511';
        $mobileThemeSecondary = $adminTheme['admin.secondary_color'] ?? '#ffcc00';

        $mobileThemeNavbar = is_string($mobileThemeNavbar) && preg_match('/^#[0-9a-fA-F]{6}$/', $mobileThemeNavbar)
            ? strtolower($mobileThemeNavbar)
            : '#ffcc00';
        $mobileThemeContent = is_string($mobileThemeContent) && preg_match('/^#[0-9a-fA-F]{6}$/', $mobileThemeContent)
            ? strtolower($mobileThemeContent)
            : '#f8fafc';
        $mobileThemePrimary = is_string($mobileThemePrimary) && preg_match('/^#[0-9a-fA-F]{6}$/', $mobileThemePrimary)
            ? strtolower($mobileThemePrimary)
            : '#d40511';
        $mobileThemeSecondary = is_string($mobileThemeSecondary) && preg_match('/^#[0-9a-fA-F]{6}$/', $mobileThemeSecondary)
            ? strtolower($mobileThemeSecondary)
            : '#ffcc00';
    @endphp
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="{{ $mobileThemeNavbar }}">
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
    <style>
        :root {
            --m-bg: {{ $mobileThemeContent }};
            --m-navbar: {{ $mobileThemeNavbar }};
            --m-primary: {{ $mobileThemePrimary }};
            --m-secondary: {{ $mobileThemeSecondary }};
            --m-blue: {{ $mobileThemePrimary }};
            --m-cyan: {{ $mobileThemePrimary }};
        }
    </style>
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

        document.querySelectorAll('.m-toast:not([data-approval-toast])').forEach((toast) => {
            const duration = toast.classList.contains('danger') ? 12000 : 5200;
            window.setTimeout(() => {
                toast.classList.add('is-hiding');
                window.setTimeout(() => toast.remove(), 260);
            }, duration);
        });
    </script>
    @stack('scripts')
</body>
</html>
