<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Notification;
use App\Models\SystemSetting;
use App\Models\User;

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
            $user->loadMissing('roles');
        }

        $sidebarMenu = [];
        foreach ($this->adminMenuConfig() as $item) {
            if ($user !== null && ($item['permission'] === null || $user->hasPermission($item['permission']))) {
                $sidebarMenu[] = [
                    'label' => $item['label'],
                    'route' => $item['route'],
                    'icon' => $item['icon'],
                    'group' => $item['group'] ?? null,
                ];
            }
        }

        $kioskSettings = SystemSetting::values(SystemSetting::kioskDefaults());
        $brandName = trim((string) ($kioskSettings['kiosk.system_name'] ?? 'Gatehouse Pro'));
        $brandSubtitle = trim((string) ($kioskSettings['kiosk.subtitle'] ?? 'Quản lý khách ra vào'));
        $brandInitials = collect(preg_split('/\s+/', $brandName) ?: [])
            ->filter()
            ->take(3)
            ->map(fn (string $word) => mb_substr($word, 0, 1))
            ->implode('');

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
                'logo_url' => $kioskSettings['kiosk.logo_url'] ?? null,
                'initials' => mb_strtoupper($brandInitials !== '' ? $brandInitials : 'VMS'),
            ],
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
            ['label' => 'Phê duyệt', 'route' => 'admin.approvals.index', 'icon' => 'bi-patch-check-fill', 'permission' => 'approvals.manage', 'group' => 'VẬN HÀNH'],
            ['label' => 'Khách ra/vào', 'route' => 'admin.access.index', 'icon' => 'bi-arrow-left-right', 'permission' => 'checkin.manage', 'group' => 'VẬN HÀNH'],
            ['label' => 'Danh sách ra/vào', 'route' => 'admin.access.lists', 'icon' => 'bi-list-check', 'permission' => 'checkin.manage', 'group' => 'VẬN HÀNH'],
            ['label' => 'Khách', 'route' => 'admin.visitors.index', 'icon' => 'bi-person-lines-fill', 'permission' => 'visitors.manage', 'group' => 'QUẢN LÝ'],
            ['label' => 'Nhân viên', 'route' => 'admin.employees.index', 'icon' => 'bi-people-fill', 'permission' => 'employees.manage', 'group' => 'QUẢN LÝ'],
            ['label' => 'Phòng ban', 'route' => 'admin.departments.index', 'icon' => 'bi-building-fill', 'permission' => 'departments.manage', 'group' => 'QUẢN LÝ'],
            ['label' => 'Báo cáo', 'route' => 'admin.reports.index', 'icon' => 'bi-file-earmark-bar-graph-fill', 'permission' => 'reports.export', 'group' => 'QUẢN LÝ'],
            ['label' => 'Danh sách cảnh báo', 'route' => 'admin.watchlists.index', 'icon' => 'bi-shield-exclamation', 'permission' => 'alerts.view', 'group' => 'AN NINH'],
            ['label' => 'Cảnh báo', 'route' => 'admin.alerts.index', 'icon' => 'bi-exclamation-triangle-fill', 'permission' => 'alerts.view', 'group' => 'AN NINH'],
            ['label' => 'Thẻ ra vào', 'route' => 'admin.badges.index', 'icon' => 'bi-person-badge-fill', 'permission' => 'badges.manage', 'group' => 'AN NINH'],
            ['label' => 'Thông báo', 'route' => 'admin.notifications.index', 'icon' => 'bi-bell-fill', 'permission' => null, 'group' => 'HỆ THỐNG'],
            ['label' => 'Cài đặt', 'route' => 'admin.settings.kiosk', 'icon' => 'bi-sliders', 'permission' => 'system.manage', 'group' => 'HỆ THỐNG'],
            ['label' => 'Cài đặt máy in', 'route' => 'admin.settings.printer', 'icon' => 'bi-printer-fill', 'permission' => 'system.manage', 'group' => 'HỆ THỐNG'],
            ['label' => 'Phân quyền', 'route' => 'admin.rbac.index', 'icon' => 'bi-shield-lock-fill', 'permission' => 'system.manage', 'group' => 'HỆ THỐNG'],
            ['label' => 'Nhật ký hệ thống', 'route' => 'admin.audit-logs.index', 'icon' => 'bi-journal-text', 'permission' => 'system.manage', 'group' => 'HỆ THỐNG'],
        ];
    }

    private function adminLayoutUnreadNotificationCount(int $userId): int
    {
        return Notification::query()
            ->where('user_id', $userId)
            ->whereNull('read_at')
            ->count();
    }
}
