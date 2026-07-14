<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /** @var array<string, string> */
    private array $permissions = [
        'visits.delete' => 'Xoa lich hen',
        'approvals.delete' => 'Tu choi duyet khach',
        'access.delete' => 'Xoa du lieu khach ra vao',
        'alerts.delete' => 'Xoa canh bao',
        'notifications.delete' => 'Xoa thong bao',
        'visitors.delete' => 'Xoa khach',
    ];

    public function up(): void
    {
        $now = now();

        foreach ($this->permissions as $slug => $name) {
            DB::table('permissions')->updateOrInsert(
                ['slug' => $slug],
                ['name' => $name, 'updated_at' => $now, 'created_at' => $now]
            );
        }

        $adminRoleIds = DB::table('roles')
            ->whereIn('slug', ['super_admin', 'admin'])
            ->pluck('id');
        $permissionIds = DB::table('permissions')
            ->whereIn('slug', array_keys($this->permissions))
            ->pluck('id');

        foreach ($adminRoleIds as $roleId) {
            foreach ($permissionIds as $permissionId) {
                DB::table('permission_role')->insertOrIgnore([
                    'role_id' => $roleId,
                    'permission_id' => $permissionId,
                ]);
            }
        }
    }

    public function down(): void
    {
        $permissionIds = DB::table('permissions')
            ->whereIn('slug', array_keys($this->permissions))
            ->pluck('id');

        DB::table('permission_role')->whereIn('permission_id', $permissionIds)->delete();
        DB::table('permissions')->whereIn('id', $permissionIds)->delete();
    }
};
