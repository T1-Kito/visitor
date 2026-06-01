<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HasAdminLayoutData;
use App\Models\AuditLog;
use App\Models\Permission;
use App\Models\Role;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SystemAdminController extends Controller
{
    use HasAdminLayoutData;

    public function rbacIndex(): View
    {
        $users = User::query()
            ->with('roles')
            ->orderBy('id')
            ->get();

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
            'users' => $users,
            'roles' => $roles,
            'permissions' => $permissions,
        ]));
    }

    public function storeUser(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role_id' => ['required', 'exists:roles,id'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $user = User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'is_active' => (bool) ($validated['is_active'] ?? true),
        ]);

        $role = Role::query()->findOrFail((int) $validated['role_id']);
        $user->roles()->sync([$role->id]);

        $this->logAudit('user.created', 'user', (string) $user->id, [
            'email' => $user->email,
            'role' => $role->slug,
        ]);

        return redirect()
            ->route('admin.rbac.users.show', $user)
            ->with('status', "Da tao user {$user->email}.");
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
            'role_slug' => ['required', 'string', 'max:120', 'regex:/^[a-z0-9_-]+$/', 'unique:roles,slug'],
            'permission_ids' => ['nullable', 'array'],
            'permission_ids.*' => ['integer', 'exists:permissions,id'],
        ]);

        $role = Role::query()->create([
            'name' => $validated['role_name'],
            'slug' => $validated['role_slug'],
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

    public function auditLogsIndex(Request $request): View
    {
        $action = trim((string) $request->input('action', ''));
        $userId = $request->input('user_id');

        $query = AuditLog::query()
            ->with('user')
            ->orderByDesc('id');

        if ($action !== '') {
            $query->where('action', 'like', '%'.$action.'%');
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
            'logo_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
            'background_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'primary_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
        ]);

        $currentSettings = SystemSetting::values(SystemSetting::kioskDefaults());

        $logoUrl = $currentSettings['kiosk.logo_url'] ?? null;
        if ($request->hasFile('logo_file')) {
            $logoUrl = $this->storeKioskUpload($request, 'logo_file', 'logo');
        }

        $backgroundUrl = $currentSettings['kiosk.background_url'] ?? null;
        if ($request->hasFile('background_file')) {
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
            'kiosk.logo_url' => $logoUrl,
            'kiosk.background_url' => $backgroundUrl,
            'kiosk.primary_color' => $validated['primary_color'],
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
