<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Notification;
use App\Models\Employee;
use App\Models\SystemSetting;
use App\Models\User;
use App\Support\LicenseManager;
use Carbon\CarbonImmutable;

trait HasAdminLayoutData
{
    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function withBase(array $data): array
    {
        /** @var User|null $user */
        $user = auth()->user();

        if ($user === null) {
            $user = User::query()->with('roles')->orderBy('id')->first();
        } else {
            $user->loadMissing(['roles', 'employeeProfile']);
        }

        $sidebarMenu = [];
        foreach ($this->adminMenuConfig() as $item) {
            $canSeeHostApproval = $item['route'] === 'admin.approvals.index'
                && $user !== null
                && $user->roles->contains(fn ($role): bool => $role->slug === 'employee')
                && Employee::query()
                    ->where(function ($query) use ($user): void {
                        $query->where('user_id', $user->id);

                        if (trim((string) $user->email) !== '') {
                            $query->orWhere('email', $user->email);
                        }
                    })
                    ->exists();
            if ($user !== null && ($item['permission'] === null || $user->hasPermission($item['permission']) || $canSeeHostApproval)) {
                $sidebarMenu[] = [
                    'label' => $item['label'],
                    'route' => $item['route'],
                    'icon' => $item['icon'],
                    'group' => $item['group'] ?? null,
                ];
            }
        }

        $kioskSettings = SystemSetting::values(SystemSetting::kioskDefaults());
        $kioskLobbyModeEnabled = ($kioskSettings['kiosk.lobby_mode_enabled'] ?? '0') === '1';
        if ($kioskLobbyModeEnabled) {
            $sidebarMenu = array_values(array_filter($sidebarMenu, fn (array $item): bool => $item['route'] !== 'admin.online-registration'));
        }
        $adminTheme = SystemSetting::values(SystemSetting::adminThemeDefaults());
        $brandName = trim((string) ($kioskSettings['kiosk.system_name'] ?? 'Gatehouse Pro'));
        $brandSubtitle = trim((string) ($kioskSettings['kiosk.subtitle'] ?? 'Quản lý khách ra vào'));
        $brandInitials = collect(preg_split('/\s+/', $brandName) ?: [])
            ->filter()
            ->take(3)
            ->map(fn (string $word) => mb_substr($word, 0, 1))
            ->implode('');
        $adminLogoUrl = $kioskSettings['admin.logo_url'] ?? null;
        $licenseNotice = $this->adminLayoutLicenseNotice(app(LicenseManager::class)->status());


        return array_merge([
            'currentUser' => [
                'name' => $user?->name ?? 'Người dùng',
                'role' => $user?->roles->first()?->name ?? 'Chưa có vai trò',
            ],
            'notificationUnreadCount' => $user === null ? 0 : $this->adminLayoutUnreadNotificationCount((int) $user->id),
            'sidebarMenu' => $sidebarMenu,
            'adminBrand' => [
                'name' => $brandName !== '' ? $brandName : 'Gatehouse Pro',
                'subtitle' => $brandSubtitle !== '' ? $brandSubtitle : 'Quản lý khách ra vào',
                'logo_url' => $adminLogoUrl,
                'favicon_url' => $kioskSettings['app.favicon_url'] ?? $adminLogoUrl,
                'initials' => mb_strtoupper($brandInitials !== '' ? $brandInitials : 'VMS'),
            ],
            'adminTheme' => $adminTheme,
            'adminKioskTheme' => [
                'background_color' => $kioskSettings['kiosk.background_color'] ?? '#f4f8fd',
            ],
            'kioskLobbyModeEnabled' => $kioskLobbyModeEnabled,
            'licenseNotice' => $licenseNotice,
        ], $data);
    }

