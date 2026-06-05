<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HasAdminLayoutData;
use App\Models\AuditLog;
use App\Models\Employee;
use App\Models\Permission;
use App\Models\Role;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SystemAdminController extends Controller
{
    use HasAdminLayoutData;

    public function rbacIndex(): View
    {
        $roles = Role::query()
            ->with('permissions')
            ->withCount('users')
            ->orderBy('name')
            ->get();

        $permissions = Permission::query()
            ->withCount('roles')
            ->orderBy('name')
            ->get();

        return view('admin.rbac.index', $this->withBase([
            'roles' => $roles,
            'permissions' => $permissions,
        ]));
    }

    public function accountsIndex(): View
    {
        $users = User::query()
            ->with(['roles', 'employeeProfile.department'])
            ->orderBy('name')
            ->get();

        $employees = Employee::query()
            ->with(['department', 'user.roles'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $roles = Role::query()
            ->whereIn('slug', ['admin', 'receptionist', 'guard', 'employee', 'department_manager', 'security_admin'])
            ->orderBy('name')
            ->get();
        $roleOrder = ['admin', 'receptionist', 'guard', 'employee', 'department_manager', 'security_admin'];
        $roles = $roles->sortBy(function (Role $role) use ($roleOrder): int {
            $index = array_search($role->slug, $roleOrder, true);

            return $index === false ? 100 : $index;
        })->values();

        return view('admin.rbac.accounts', $this->withBase([
            'users' => $users,
            'employees' => $employees,
            'roles' => $roles,
        ]));
    }

    public function storeUser(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role_id' => ['required', 'exists:roles,id'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $employee = Employee::query()->findOrFail((int) $validated['employee_id']);
        if ($employee->user_id !== null) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', "Nhân viên {$employee->name} đã có tài khoản đăng nhập.");
        }

        $emailUsedByOtherEmployee = Employee::query()
            ->where('email', $validated['email'])
            ->whereKeyNot($employee->id)
            ->exists();
        if ($emailUsedByOtherEmployee) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Email đăng nhập này đang thuộc về nhân viên khác.');
        }

        $user = User::query()->create([
            'name' => $employee->name,
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'is_active' => (bool) ($validated['is_active'] ?? true),
        ]);

        $role = Role::query()->findOrFail((int) $validated['role_id']);
        $user->roles()->sync([$role->id]);
        $employee->update([
            'user_id' => $user->id,
            'email' => $validated['email'],
        ]);

        $this->logAudit('user.created', 'user', (string) $user->id, [
            'email' => $user->email,
            'role' => $role->slug,
            'employee_id' => $employee->id,
        ]);

        return redirect()
            ->route('admin.rbac.accounts.index')
            ->with('status', "Đã tạo tài khoản {$user->email} cho nhân viên {$employee->name}.");
    }

    public function showUser(User $user): View
    {
        $user->load(['roles', 'employeeProfile.department']);
        $logs = AuditLog::query()
            ->where('user_id', $user->id)
            ->orderByDesc('id')
            ->limit(20)
            ->get();

        return view('admin.rbac.users.show', $this->withBase([
            'user' => $user,
            'logs' => $logs,
        ]));
    }

    public function editUser(User $user): View
    {
        $user->load('roles');

        return view('admin.rbac.users.edit', $this->withBase([
            'targetUser' => $user,
            'roles' => Role::query()->orderBy('name')->get(),
        ]));
    }

    public function updateUser(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8'],
            'role_id' => ['required', 'exists:roles,id'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ((int) auth()->id() === $user->id && ! (bool) ($validated['is_active'] ?? false)) {
            return redirect()->back()->withInput()->with('error', 'Khong the tu khoa tai khoan dang dang nhap.');
        }

        $payload = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'is_active' => (bool) ($validated['is_active'] ?? false),
        ];

        if (! empty($validated['password'])) {
            $payload['password'] = Hash::make($validated['password']);
        }

        $user->update($payload);

        $role = Role::query()->findOrFail((int) $validated['role_id']);
        if ($role->slug === 'employee' && $user->employeeProfile()->doesntExist()) {
            return redirect()
                ->back()
                ->with('error', 'Role Host phải được gán cho tài khoản đã liên kết với nhân viên nội bộ.');
        }

        $user->roles()->sync([$role->id]);

        $this->logAudit('user.updated', 'user', (string) $user->id, [
            'email' => $user->email,
            'role' => $role->slug,
            'is_active' => $user->is_active,
        ]);

        return redirect()
            ->route('admin.rbac.users.show', $user)
            ->with('status', "Da cap nhat user {$user->email}.");
    }

    public function destroyUser(User $user): RedirectResponse
    {
        if ((int) auth()->id() === $user->id) {
            return redirect()->back()->with('error', 'Khong the xoa tai khoan dang dang nhap.');
        }

        if ($this->isLastSystemAdmin($user)) {
            return redirect()->back()->with('error', 'Khong the xoa admin cuoi cung cua he thong.');
        }

        $userId = (string) $user->id;
        $email = $user->email;
        $user->roles()->detach();
        $user->delete();

        $this->logAudit('user.deleted', 'user', $userId, [
            'email' => $email,
        ]);

        return redirect()
            ->route('admin.rbac.index')
            ->with('status', "Da xoa user {$email}.");
    }

    public function storeRole(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'role_name' => ['required', 'string', 'max:100', 'unique:roles,name'],
            'permission_ids' => ['nullable', 'array'],
            'permission_ids.*' => ['integer', 'exists:permissions,id'],
        ]);

        $role = Role::query()->create([
            'name' => $validated['role_name'],
            'slug' => $this->generateRoleSlug($validated['role_name']),
        ]);

        $permissionIds = $validated['permission_ids'] ?? [];
        $role->permissions()->sync($permissionIds);

        $this->logAudit('role.created', 'role', (string) $role->id, [
            'role' => $role->slug,
            'permission_count' => count($permissionIds),
        ]);

        return redirect()
            ->route('admin.rbac.roles.show', $role)
            ->with('status', "Da tao role {$role->name}.");
    }

    public function showRole(Role $role): View
    {
        $role->load([
            'permissions' => fn ($query) => $query->orderBy('name'),
            'users' => fn ($query) => $query->orderBy('name'),
        ]);

        return view('admin.rbac.roles.show', $this->withBase([
            'role' => $role,
            'isProtectedRole' => in_array($role->slug, $this->protectedRoleSlugs(), true),
        ]));
    }

    public function editRole(Role $role): View
    {
        $role->load('permissions');

        return view('admin.rbac.roles.edit', $this->withBase([
            'role' => $role,
            'permissions' => Permission::query()->orderBy('name')->get(),
            'isProtectedRole' => in_array($role->slug, $this->protectedRoleSlugs(), true),
        ]));
    }

    public function updateRole(Request $request, Role $role): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('roles', 'name')->ignore($role->id)],
            'slug' => ['required', 'string', 'max:120', 'regex:/^[a-z0-9_-]+$/', Rule::unique('roles', 'slug')->ignore($role->id)],
            'permission_ids' => ['nullable', 'array'],
            'permission_ids.*' => ['integer', 'exists:permissions,id'],
        ]);

        if (
            in_array($role->slug, $this->protectedRoleSlugs(), true)
            && $validated['slug'] !== $role->slug
        ) {
            return redirect()->back()->withInput()->with('error', 'Khong the doi slug cua role he thong.');
        }

        $role->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
        ]);

        $permissionIds = $validated['permission_ids'] ?? [];
        $role->permissions()->sync($permissionIds);

        $this->logAudit('role.updated', 'role', (string) $role->id, [
            'role' => $role->slug,
            'permission_count' => count($permissionIds),
        ]);

        return redirect()
            ->route('admin.rbac.roles.show', $role)
            ->with('status', "Da cap nhat role {$role->name}.");
    }

    public function destroyRole(Role $role): RedirectResponse
    {
        if (in_array($role->slug, $this->protectedRoleSlugs(), true)) {
            return redirect()->back()->with('error', 'Khong the xoa role he thong.');
        }

        if ($role->users()->exists()) {
            return redirect()->back()->with('error', 'Khong the xoa role dang duoc gan cho user.');
        }

        $roleId = (string) $role->id;
        $roleName = $role->name;
        $roleSlug = $role->slug;

        $role->permissions()->detach();
        $role->delete();

        $this->logAudit('role.deleted', 'role', $roleId, [
            'role' => $roleSlug,
        ]);

        return redirect()
            ->route('admin.rbac.index')
            ->with('status', "Da xoa role {$roleName}.");
    }

    public function storePermission(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'permission_name' => ['required', 'string', 'max:100', 'unique:permissions,name'],
            'permission_slug' => ['required', 'string', 'max:150', 'regex:/^[a-z0-9_.-]+$/', 'unique:permissions,slug'],
        ]);

        $permission = Permission::query()->create([
            'name' => $validated['permission_name'],
            'slug' => $validated['permission_slug'],
        ]);

        $this->logAudit('permission.created', 'permission', (string) $permission->id, [
            'permission' => $permission->slug,
        ]);

        return redirect()
            ->route('admin.rbac.permissions.show', $permission)
            ->with('status', "Da tao permission {$permission->name}.");
    }

    public function showPermission(Permission $permission): View
    {
        $permission->load([
            'roles' => fn ($query) => $query->orderBy('name'),
        ]);

        return view('admin.rbac.permissions.show', $this->withBase([
            'permission' => $permission,
            'isProtectedPermission' => in_array($permission->slug, $this->protectedPermissionSlugs(), true),
        ]));
    }

    public function editPermission(Permission $permission): View
    {
        $permission->load('roles');

        return view('admin.rbac.permissions.edit', $this->withBase([
            'permission' => $permission,
            'isProtectedPermission' => in_array($permission->slug, $this->protectedPermissionSlugs(), true),
        ]));
    }

    public function updatePermission(Request $request, Permission $permission): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('permissions', 'name')->ignore($permission->id)],
            'slug' => ['required', 'string', 'max:150', 'regex:/^[a-z0-9_.-]+$/', Rule::unique('permissions', 'slug')->ignore($permission->id)],
        ]);

        if (
            in_array($permission->slug, $this->protectedPermissionSlugs(), true)
            && $validated['slug'] !== $permission->slug
        ) {
            return redirect()->back()->withInput()->with('error', 'Khong the doi slug cua permission he thong.');
        }

        $permission->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
        ]);

        $this->logAudit('permission.updated', 'permission', (string) $permission->id, [
            'permission' => $permission->slug,
        ]);

        return redirect()
            ->route('admin.rbac.permissions.show', $permission)
            ->with('status', "Da cap nhat permission {$permission->name}.");
    }

    public function destroyPermission(Permission $permission): RedirectResponse
    {
        if (in_array($permission->slug, $this->protectedPermissionSlugs(), true)) {
            return redirect()->back()->with('error', 'Khong the xoa permission he thong.');
        }

        if ($permission->roles()->exists()) {
            return redirect()->back()->with('error', 'Khong the xoa permission dang duoc gan cho role.');
        }

        $permissionId = (string) $permission->id;
        $permissionName = $permission->name;
        $permissionSlug = $permission->slug;

        $permission->delete();

        $this->logAudit('permission.deleted', 'permission', $permissionId, [
            'permission' => $permissionSlug,
        ]);

        return redirect()
            ->route('admin.rbac.index')
            ->with('status', "Da xoa permission {$permissionName}.");
    }

    public function updateUserRole(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'role_id' => ['required', 'exists:roles,id'],
        ]);

        $role = Role::query()->findOrFail((int) $validated['role_id']);
        if ($role->slug === 'employee' && $user->employeeProfile()->doesntExist()) {
            return redirect()
                ->back()
                ->with('error', 'Role Host phải được gán cho tài khoản đã liên kết với nhân viên nội bộ.');
        }

        $user->roles()->sync([$role->id]);

        AuditLog::query()->create([
            'user_id' => auth()->id(),
            'action' => 'rbac.user_role_updated',
            'entity_type' => 'user',
            'entity_id' => (string) $user->id,
            'meta' => [
                'email' => $user->email,
                'role' => $role->slug,
            ],
        ]);

        return redirect()->back()->with('status', "Da cap nhat role cho {$user->email}.");
    }

    public function updateRolePermissions(Request $request, Role $role): RedirectResponse
    {
        $validated = $request->validate([
            'permission_ids' => ['nullable', 'array'],
            'permission_ids.*' => ['integer', 'exists:permissions,id'],
        ]);

        $permissionIds = $validated['permission_ids'] ?? [];
        $role->permissions()->sync($permissionIds);

        AuditLog::query()->create([
            'user_id' => auth()->id(),
            'action' => 'rbac.role_permissions_updated',
            'entity_type' => 'role',
            'entity_id' => (string) $role->id,
            'meta' => [
                'role' => $role->slug,
                'permission_count' => count($permissionIds),
            ],
        ]);

        return redirect()->back()->with('status', "Da cap nhat permissions cho role {$role->name}.");
    }

    public function updatePermissionMatrix(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'role_ids' => ['required', 'array'],
            'role_ids.*' => ['integer', 'exists:roles,id'],
            'matrix' => ['nullable', 'array'],
            'matrix.*' => ['nullable', 'array'],
            'matrix.*.*' => ['integer', 'exists:permissions,id'],
        ]);

        $roleIds = collect($validated['role_ids'])->map(fn ($id) => (int) $id)->unique()->values();
        $matrix = collect($validated['matrix'] ?? []);

        Role::query()
            ->whereIn('id', $roleIds)
            ->get()
            ->each(function (Role $role) use ($matrix): void {
                $permissionIds = collect($matrix->get((string) $role->id, $matrix->get($role->id, [])))
                    ->map(fn ($id) => (int) $id)
                    ->unique()
                    ->values()
                    ->all();

                $role->permissions()->sync($permissionIds);

                AuditLog::query()->create([
                    'user_id' => auth()->id(),
                    'action' => 'rbac.permission_matrix_updated',
                    'entity_type' => 'role',
                    'entity_id' => (string) $role->id,
                    'meta' => [
                        'role' => $role->slug,
                        'permission_count' => count($permissionIds),
                    ],
                ]);
            });

        return redirect()->back()->with('status', 'Da cap nhat ma tran phan quyen.');
    }

    public function auditLogsIndex(Request $request): View
    {
        $action = trim((string) $request->input('action', ''));
        $userId = $request->input('user_id');

        $query = AuditLog::query()
            ->with('user')
            ->orderByDesc('id');

        if ($action !== '') {
            $normalizedAction = mb_strtolower($action);
            $actionKeywordMap = [
                'duyệt' => ['approval.approved', 'rbac.role_permissions_updated', 'rbac.permission_matrix_updated'],
                'tu choi' => ['approval.rejected'],
                'từ chối' => ['approval.rejected'],
                'checkin' => ['checkin.checked_in', 'kiosk.checked_in'],
                'check-in' => ['checkin.checked_in', 'kiosk.checked_in'],
                'checkout' => ['checkout.checked_out', 'kiosk.checked_out'],
                'check-out' => ['checkout.checked_out', 'kiosk.checked_out'],
                'gửi qr' => ['visit.qr_emailed'],
                'gui qr' => ['visit.qr_emailed'],
                'kiosk' => ['settings.kiosk_updated', 'kiosk.walk_in_created', 'kiosk.checked_in', 'kiosk.checked_out'],
                'cài đặt' => ['settings.kiosk_updated', 'settings.printer_updated'],
                'cai dat' => ['settings.kiosk_updated', 'settings.printer_updated'],
                'phân quyền' => ['rbac.user_role_updated', 'rbac.role_permissions_updated', 'rbac.permission_matrix_updated'],
                'phan quyen' => ['rbac.user_role_updated', 'rbac.role_permissions_updated', 'rbac.permission_matrix_updated'],
                'cảnh báo' => ['watchlist.matched', 'watchlist.created', 'watchlist.updated', 'watchlist.deleted'],
                'canh bao' => ['watchlist.matched', 'watchlist.created', 'watchlist.updated', 'watchlist.deleted'],
            ];

            $mappedActions = [];
            foreach ($actionKeywordMap as $keyword => $actions) {
                if (str_contains($normalizedAction, $keyword)) {
                    $mappedActions = array_merge($mappedActions, $actions);
                }
            }

            $query->where(function ($builder) use ($action, $mappedActions) {
                $builder->where('action', 'like', '%'.$action.'%');

                if ($mappedActions !== []) {
                    $builder->orWhereIn('action', array_unique($mappedActions));
                }
            });
        }

        if (is_numeric($userId)) {
            $query->where('user_id', (int) $userId);
        }

        $logs = $query->paginate(20)->withQueryString();
        $users = User::query()->orderBy('name')->get(['id', 'name', 'email']);

        return view('admin.audit-logs.index', $this->withBase([
            'logs' => $logs,
            'users' => $users,
            'filters' => [
                'action' => $action,
                'user_id' => (string) $userId,
            ],
        ]));
    }

    public function kioskSettingsEdit(): View
    {
        return view('admin.settings.kiosk', $this->withBase([
            'settings' => SystemSetting::values(SystemSetting::kioskDefaults()),
        ]));
    }

    public function printerSettingsEdit(): View
    {
        return view('admin.settings.printer', $this->withBase([
            'defaultBridgeUrl' => 'http://127.0.0.1:9191',
        ]));
    }

    public function kioskSettingsUpdate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:160'],
            'system_name' => ['required', 'string', 'max:120'],
            'subtitle' => ['required', 'string', 'max:180'],
            'welcome_title' => ['nullable', 'string', 'max:180'],
            'welcome_description' => ['required', 'string', 'max:280'],
            'hotline' => ['required', 'string', 'max:60'],
            'working_hours' => ['required', 'string', 'max:80'],
            'admin_logo_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
            'owner_logo_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
            'customer_logo_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
            'favicon_file' => ['nullable', 'file', 'mimes:ico,jpg,jpeg,png,webp,svg', 'max:1024'],
            'background_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'remove_admin_logo' => ['nullable', 'boolean'],
            'remove_owner_logo' => ['nullable', 'boolean'],
            'remove_customer_logo' => ['nullable', 'boolean'],
            'remove_favicon' => ['nullable', 'boolean'],
            'remove_background' => ['nullable', 'boolean'],
            'primary_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
        ]);

        $currentSettings = SystemSetting::values(SystemSetting::kioskDefaults());

        $adminLogoUrl = $this->resolveUploadedSetting($request, $currentSettings['admin.logo_url'] ?? null, 'admin_logo', 'admin_logo_file', 'admin-logo');
        $ownerLogoUrl = $this->resolveUploadedSetting($request, $currentSettings['kiosk.owner_logo_url'] ?? null, 'owner_logo', 'owner_logo_file', 'owner-logo');
        $customerLogoUrl = $this->resolveUploadedSetting(
            $request,
            $currentSettings['kiosk.customer_logo_url'] ?? ($currentSettings['kiosk.logo_url'] ?? null),
            'customer_logo',
            'customer_logo_file',
            'customer-logo',
        );
        $faviconUrl = $this->resolveUploadedSetting($request, $currentSettings['app.favicon_url'] ?? null, 'favicon', 'favicon_file', 'favicon');

        $backgroundUrl = $currentSettings['kiosk.background_url'] ?? null;
        if ($request->boolean('remove_background')) {
            $this->deleteKioskUpload($backgroundUrl);
            $backgroundUrl = null;
        }
        if ($request->hasFile('background_file')) {
            $this->deleteKioskUpload($backgroundUrl);
            $backgroundUrl = $this->storeKioskUpload($request, 'background_file', 'background');
        }

        SystemSetting::putMany([
            'kiosk.company_name' => $validated['company_name'],
            'kiosk.system_name' => $validated['system_name'],
            'kiosk.subtitle' => $validated['subtitle'],
            'kiosk.welcome_title' => $validated['welcome_title'] ?? null,
            'kiosk.welcome_description' => $validated['welcome_description'],
            'kiosk.hotline' => $validated['hotline'],
            'kiosk.working_hours' => $validated['working_hours'],
            'admin.logo_url' => $adminLogoUrl,
            'kiosk.owner_logo_url' => $ownerLogoUrl,
            'kiosk.customer_logo_url' => $customerLogoUrl,
            'kiosk.logo_url' => $customerLogoUrl,
            'kiosk.background_url' => $backgroundUrl,
            'kiosk.primary_color' => $validated['primary_color'],
            'app.favicon_url' => $faviconUrl,
        ]);

        $this->logAudit('settings.kiosk_updated', 'system_setting', 'kiosk', [
            'company_name' => $validated['company_name'],
            'system_name' => $validated['system_name'],
        ]);

        return redirect()
            ->route('admin.settings.kiosk')
            ->with('status', 'Da cap nhat cau hinh kiosk.');
    }

    private function storeKioskUpload(Request $request, string $field, string $prefix): string
    {
        $file = $request->file($field);
        abort_if($file === null, 422, 'File upload khong hop le.');

        $extension = $file->getClientOriginalExtension() ?: $file->extension();
        $filename = sprintf('%s-%s.%s', $prefix, now()->format('YmdHis'), $extension);
        $path = $file->storeAs('kiosk', $filename, 'public');

        return Storage::disk('public')->url($path);
    }

    private function resolveUploadedSetting(Request $request, ?string $currentUrl, string $key, string $field, string $prefix): ?string
    {
        $url = $currentUrl;

        if ($request->boolean('remove_'.$key)) {
            $this->deleteKioskUpload($url);
            $url = null;
        }

        if ($request->hasFile($field)) {
            $this->deleteKioskUpload($url);
            $url = $this->storeKioskUpload($request, $field, $prefix);
        }

        return $url;
    }

    private function deleteKioskUpload(?string $url): void
    {
        if (! $url) {
            return;
        }

        $path = parse_url($url, PHP_URL_PATH) ?: $url;
        $storagePrefix = '/storage/';
        $position = strpos($path, $storagePrefix);

        if ($position === false) {
            return;
        }

        $relativePath = ltrim(substr($path, $position + strlen($storagePrefix)), '/');

        if (str_starts_with($relativePath, 'kiosk/')) {
            Storage::disk('public')->delete($relativePath);
        }
    }


    private function isLastSystemAdmin(User $user): bool
    {
        $hasAdminRole = $user->roles()
            ->whereIn('slug', ['super_admin', 'admin'])
            ->exists();

        if (! $hasAdminRole) {
            return false;
        }

        $adminCount = User::query()
            ->where('id', '!=', $user->id)
            ->whereHas('roles', fn ($query) => $query->whereIn('slug', ['super_admin', 'admin']))
            ->count();

        return $adminCount === 0;
    }

    private function generateRoleSlug(string $name): string
    {
        $base = Str::slug(Str::ascii($name), '-');
        $base = Str::substr($base !== '' ? $base : 'vai-tro', 0, 100);
        $candidate = $base;
        $index = 2;

        while (Role::query()->where('slug', $candidate)->exists()) {
            $suffix = '-'.$index;
            $candidate = Str::substr($base, 0, 120 - strlen($suffix)).$suffix;
            $index++;
        }

        return $candidate;
    }

    /**
     * @return array<int, string>
     */
    private function protectedRoleSlugs(): array
    {
        return [
            'super_admin',
            'admin',
            'receptionist',
            'guard',
            'employee',
            'department_manager',
            'security_admin',
        ];
    }

    /**
     * @return array<int, string>
     */
    private function protectedPermissionSlugs(): array
    {
        return [
            'dashboard.view',
            'visits.manage',
            'approvals.manage',
            'checkin.manage',
            'reports.export',
            'system.manage',
            'departments.manage',
            'employees.manage',
            'visitors.manage',
            'badges.manage',
            'alerts.view',
        ];
    }

    /**
     * @param  array<string, mixed>  $meta
     */
    private function logAudit(string $action, string $entityType, string $entityId, array $meta = []): void
    {
        AuditLog::query()->create([
            'user_id' => auth()->id(),
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'meta' => $meta,
        ]);
    }
}
