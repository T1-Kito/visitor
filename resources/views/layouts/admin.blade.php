<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Visitor Management Dashboard')</title>
    @if (! empty($adminBrand['favicon_url']))
        <link rel="icon" href="{{ $adminBrand['favicon_url'] }}">
        <link rel="shortcut icon" href="{{ $adminBrand['favicon_url'] }}">
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ \App\Support\AssetVersion::url('css/admin-ui.css') }}" rel="stylesheet">
    <link href="{{ \App\Support\AssetVersion::url('css/admin-fixed-shell.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body>
    <div class="dashboard-shell">
        <aside class="sidebar-panel d-none d-lg-flex">
            <div class="brand-block">
                <div class="brand-badge {{ ! empty($adminBrand['logo_url']) ? 'has-logo' : '' }}">
                    @if (! empty($adminBrand['logo_url']))
                        <img src="{{ $adminBrand['logo_url'] }}" alt="{{ $adminBrand['name'] ?? 'Logo' }}">
                    @else
                        {{ $adminBrand['initials'] ?? 'VMS' }}
                    @endif
                </div>
            </div>

            @include('admin.partials.sidebar-menu')

            <div class="sidebar-footer" data-account-toggle>
                <button type="button" class="sidebar-account" data-account-trigger aria-expanded="false">
                    <div class="user-avatar sidebar-user-avatar">{{ strtoupper(substr($currentUser['name'], 0, 1)) }}</div>
                    <div class="sidebar-account-meta">
                        <p class="user-name">{{ $currentUser['name'] }}</p>
                        <p class="user-role">{{ $currentUser['role'] }}</p>
                    </div>
                    <i class="bi bi-chevron-up sidebar-account-caret"></i>
                </button>
                <div class="sidebar-account-menu">
                    <form action="{{ route('admin.logout') }}" method="post">
                        @csrf
                        <button class="sidebar-logout" type="submit">
                            <i class="bi bi-box-arrow-right"></i>
                            Thoát
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <div class="content-panel">
            <header class="topbar">
                <div class="topbar-left">
                    <button class="btn btn-outline-primary d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" aria-controls="mobileSidebar">
                        <i class="bi bi-list"></i>
                    </button>
                    <div>
                        <h2 class="page-title">@yield('page_title', 'Tổng quan')</h2>
                        <p class="page-subtitle">@yield('page_subtitle', 'Vận hành hệ thống tiếp đón khách')</p>
                    </div>
                </div>

                <div class="topbar-right">
                    <a class="btn btn-light d-none d-md-inline-flex align-items-center gap-2" href="{{ route('kiosk.index') }}" target="_blank" rel="noopener">
                        <i class="bi bi-display"></i>
                        Kiosk
                    </a>
                    <a class="btn btn-light position-relative" href="{{ route('admin.notifications.index') }}">
                        <i class="bi bi-bell"></i>
                        <span class="d-none d-md-inline">Thông báo</span>
                        @if (($notificationUnreadCount ?? 0) > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                {{ $notificationUnreadCount }}
                            </span>
                        @endif
                    </a>
                    <a class="btn btn-brand d-none d-md-inline-flex align-items-center gap-2" href="{{ route('admin.visits.create') }}">
                        <i class="bi bi-plus-circle"></i>
                        Tạo lịch
                    </a>
                </div>
            </header>

            <main class="workspace">
                @if (! empty($licenseNotice))
                    <div class="license-warning-banner">
                        <div class="license-warning-icon">
                            <i class="bi bi-shield-exclamation"></i>
                        </div>
                        <div class="license-warning-content">
                            <strong>{{ $licenseNotice['title'] }}</strong>
                            <p>{{ $licenseNotice['message'] }}</p>
                        </div>
                        <a class="license-warning-action" href="{{ $licenseNotice['url'] }}">
                            Xem bản quyền
                        </a>
                    </div>
                @endif

                @php
                    $adminNoticeMessages = [];
                    if (session('error')) {
                        $adminNoticeMessages[] = session('error');
                    }
                    if (session('status')) {
                        $adminNoticeMessages[] = session('status');
                    }
                    if ($errors->any()) {
                        $adminNoticeMessages = array_merge($adminNoticeMessages, $errors->all());
                    }
                    $adminNoticeType = session('error') || $errors->any() ? 'danger' : (session('status') ? 'success' : null);
                @endphp

                @if ($adminNoticeType && count($adminNoticeMessages) > 0)
                    <div class="admin-notice-layer" id="adminNotice" data-notice-type="{{ $adminNoticeType }}">
                        <div class="admin-notice admin-notice-{{ $adminNoticeType }}">
                            <div class="admin-notice-icon">
                                <i class="bi {{ $adminNoticeType === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill' }}"></i>
                            </div>
                            <div class="admin-notice-content">
                                <strong>{{ $adminNoticeType === 'success' ? 'Thao tác thành công' : 'Cần kiểm tra lại' }}</strong>
                                @foreach ($adminNoticeMessages as $message)
                                    <p>{{ $message }}</p>
                                @endforeach
                            </div>
                            <button class="admin-notice-close" type="button" data-close-admin-notice aria-label="Đóng thông báo">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <div class="offcanvas offcanvas-start mobile-sidebar" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="mobileSidebarLabel">Quản lý khách</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            @include('admin.partials.sidebar-menu')
            <div class="sidebar-footer" data-account-toggle>
                <button type="button" class="sidebar-account" data-account-trigger aria-expanded="false">
                    <div class="user-avatar sidebar-user-avatar">{{ strtoupper(substr($currentUser['name'], 0, 1)) }}</div>
                    <div class="sidebar-account-meta">
                        <p class="user-name">{{ $currentUser['name'] }}</p>
                        <p class="user-role">{{ $currentUser['role'] }}</p>
                    </div>
                    <i class="bi bi-chevron-up sidebar-account-caret"></i>
                </button>
                <div class="sidebar-account-menu">
                    <form action="{{ route('admin.logout') }}" method="post">
                        @csrf
                        <button class="sidebar-logout" type="submit">
                            <i class="bi bi-box-arrow-right"></i>
                            Thoát
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const adminNotice = document.getElementById('adminNotice');
        if (adminNotice) {
            const closeAdminNotice = () => adminNotice.classList.add('is-hidden');
            document.querySelector('[data-close-admin-notice]')?.addEventListener('click', closeAdminNotice);
            const noticeDuration = adminNotice.dataset.noticeType === 'danger' ? 12000 : 5200;
            setTimeout(closeAdminNotice, noticeDuration);
        }

        document.querySelectorAll('[data-account-toggle]').forEach((footer) => {
            const trigger = footer.querySelector('[data-account-trigger]');
            if (!trigger) return;
            trigger.addEventListener('click', (event) => {
                event.stopPropagation();
                const isOpen = footer.classList.toggle('is-open');
                trigger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            });
        });

        document.addEventListener('click', (event) => {
            document.querySelectorAll('[data-account-toggle].is-open').forEach((footer) => {
                if (!footer.contains(event.target)) {
                    footer.classList.remove('is-open');
                    footer.querySelector('[data-account-trigger]')?.setAttribute('aria-expanded', 'false');
                }
            });
        });

        document.addEventListener('submit', (event) => {
            const form = event.target;
            if (!(form instanceof HTMLFormElement) || !form.matches('[data-disable-on-submit]')) {
                return;
            }

            if (form.dataset.submitted === '1') {
                event.preventDefault();
                return;
            }

            form.dataset.submitted = '1';
            form.querySelectorAll('button[type="submit"]').forEach((button) => {
                button.disabled = true;
                button.setAttribute('aria-busy', 'true');
                const loadingText = button.dataset.loadingText;
                if (loadingText) {
                    const label = button.querySelector('span');
                    if (label) {
                        label.textContent = loadingText;
                    } else {
                        button.textContent = loadingText;
                    }
                }
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
