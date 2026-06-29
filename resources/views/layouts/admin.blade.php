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
    @php
        $adminThemeNavbar = $adminTheme['admin.navbar_color'] ?? '#ffcc00';
        $adminThemeContent = $adminTheme['admin.content_background'] ?? '#ffffff';
        $adminThemePrimary = $adminTheme['admin.primary_color'] ?? '#d40511';
        $adminThemeSecondary = $adminTheme['admin.secondary_color'] ?? '#ffcc00';
        $adminKioskBackground = $adminKioskTheme['background_color'] ?? '#ffffff';
    @endphp
    <style>
        :root {
            --admin-navbar-color: {{ preg_match('/^#[0-9a-fA-F]{6}$/', $adminThemeNavbar) ? $adminThemeNavbar : '#ffcc00' }};
            --admin-content-background: {{ preg_match('/^#[0-9a-fA-F]{6}$/', $adminThemeContent) ? $adminThemeContent : '#ffffff' }};
            --admin-primary-color: {{ preg_match('/^#[0-9a-fA-F]{6}$/', $adminThemePrimary) ? $adminThemePrimary : '#d40511' }};
            --admin-secondary-color: {{ preg_match('/^#[0-9a-fA-F]{6}$/', $adminThemeSecondary) ? $adminThemeSecondary : '#ffcc00' }};
            --gate-blue: var(--admin-primary-color);
            --gate-blue-2: color-mix(in srgb, var(--admin-primary-color) 78%, #071421);
            --gate-cyan: var(--admin-secondary-color);
            --gate-bg: color-mix(in srgb, var(--admin-secondary-color) 5%, #f8fafc);
            --gate-bg-2: color-mix(in srgb, var(--admin-secondary-color) 3%, #ffffff);
        }
    </style>
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
                    @yield('topbar_meta')
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

    <div class="modal fade admin-theme-modal" id="adminThemeModal" tabindex="-1" aria-labelledby="adminThemeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content" method="post" action="{{ route('admin.settings.admin-theme.update') }}">
                @csrf
                @method('put')
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="adminThemeModalLabel">Mau giao dien</h5>
                        <p class="modal-subtitle">Doi nhanh mau admin va nen kiosk.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Dong"></button>
                </div>
                <div class="modal-body">
                    <label class="admin-theme-field">
                        <span>Thanh dieu huong</span>
                        <div>
                            <input type="color" value="{{ $adminThemeNavbar }}" data-theme-color-picker="navbar_color">
                            <input type="text" name="navbar_color" value="{{ $adminThemeNavbar }}" maxlength="7" data-theme-color-text="navbar_color" required>
                        </div>
                    </label>
                    <label class="admin-theme-field">
                        <span>Nen noi dung admin</span>
                        <div>
                            <input type="color" value="{{ $adminThemeContent }}" data-theme-color-picker="content_background">
                            <input type="text" name="content_background" value="{{ $adminThemeContent }}" maxlength="7" data-theme-color-text="content_background" required>
                        </div>
                    </label>
                    <label class="admin-theme-field">
                        <span>Mau nhan chinh</span>
                        <div>
                            <input type="color" value="{{ $adminThemePrimary }}" data-theme-color-picker="primary_color">
                            <input type="text" name="primary_color" value="{{ $adminThemePrimary }}" maxlength="7" data-theme-color-text="primary_color" required>
                        </div>
                    </label>
                    <label class="admin-theme-field">
                        <span>Mau nhan phu</span>
                        <div>
                            <input type="color" value="{{ $adminThemeSecondary }}" data-theme-color-picker="secondary_color">
                            <input type="text" name="secondary_color" value="{{ $adminThemeSecondary }}" maxlength="7" data-theme-color-text="secondary_color" required>
                        </div>
                    </label>
                    <label class="admin-theme-field">
                        <span>Nen kiosk</span>
                        <div>
                            <input type="color" value="{{ $adminKioskBackground }}" data-theme-color-picker="kiosk_background_color">
                            <input type="text" name="kiosk_background_color" value="{{ $adminKioskBackground }}" maxlength="7" data-theme-color-text="kiosk_background_color" required>
                        </div>
                    </label>
                    <div class="admin-theme-presets" aria-label="Mau goi y">
                        <button type="button" data-theme-preset data-nav="#f6fbff" data-content="#ffffff" data-primary="#146bd7" data-secondary="#0cb4d8" data-kiosk-bg="#f4f8fd" title="Mac dinh"><span style="background:#f6fbff"></span></button>
                        <button type="button" data-theme-preset data-nav="#ffffff" data-content="#f8fafc" data-primary="#d40511" data-secondary="#ffcc00" data-kiosk-bg="#fff8df" title="DHL"><span style="background:linear-gradient(135deg,#ffcc00 0 55%,#d40511 55%)"></span></button>
                        <button type="button" data-theme-preset data-nav="#f8fafc" data-content="#ffffff" data-primary="#334155" data-secondary="#64748b" data-kiosk-bg="#f8fafc" title="Trang xam"><span style="background:#f8fafc"></span></button>
                        <button type="button" data-theme-preset data-nav="#eff6ff" data-content="#ffffff" data-primary="#146bd7" data-secondary="#0cb4d8" data-kiosk-bg="#eef7ff" title="Xanh nhe"><span style="background:#eff6ff"></span></button>
                        <button type="button" data-theme-preset data-nav="#f7fee7" data-content="#ffffff" data-primary="#15803d" data-secondary="#84cc16" data-kiosk-bg="#f7fee7" title="Xanh la"><span style="background:#f7fee7"></span></button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Huy</button>
                    <button class="admin-theme-save" type="submit">
                        <i class="bi bi-check2"></i>
                        Luu mau
                    </button>
                </div>
            </form>
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

        (() => {
            const normalizeColor = (value) => /^#[0-9a-fA-F]{6}$/.test(value) ? value.toLowerCase() : null;
            const setThemeColor = (key, value) => {
                const color = normalizeColor(value);
                if (!color) return;

                document
                    .querySelectorAll(`[data-theme-color-picker="${key}"], [data-theme-color-text="${key}"]`)
                    .forEach((input) => {
                        input.value = color;
                    });

                if (key === 'navbar_color') {
                    document.documentElement.style.setProperty('--admin-navbar-color', color);
                }

                if (key === 'content_background') {
                    document.documentElement.style.setProperty('--admin-content-background', color);
                }

                if (key === 'primary_color') {
                    document.documentElement.style.setProperty('--admin-primary-color', color);
                    document.documentElement.style.setProperty('--gate-blue', color);
                }

                if (key === 'secondary_color') {
                    document.documentElement.style.setProperty('--admin-secondary-color', color);
                    document.documentElement.style.setProperty('--gate-cyan', color);
                }
            };

            document.querySelectorAll('[data-theme-color-picker], [data-theme-color-text]').forEach((input) => {
                input.addEventListener('input', () => {
                    const key = input.dataset.themeColorPicker || input.dataset.themeColorText;
                    setThemeColor(key, input.value);
                });
            });

            document.querySelectorAll('[data-theme-preset]').forEach((button) => {
                button.addEventListener('click', () => {
                    setThemeColor('navbar_color', button.dataset.nav || '#f6fbff');
                    setThemeColor('content_background', button.dataset.content || '#ffffff');
                    setThemeColor('primary_color', button.dataset.primary || '#146bd7');
                    setThemeColor('secondary_color', button.dataset.secondary || '#0cb4d8');
                    setThemeColor('kiosk_background_color', button.dataset.kioskBg || '#f4f8fd');
                });
            });
        })();
    </script>
    @stack('scripts')
</body>
</html>
