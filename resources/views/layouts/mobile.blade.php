<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>@yield('title', 'VMS Mobile')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/mobile-ui.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body>
    @php
        $mobileAuthUser = auth()->user();
        $canOpenVisits = $mobileAuthUser?->hasPermission('visits.manage');
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
            <a class="m-icon-btn" href="{{ route('admin.notifications.index') }}" aria-label="Thông báo">
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
            <a class="active" href="{{ route('mobile.home') }}">
                <i class="bi bi-house-fill"></i>
                <span>Trang chủ</span>
            </a>
            <a href="{{ $canOpenVisits ? route('admin.visits.index') : route('mobile.home') }}">
                <i class="bi bi-calendar-check"></i>
                <span>Lịch</span>
            </a>
            <a class="scan" href="{{ $canScanAccess ? route('admin.access.index', ['mode' => 'checkin']) : route('mobile.home') }}">
                <i class="bi bi-qr-code-scan"></i>
                <span>Quét QR</span>
            </a>
            <a href="{{ route('admin.notifications.index') }}">
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
    @stack('scripts')
</body>
</html>
