<?php

use App\Http\Controllers\AdminUiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\KioskController;
use App\Http\Controllers\LicenseController;
use App\Http\Controllers\SystemAdminController;
use Illuminate\Support\Facades\Route;

Route::get('/license', [LicenseController::class, 'show'])->name('license.show');
Route::post('/license', [LicenseController::class, 'store'])->name('license.store');

Route::get('/kiosk', [KioskController::class, 'index'])->name('kiosk.index');
Route::get('/kiosk/register', [KioskController::class, 'register'])->name('kiosk.register');
Route::get('/kiosk/privacy-notice', [KioskController::class, 'privacyNotice'])->name('kiosk.privacy-notice');
Route::get('/kiosk/employees/search', [KioskController::class, 'searchEmployees'])->name('kiosk.employees.search');
Route::post('/kiosk/checkin/manual', [KioskController::class, 'storeWalkIn'])->name('kiosk.checkin.manual');
Route::post('/kiosk/checkin/scan-qr', [KioskController::class, 'scanQr'])->name('kiosk.checkin.scan-qr');
Route::post('/kiosk/checkout/scan-qr', [KioskController::class, 'scanCheckoutQr'])->name('kiosk.checkout.scan-qr');
Route::get('/kiosk/checkin/status/{visit}', [KioskController::class, 'status'])->name('kiosk.checkin.status');
Route::post('/kiosk/checkin/{visit}/confirm', [KioskController::class, 'confirmCheckin'])->name('kiosk.checkin.confirm');
Route::post('/kiosk/walk-in', [KioskController::class, 'storeWalkIn'])->name('kiosk.walk-in.store');
Route::get('/kiosk/status/{visit}', [KioskController::class, 'status'])->name('kiosk.status');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::get('/csrf-token', [AuthController::class, 'csrfToken'])->name('csrf-token');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
});

