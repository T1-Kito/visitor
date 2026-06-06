<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $permissions = collect([
                ['name' => 'Xem dashboard', 'slug' => 'dashboard.view'],
                ['name' => 'Quan ly lich hen', 'slug' => 'visits.manage'],
                ['name' => 'Phe duyet lich hen', 'slug' => 'approvals.manage'],
                ['name' => 'Checkin/Checkout', 'slug' => 'checkin.manage'],
                ['name' => 'Xuat bao cao', 'slug' => 'reports.export'],
                ['name' => 'Quan tri he thong', 'slug' => 'system.manage'],
                ['name' => 'Quan ly phong ban', 'slug' => 'departments.manage'],
                ['name' => 'Quan ly nhan vien', 'slug' => 'employees.manage'],
                ['name' => 'Quan ly khach', 'slug' => 'visitors.manage'],
                ['name' => 'Quan ly badge', 'slug' => 'badges.manage'],
                ['name' => 'Xem canh bao', 'slug' => 'alerts.view'],
            ])->map(function (array $permission): Permission {
                return Permission::query()->updateOrCreate(
                    ['slug' => $permission['slug']],
                    ['name' => $permission['name']]
                );
            });

            $adminRole = Role::query()->updateOrCreate(
                ['slug' => 'admin'],
                ['name' => 'Admin']
            );
            $adminRole->permissions()->sync($permissions->pluck('id')->all());

            $admin = User::query()->updateOrCreate(
                ['email' => env('ADMIN_EMAIL', 'admin@company.local')],
                [
                    'name' => env('ADMIN_NAME', 'Admin'),
                    'password' => Hash::make(env('ADMIN_PASSWORD', 'Admin@123')),
                    'is_active' => true,
                ]
            );
            $admin->roles()->sync([$adminRole->id]);
        });
    }
}
