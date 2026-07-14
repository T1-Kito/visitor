<?php

namespace Tests\Feature;

use App\Models\Badge;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Models\Visit;
use App\Models\Visitor;
use Database\Seeders\VmsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeletePermissionsAndRegistrationSuccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_delete_visitor_with_appointments_without_server_error(): void
    {
        $this->seed(VmsSeeder::class);

        $admin = User::query()->where('email', 'superadmin@company.local')->firstOrFail();
        $host = Employee::query()->firstOrFail();
        $visitor = Visitor::query()->create(['full_name' => 'Khach can xoa']);
        $visit = Visit::query()->create([
            'code' => 'DELETE-0001',
            'visitor_id' => $visitor->id,
            'host_employee_id' => $host->id,
            'host_name' => $host->name,
            'department_id' => $host->department_id ?? Department::query()->value('id'),
            'scheduled_at' => now()->addDay(),
            'expected_checkout_at' => now()->addDay()->addHour(),
            'status' => 'pending',
            'purpose' => 'Test delete',
            'checkin_method' => 'qr',
        ]);
        $badge = Badge::query()->where('status', 'available')->firstOrFail();
        $badge->update(['visit_id' => $visit->id, 'status' => 'active']);

        $this->actingAs($admin)
            ->delete(route('admin.visitors.destroy', $visitor))
            ->assertRedirect(route('admin.visitors.index'));

        $this->assertDatabaseMissing('visitors', ['id' => $visitor->id]);
        $this->assertDatabaseMissing('visits', ['id' => $visit->id]);
        $this->assertDatabaseHas('badges', [
            'id' => $badge->id,
            'visit_id' => null,
            'status' => 'available',
        ]);
    }

    public function test_manage_permission_does_not_grant_delete_permission(): void
    {
        $this->seed(VmsSeeder::class);

        $role = Role::query()->create(['name' => 'Quan ly khong xoa', 'slug' => 'manager_without_delete']);
        $role->permissions()->attach(Permission::query()->where('slug', 'visitors.manage')->firstOrFail());
        $user = User::query()->create([
            'name' => 'Manager',
            'email' => 'manager-delete-test@example.test',
            'password' => 'Password@123',
            'is_active' => true,
        ]);
        $user->roles()->attach($role);
        $visitor = Visitor::query()->create(['full_name' => 'Khach duoc bao ve']);

        $this->actingAs($user)
            ->delete(route('admin.visitors.destroy', $visitor))
            ->assertRedirect(route('admin.dashboard'));

        $this->assertDatabaseHas('visitors', ['id' => $visitor->id]);
    }

    public function test_success_page_has_30_second_return_and_edit_form_has_no_access_area(): void
    {
        $this->seed(VmsSeeder::class);

        $admin = User::query()->where('email', 'superadmin@company.local')->firstOrFail();
        $visit = Visit::query()->where('status', 'pending')->firstOrFail();

        $this->withSession(['kiosk_success_return_url' => route('kiosk.index')])
            ->get(route('kiosk.checkin.status', $visit))
            ->assertOk()
            ->assertSee('id="ksReturnCountdown">30', false)
            ->assertSee('window.location.replace(returnUrl)', false);

        $this->actingAs($admin)
            ->get(route('admin.visits.edit', $visit))
            ->assertOk()
            ->assertDontSee('name="access_zone"', false);

        $this->actingAs($admin)
            ->get(route('admin.visits.show', $visit))
            ->assertOk()
            ->assertDontSee('name="access_zone"', false);
    }
}