Route::middleware('auth')->group(function (): void {
    Route::get('/', [AdminUiController::class, 'dashboard'])
        ->middleware('permission:dashboard.view')
        ->name('admin.dashboard');
    Route::get('/dashboard', [AdminUiController::class, 'dashboard'])
        ->middleware('permission:dashboard.view')
        ->name('admin.dashboard.page');
    Route::get('/dashboard/summary', [AdminUiController::class, 'dashboardSummary'])
        ->middleware('permission:dashboard.view')
        ->name('admin.dashboard.summary');
    Route::get('/dashboard/live-visitors', [AdminUiController::class, 'dashboardLiveVisitors'])
        ->middleware('permission:dashboard.view')
        ->name('admin.dashboard.live-visitors');
    Route::get('/dashboard/alerts', [AdminUiController::class, 'dashboardAlerts'])
        ->middleware('permission:dashboard.view')
        ->name('admin.dashboard.alerts');
    Route::get('/dashboard/events', [AdminUiController::class, 'dashboardEvents'])
        ->middleware('permission:dashboard.view')
        ->name('admin.dashboard.events');

    Route::get('/online-registration', [AdminUiController::class, 'onlineRegistration'])
        ->middleware('permission:visits.manage')
        ->name('admin.online-registration');
    Route::post('/online-registration/send-email', [AdminUiController::class, 'sendOnlineRegistrationEmail'])
        ->middleware('permission:visits.manage')
        ->name('admin.online-registration.send-email');

    Route::get('/m', [AdminUiController::class, 'mobileHome'])
        ->middleware('permission:dashboard.view')
        ->name('mobile.home');
    Route::post('/m/favorites', [AdminUiController::class, 'updateMobileFavorites'])
        ->middleware('permission:dashboard.view')
        ->name('mobile.favorites.update');
    Route::get('/m/approvals', [AdminUiController::class, 'mobileApprovals'])
        ->name('mobile.approvals');
    Route::get('/m/checkin', [AdminUiController::class, 'mobileCheckin'])
        ->middleware('permission:checkin.manage')
        ->name('mobile.checkin');
    Route::get('/m/checkout', [AdminUiController::class, 'mobileCheckout'])
        ->middleware('permission:checkin.manage')
        ->name('mobile.checkout');
    Route::get('/m/access-lists', [AdminUiController::class, 'mobileAccessLists'])
        ->middleware('permission:checkin.manage')
        ->name('mobile.access-lists');
    Route::get('/m/reports', [AdminUiController::class, 'mobileReports'])
        ->middleware('permission:reports.export')
        ->name('mobile.reports');
    Route::get('/m/notifications', [AdminUiController::class, 'mobileNotifications'])
        ->name('mobile.notifications');
    Route::patch('/m/notifications/{notification}/read', [AdminUiController::class, 'markMobileNotificationRead'])
        ->name('mobile.notifications.read');
    Route::get('/m/profile', [AdminUiController::class, 'mobileProfile'])
        ->name('mobile.profile');
    Route::get('/m/visits/create', [AdminUiController::class, 'mobileVisitsCreate'])
        ->middleware('permission:visits.manage')
        ->name('mobile.visits.create');
    Route::get('/m/visits', [AdminUiController::class, 'mobileVisits'])
        ->middleware('permission:visits.manage')
        ->name('mobile.visits.index');
    Route::get('/m/visits/{visit}', [AdminUiController::class, 'mobileVisitShow'])
        ->name('mobile.visits.show');

    Route::get('/visits', [AdminUiController::class, 'visitsIndex'])
        ->middleware('permission:visits.manage')
        ->name('admin.visits.index');
    Route::get('/visits/create', [AdminUiController::class, 'visitsCreate'])
        ->middleware('permission:visits.manage')
        ->name('admin.visits.create');
    Route::post('/visits', [AdminUiController::class, 'visitsStore'])
        ->middleware('permission:visits.manage')
        ->name('admin.visits.store');
    Route::get('/visits/{visit}', [AdminUiController::class, 'visitsShow'])
        ->middleware('permission:visits.manage')
        ->name('admin.visits.show');
    Route::get('/visits/{visit}/edit', [AdminUiController::class, 'visitsEdit'])
        ->middleware('permission:visits.manage')
        ->name('admin.visits.edit');
    Route::put('/visits/{visit}', [AdminUiController::class, 'visitsUpdate'])
        ->middleware('permission:visits.manage')
        ->name('admin.visits.update');
    Route::post('/visits/{visit}/cancel', [AdminUiController::class, 'cancelVisit'])
        ->middleware('permission:visits.manage')
        ->name('admin.visits.cancel');
    Route::post('/visits/{visit}/generate-qr', [AdminUiController::class, 'generateVisitQr'])
        ->middleware('permission:visits.manage')
        ->name('admin.visits.generate-qr');
    Route::post('/visits/{visit}/send-qr-email', [AdminUiController::class, 'sendVisitQrEmail'])
        ->middleware('permission:visits.manage')
        ->name('admin.visits.send-qr-email');

    Route::get('/approvals', [AdminUiController::class, 'approvalsIndex'])
        ->name('admin.approvals.index');
    Route::post('/approvals/{visit}/approve', [AdminUiController::class, 'approveVisit'])
        ->name('admin.approvals.approve');
    Route::post('/approvals/{visit}/reject', [AdminUiController::class, 'rejectVisit'])
        ->name('admin.approvals.reject');
    Route::post('/approvals/{visit}/wait', [AdminUiController::class, 'waitVisit'])
        ->name('admin.approvals.wait');

    Route::get('/access', [AdminUiController::class, 'accessIndex'])
        ->middleware('permission:checkin.manage')
        ->name('admin.access.index');
    Route::put('/access/quick-settings', [SystemAdminController::class, 'accessQuickSettingsUpdate'])
        ->middleware('permission:system.manage')
        ->name('admin.access.quick-settings.update');
    Route::get('/access/lists', [AdminUiController::class, 'accessListsIndex'])
        ->middleware('permission:checkin.manage')
        ->name('admin.access.lists');
    Route::get('/access/lists/export', [AdminUiController::class, 'accessListsExport'])
        ->middleware('permission:checkin.manage')
        ->name('admin.access.lists.export');

    Route::get('/checkin', fn () => redirect()->route('admin.access.index', ['mode' => 'checkin']))
        ->middleware('permission:checkin.manage')
        ->name('admin.checkin.index');
    Route::post('/checkin/scan-qr', [AdminUiController::class, 'scanCheckinQr'])
        ->middleware('permission:checkin.manage')
        ->name('admin.checkin.scan-qr');
    Route::post('/checkin/{visit}/confirm', [AdminUiController::class, 'confirmCheckin'])
        ->middleware('permission:checkin.manage')
        ->name('admin.checkin.confirm');

    Route::get('/checkout', fn () => redirect()->route('admin.access.index', ['mode' => 'checkout']))
        ->middleware('permission:checkin.manage')
        ->name('admin.checkout.index');
    Route::post('/checkout/scan-qr', [AdminUiController::class, 'scanCheckoutQr'])
        ->middleware('permission:checkin.manage')
        ->name('admin.checkout.scan-qr');
    Route::post('/checkout/scan-badge', [AdminUiController::class, 'scanCheckoutBadge'])
        ->middleware('permission:checkin.manage')
        ->name('admin.checkout.scan-badge');
    Route::post('/checkout/{visit}/confirm', [AdminUiController::class, 'confirmCheckout'])
        ->middleware('permission:checkin.manage')
        ->name('admin.checkout.confirm');

    Route::get('/badges', [AdminUiController::class, 'badgesIndex'])
        ->middleware('permission:badges.manage')
        ->name('admin.badges.index');

    Route::get('/alerts', [AdminUiController::class, 'alertsIndex'])
        ->middleware('permission:alerts.view')
        ->name('admin.alerts.index');

    Route::get('/watchlists', [AdminUiController::class, 'watchlistsIndex'])
        ->middleware('permission:alerts.view')
        ->name('admin.watchlists.index');
    Route::post('/watchlists', [AdminUiController::class, 'watchlistsStore'])
        ->middleware('permission:alerts.view')
        ->name('admin.watchlists.store');
    Route::get('/watchlists/{watchlist}', [AdminUiController::class, 'watchlistsShow'])
        ->middleware('permission:alerts.view')
        ->name('admin.watchlists.show');
    Route::get('/watchlists/{watchlist}/edit', [AdminUiController::class, 'watchlistsEdit'])
        ->middleware('permission:alerts.view')
        ->name('admin.watchlists.edit');
    Route::put('/watchlists/{watchlist}', [AdminUiController::class, 'watchlistsUpdate'])
        ->middleware('permission:alerts.view')
        ->name('admin.watchlists.update');
    Route::delete('/watchlists/{watchlist}', [AdminUiController::class, 'watchlistsDestroy'])
        ->middleware('permission:alerts.view')
        ->name('admin.watchlists.destroy');

    Route::get('/notifications', [AdminUiController::class, 'notificationsIndex'])
        ->name('admin.notifications.index');
    Route::get('/notifications/unread-count', [AdminUiController::class, 'notificationsUnreadCount'])
        ->name('admin.notifications.unread-count');
    Route::patch('/notifications/{notification}/read', [AdminUiController::class, 'markNotificationRead'])
        ->name('admin.notifications.read');
    Route::patch('/notifications/read-all', [AdminUiController::class, 'markAllNotificationsRead'])
        ->name('admin.notifications.read-all');

    Route::get('/departments', [CatalogController::class, 'departmentsIndex'])
        ->middleware('permission:departments.manage')
        ->name('admin.departments.index');
    Route::post('/departments', [CatalogController::class, 'departmentsStore'])
        ->middleware('permission:departments.manage')
        ->name('admin.departments.store');
    Route::get('/departments/{department}', [CatalogController::class, 'departmentsShow'])
        ->middleware('permission:departments.manage')
        ->name('admin.departments.show');
    Route::get('/departments/{department}/edit', [CatalogController::class, 'departmentsEdit'])
        ->middleware('permission:departments.manage')
        ->name('admin.departments.edit');
    Route::put('/departments/{department}', [CatalogController::class, 'departmentsUpdate'])
        ->middleware('permission:departments.manage')
        ->name('admin.departments.update');
    Route::delete('/departments/{department}', [CatalogController::class, 'departmentsDestroy'])
        ->middleware('permission:departments.manage')
        ->name('admin.departments.destroy');

    Route::get('/employees', [CatalogController::class, 'employeesIndex'])
        ->middleware('permission:employees.manage')
        ->name('admin.employees.index');
    Route::get('/employees/import-template', [CatalogController::class, 'employeesImportTemplate'])
        ->middleware('permission:employees.manage')
        ->name('admin.employees.import-template');
    Route::post('/employees/import', [CatalogController::class, 'employeesImport'])
        ->middleware('permission:employees.manage')
        ->name('admin.employees.import');
    Route::post('/employees', [CatalogController::class, 'employeesStore'])
        ->middleware('permission:employees.manage')
        ->name('admin.employees.store');
    Route::get('/employees/{employee}', [CatalogController::class, 'employeesShow'])
        ->middleware('permission:employees.manage')
        ->name('admin.employees.show');
    Route::get('/employees/{employee}/edit', [CatalogController::class, 'employeesEdit'])
        ->middleware('permission:employees.manage')
        ->name('admin.employees.edit');
    Route::put('/employees/{employee}', [CatalogController::class, 'employeesUpdate'])
        ->middleware('permission:employees.manage')
        ->name('admin.employees.update');
    Route::delete('/employees/{employee}', [CatalogController::class, 'employeesDestroy'])
        ->middleware('permission:employees.manage')
        ->name('admin.employees.destroy');

    Route::get('/visitors', [CatalogController::class, 'visitorsIndex'])
        ->middleware('permission:visitors.manage')
        ->name('admin.visitors.index');
    Route::post('/visitors', [CatalogController::class, 'visitorsStore'])
        ->middleware('permission:visitors.manage')
        ->name('admin.visitors.store');
    Route::get('/visitors/search', [AdminUiController::class, 'searchVisitors'])
        ->middleware('permission:visits.manage')
        ->name('admin.visitors.search');
    Route::get('/visitors/{visitor}', [CatalogController::class, 'visitorsShow'])
        ->middleware('permission:visitors.manage')
        ->name('admin.visitors.show');
    Route::get('/visitors/{visitor}/edit', [CatalogController::class, 'visitorsEdit'])
        ->middleware('permission:visitors.manage')
        ->name('admin.visitors.edit');
    Route::put('/visitors/{visitor}', [CatalogController::class, 'visitorsUpdate'])
        ->middleware('permission:visitors.manage')
        ->name('admin.visitors.update');
    Route::delete('/visitors/{visitor}', [CatalogController::class, 'visitorsDestroy'])
        ->middleware('permission:visitors.manage')
        ->name('admin.visitors.destroy');

    Route::get('/reports', [AdminUiController::class, 'reportsIndex'])
        ->middleware('permission:reports.export')
        ->name('admin.reports.index');
    Route::get('/reports/visits', [AdminUiController::class, 'reportsVisits'])
        ->middleware('permission:reports.export')
        ->name('admin.reports.visits');
    Route::get('/reports/by-department', [AdminUiController::class, 'reportsByDepartment'])
        ->middleware('permission:reports.export')
        ->name('admin.reports.by-department');
    Route::get('/reports/by-host', [AdminUiController::class, 'reportsByHost'])
        ->middleware('permission:reports.export')
        ->name('admin.reports.by-host');
    Route::get('/reports/current-visitors', [AdminUiController::class, 'reportsCurrentVisitors'])
        ->middleware('permission:reports.export')
        ->name('admin.reports.current-visitors');
    Route::get('/reports/overstay', [AdminUiController::class, 'reportsOverstay'])
        ->middleware('permission:reports.export')
        ->name('admin.reports.overstay');
    Route::get('/reports/rejected', [AdminUiController::class, 'reportsRejected'])
        ->middleware('permission:reports.export')
        ->name('admin.reports.rejected');
    Route::get('/reports/emergency-current-visitors', [AdminUiController::class, 'reportsEmergencyCurrentVisitors'])
        ->middleware('permission:reports.export')
        ->name('admin.reports.emergency-current-visitors');
    Route::get('/reports/visits/export', [AdminUiController::class, 'exportReportCsv'])
        ->middleware('permission:reports.export')
        ->name('admin.reports.visits.export');
    Route::get('/reports/visits/export-xlsx', [AdminUiController::class, 'exportReportXlsx'])
        ->middleware('permission:reports.export')
        ->name('admin.reports.visits.export-xlsx');
    Route::get('/reports/export/csv', [AdminUiController::class, 'exportVisitsCsv'])
        ->middleware('permission:reports.export')
        ->name('admin.reports.export.csv');

    Route::get('/rbac', [SystemAdminController::class, 'rbacIndex'])
        ->middleware('permission:system.manage')
        ->name('admin.rbac.index');
    Route::get('/rbac/accounts', [SystemAdminController::class, 'accountsIndex'])
        ->middleware('permission:system.manage')
        ->name('admin.rbac.accounts.index');
    Route::post('/rbac/users', [SystemAdminController::class, 'storeUser'])
        ->middleware('permission:system.manage')
        ->name('admin.rbac.users.store');
    Route::get('/rbac/users/{user}', [SystemAdminController::class, 'showUser'])
        ->middleware('permission:system.manage')
        ->name('admin.rbac.users.show');
    Route::get('/rbac/users/{user}/edit', [SystemAdminController::class, 'editUser'])
        ->middleware('permission:system.manage')
        ->name('admin.rbac.users.edit');
    Route::put('/rbac/users/{user}', [SystemAdminController::class, 'updateUser'])
        ->middleware('permission:system.manage')
        ->name('admin.rbac.users.update');
    Route::delete('/rbac/users/{user}', [SystemAdminController::class, 'destroyUser'])
        ->middleware('permission:system.manage')
        ->name('admin.rbac.users.destroy');
    Route::post('/rbac/users/{user}/role', [SystemAdminController::class, 'updateUserRole'])
        ->middleware('permission:system.manage')
        ->name('admin.rbac.user-role.update');
    Route::post('/rbac/roles', [SystemAdminController::class, 'storeRole'])
        ->middleware('permission:system.manage')
        ->name('admin.rbac.roles.store');
    Route::get('/rbac/roles/{role}', [SystemAdminController::class, 'showRole'])
        ->middleware('permission:system.manage')
        ->name('admin.rbac.roles.show');
    Route::get('/rbac/roles/{role}/edit', [SystemAdminController::class, 'editRole'])
        ->middleware('permission:system.manage')
        ->name('admin.rbac.roles.edit');
    Route::put('/rbac/roles/{role}', [SystemAdminController::class, 'updateRole'])
        ->middleware('permission:system.manage')
        ->name('admin.rbac.roles.update');
    Route::delete('/rbac/roles/{role}', [SystemAdminController::class, 'destroyRole'])
        ->middleware('permission:system.manage')
        ->name('admin.rbac.roles.destroy');
    Route::post('/rbac/roles/{role}/permissions', [SystemAdminController::class, 'updateRolePermissions'])
        ->middleware('permission:system.manage')
        ->name('admin.rbac.role-permissions.update');
    Route::post('/rbac/permission-matrix', [SystemAdminController::class, 'updatePermissionMatrix'])
        ->middleware('permission:system.manage')
        ->name('admin.rbac.permission-matrix.update');
    Route::post('/rbac/permissions', [SystemAdminController::class, 'storePermission'])
        ->middleware('permission:system.manage')
        ->name('admin.rbac.permissions.store');
    Route::get('/rbac/permissions/{permission}', [SystemAdminController::class, 'showPermission'])
        ->middleware('permission:system.manage')
        ->name('admin.rbac.permissions.show');
    Route::get('/rbac/permissions/{permission}/edit', [SystemAdminController::class, 'editPermission'])
        ->middleware('permission:system.manage')
        ->name('admin.rbac.permissions.edit');
    Route::put('/rbac/permissions/{permission}', [SystemAdminController::class, 'updatePermission'])
        ->middleware('permission:system.manage')
        ->name('admin.rbac.permissions.update');
    Route::delete('/rbac/permissions/{permission}', [SystemAdminController::class, 'destroyPermission'])
        ->middleware('permission:system.manage')
        ->name('admin.rbac.permissions.destroy');

    Route::get('/audit-logs', [SystemAdminController::class, 'auditLogsIndex'])
        ->middleware('permission:system.manage')
        ->name('admin.audit-logs.index');

    Route::get('/settings', [SystemAdminController::class, 'settingsIndex'])
        ->middleware('permission:system.manage')
        ->name('admin.settings.index');
    Route::get('/settings/kiosk', [SystemAdminController::class, 'kioskSettingsEdit'])
        ->middleware('permission:system.manage')
        ->name('admin.settings.kiosk');
    Route::put('/settings/kiosk', [SystemAdminController::class, 'kioskSettingsUpdate'])
        ->middleware('permission:system.manage')
        ->name('admin.settings.kiosk.update');
    Route::put('/settings/admin-theme', [SystemAdminController::class, 'adminThemeSettingsUpdate'])
        ->middleware('permission:system.manage')
        ->name('admin.settings.admin-theme.update');
    Route::get('/settings/logos', [SystemAdminController::class, 'logoSettingsEdit'])
        ->middleware('permission:system.manage')
        ->name('admin.settings.logos');
    Route::put('/settings/logos', [SystemAdminController::class, 'logoSettingsUpdate'])
        ->middleware('permission:system.manage')
        ->name('admin.settings.logos.update');
    Route::get('/settings/printer', [SystemAdminController::class, 'printerSettingsEdit'])
        ->middleware('permission:system.manage')
        ->name('admin.settings.printer');
    Route::get('/settings/mail', [SystemAdminController::class, 'mailSettingsEdit'])
        ->middleware('permission:system.manage')
        ->name('admin.settings.mail');
    Route::get('/settings/license', [LicenseController::class, 'show'])
        ->middleware('permission:system.manage')
        ->name('admin.settings.license');
    Route::put('/settings/mail', [SystemAdminController::class, 'mailSettingsUpdate'])
        ->middleware('permission:system.manage')
        ->name('admin.settings.mail.update');
    Route::post('/settings/mail/test', [SystemAdminController::class, 'mailSettingsTest'])
        ->middleware('permission:system.manage')
        ->name('admin.settings.mail.test');

    Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');
});
