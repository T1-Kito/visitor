<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\User;
use App\Models\Visit;
use Database\Seeders\VmsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VisitApprovalDisplayTest extends TestCase
{
    use RefreshDatabase;

    public function test_approval_and_visit_details_show_registered_department_instead_of_legacy_access_zone(): void
    {
        $this->seed(VmsSeeder::class);

        $admin = User::query()->where('email', 'superadmin@company.local')->firstOrFail();
        $department = Department::query()->create([
            'code' => 'CUSTOMER-SERVICE',
            'name' => 'Phòng Dịch vụ Khách hàng',
        ]);
        $visit = Visit::query()->where('status', 'pending')->firstOrFail();
        $visit->update([
            'department_id' => $department->id,
            'access_zone' => 'Tầng 1 - Lễ tân',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.approvals.index'))
            ->assertOk()
            ->assertSeeText($department->name)
            ->assertDontSeeText($visit->access_zone);

        $this->actingAs($admin)
            ->get(route('admin.visits.show', $visit))
            ->assertOk()
            ->assertSeeText('Phòng ban')
            ->assertSeeText($department->name)
            ->assertDontSeeText('Khu vực')
            ->assertDontSeeText($visit->access_zone);

        $this->actingAs($admin)
            ->get(route('mobile.visits.show', $visit))
            ->assertOk()
            ->assertSeeText($department->name)
            ->assertDontSeeText('Khu vực')
            ->assertDontSeeText($visit->access_zone);
    }
}
