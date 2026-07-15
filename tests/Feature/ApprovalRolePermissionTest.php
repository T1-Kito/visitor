<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\SystemSetting;
use App\Models\User;
use App\Models\Visit;
use Database\Seeders\VmsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApprovalRolePermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_receptionist_with_approval_permission_can_approve_a_visit(): void
    {
        $this->seed(VmsSeeder::class);

        $receptionist = User::query()
            ->where('email', 'reception1@company.local')
            ->firstOrFail();
        $approvalPermission = Permission::query()
            ->where('slug', 'approvals.manage')
            ->firstOrFail();
        $receptionist->roles()->firstOrFail()->permissions()
            ->syncWithoutDetaching([$approvalPermission->id]);

        SystemSetting::withoutGlobalScopes()->updateOrCreate(
            [
                'tenant_id' => $receptionist->tenant_id,
                'key' => 'kiosk.lobby_mode_enabled',
            ],
            ['value' => '0'],
        );

        $visit = Visit::query()->where('status', 'pending')->firstOrFail();

        $this->actingAs($receptionist)
            ->get(route('admin.approvals.index'))
            ->assertOk();

        $this->actingAs($receptionist)
            ->post(route('admin.approvals.approve', $visit))
            ->assertRedirect()
            ->assertSessionHas('status')
            ->assertSessionMissing('error');

        $this->assertSame('approved', $visit->fresh()->status);
        $this->assertSame($receptionist->id, $visit->approval?->approver_user_id);
    }
}
