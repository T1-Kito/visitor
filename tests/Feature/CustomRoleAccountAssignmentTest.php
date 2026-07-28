<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Database\Seeders\AdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomRoleAccountAssignmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_custom_role_is_available_when_creating_an_employee_account(): void
    {
        $this->seed(AdminSeeder::class);

        $admin = User::query()->where('email', 'admin@company.local')->sole();
        $customRole = Role::query()->create([
            'name' => 'Điều phối viên',
            'slug' => 'dieu-phoi-vien',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.rbac.accounts.index'))
            ->assertOk()
            ->assertSee('value="'.$customRole->id.'"', false)
            ->assertSeeText($customRole->name);
    }

    public function test_custom_role_permissions_are_enforced_and_can_be_revoked_from_matrix(): void
    {
        $this->seed(AdminSeeder::class);

        $admin = User::query()->where('email', 'admin@company.local')->sole();
        $customRole = Role::query()->create([
            'name' => 'Điều phối viên',
            'slug' => 'dieu-phoi-vien',
        ]);
        $customUser = User::query()->create([
            'name' => 'Điều phối viên thử nghiệm',
            'email' => 'dispatcher@example.test',
            'password' => 'Password@123',
            'is_active' => true,
        ]);
        $customUser->roles()->sync([$customRole->id]);

        $dashboardPermission = Permission::query()->where('slug', 'dashboard.view')->sole();
        $reportPermission = Permission::query()->where('slug', 'reports.export')->sole();

        $this->actingAs($admin)
            ->post(route('admin.rbac.permission-matrix.update'), [
                'role_ids' => [$customRole->id],
                'matrix' => [
                    $customRole->id => [$dashboardPermission->id, $reportPermission->id],
                ],
            ])
            ->assertRedirect();

        $this->assertTrue($customUser->hasPermission('reports.export'));
        $this->actingAs($customUser)
            ->get(route('admin.reports.index'))
            ->assertOk();
        $this->actingAs($customUser)
            ->get(route('admin.employees.index'))
            ->assertRedirect(route('admin.dashboard'))
            ->assertSessionHas('error');

        $this->actingAs($admin)
            ->post(route('admin.rbac.permission-matrix.update'), [
                'role_ids' => [$customRole->id],
                'matrix' => [
                    $customRole->id => [$dashboardPermission->id],
                ],
            ])
            ->assertRedirect();

        $this->assertFalse($customUser->hasPermission('reports.export'));
        $this->actingAs($customUser)
            ->get(route('admin.reports.index'))
            ->assertRedirect(route('admin.dashboard'))
            ->assertSessionHas('error');
    }
}