    /**
     * @return array<int, array{label: string, route: string, icon: string, permission: string|null, group: string|null}>
     */
    protected function adminMenuConfig(): array
    {
        return [
            ['label' => 'Tổng quan', 'route' => 'admin.dashboard', 'icon' => 'bi-grid-1x2-fill', 'permission' => 'dashboard.view', 'group' => null],
            ['label' => 'Lịch hẹn', 'route' => 'admin.visits.index', 'icon' => 'bi-calendar-check-fill', 'permission' => 'visits.manage', 'group' => 'VẬN HÀNH'],
            ['label' => 'Đăng ký online', 'route' => 'admin.online-registration', 'icon' => 'bi-send-fill', 'permission' => 'visits.manage', 'group' => 'VẬN HÀNH'],
            ['label' => 'Khách cần duyệt', 'route' => 'admin.approvals.index', 'icon' => 'bi-patch-check-fill', 'permission' => 'approvals.manage', 'group' => 'VẬN HÀNH'],
            ['label' => 'Danh sách ra/vào', 'route' => 'admin.access.lists', 'icon' => 'bi-list-check', 'permission' => 'checkin.manage', 'group' => 'VẬN HÀNH'],
            ['label' => 'Khách', 'route' => 'admin.visitors.index', 'icon' => 'bi-person-lines-fill', 'permission' => 'visitors.manage', 'group' => 'QUẢN LÝ'],
            ['label' => 'Nhân viên', 'route' => 'admin.employees.index', 'icon' => 'bi-people-fill', 'permission' => 'employees.manage', 'group' => 'QUẢN LÝ'],
            ['label' => 'Phòng ban', 'route' => 'admin.departments.index', 'icon' => 'bi-building-fill', 'permission' => 'departments.manage', 'group' => 'QUẢN LÝ'],
            ['label' => 'Báo cáo', 'route' => 'admin.reports.index', 'icon' => 'bi-file-earmark-bar-graph-fill', 'permission' => 'reports.export', 'group' => 'QUẢN LÝ'],
            ['label' => 'Danh sách cảnh báo', 'route' => 'admin.watchlists.index', 'icon' => 'bi-shield-exclamation', 'permission' => 'alerts.view', 'group' => 'AN NINH'],
            ['label' => 'Cảnh báo', 'route' => 'admin.alerts.index', 'icon' => 'bi-exclamation-triangle-fill', 'permission' => 'alerts.view', 'group' => 'AN NINH'],
            ['label' => 'Thẻ ra vào', 'route' => 'admin.badges.index', 'icon' => 'bi-person-badge-fill', 'permission' => 'badges.manage', 'group' => 'AN NINH'],
            ['label' => 'Thông báo', 'route' => 'admin.notifications.index', 'icon' => 'bi-bell-fill', 'permission' => null, 'group' => 'HỆ THỐNG'],
            ['label' => 'Cài đặt', 'route' => 'admin.settings.index', 'icon' => 'bi-grid-fill', 'permission' => 'system.manage', 'group' => 'HỆ THỐNG'],
        ];
    }

    private function adminLayoutUnreadNotificationCount(int $userId): int
    {
        return Notification::query()
            ->where('user_id', $userId)
            ->whereNull('read_at')
            ->count();
    }

    /**
     * @param  array<string, mixed>  $licenseStatus
     * @return array{type: string, title: string, message: string, url: string, days_remaining: int|null, ends_at: string|null}|null
     */
    private function adminLayoutLicenseNotice(array $licenseStatus): ?array
    {
        if (! ($licenseStatus['enabled'] ?? false) || ! ($licenseStatus['valid'] ?? false)) {
            return null;
        }

        $daysRemaining = $licenseStatus['days_remaining'] ?? ($licenseStatus['trial_days_remaining'] ?? null);
        $endsAt = $licenseStatus['trial_ends_at'] ?? ($licenseStatus['expires_at'] ?? null);

        if (is_int($daysRemaining) && $daysRemaining > 3) {
            return null;
        }

        if ($daysRemaining === null && is_string($endsAt) && $endsAt !== '') {
            try {
                $daysRemaining = now()->startOfDay()->diffInDays(CarbonImmutable::parse($endsAt)->startOfDay(), false);
            } catch (\Throwable) {
                return null;
            }

            if ($daysRemaining > 3) {
                return null;
            }
        }

        if (! is_int($daysRemaining) || $daysRemaining < 0 || $daysRemaining > 3) {
            return null;
        }

        return [
            'type' => 'warning',
            'title' => 'Bản quyền sắp hết hạn',
            'message' => $daysRemaining === 0
                ? 'Bản quyền sẽ hết hạn trong hôm nay. Vui lòng liên hệ nhà cung cấp.'
                : "Còn {$daysRemaining} ngày nữa là hết hạn. Vui lòng liên hệ nhà cung cấp để gia hạn.",
            'url' => route('admin.settings.license'),
            'days_remaining' => $daysRemaining,
            'ends_at' => is_string($endsAt) && $endsAt !== '' ? $endsAt : null,
        ];
    }
}
