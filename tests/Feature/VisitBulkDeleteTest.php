<?php

namespace Tests\Feature;

use App\Models\AccessControlLog;
use App\Models\Approval;
use App\Models\AuditLog;
use App\Models\Badge;
use App\Models\Notification;
use App\Models\User;
use App\Models\Visit;
use Database\Seeders\VmsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VisitBulkDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_preview_and_bulk_delete_historical_visits_in_date_range(): void
    {
        $this->seed(VmsSeeder::class);

        $admin = User::query()->where('email', 'superadmin@company.local')->firstOrFail();
        $seedVisit = Visit::query()->firstOrFail();
        $tenantId = $admin->tenant_id;

        $target = Visit::query()->create([
            'tenant_id' => $tenantId,
            'code' => 'BULK-HISTORY-001',
            'visitor_id' => $seedVisit->visitor_id,
            'host_employee_id' => $seedVisit->host_employee_id,
            'scheduled_at' => '2026-01-10 09:00:00',
            'expected_checkout_at' => '2026-01-10 10:00:00',
            'actual_checkin_at' => '2026-01-10 09:00:00',
            'actual_checkout_at' => '2026-01-10 10:00:00',
            'status' => 'checked_out',
            'purpose' => 'Bulk delete target',
            'checkin_method' => 'qr',
        ]);
        $outsideRange = Visit::query()->create([
            'tenant_id' => $tenantId,
            'code' => 'BULK-OUTSIDE-001',
            'visitor_id' => $seedVisit->visitor_id,
            'host_employee_id' => $seedVisit->host_employee_id,
            'scheduled_at' => '2026-02-10 09:00:00',
            'status' => 'checked_out',
            'purpose' => 'Outside range',
            'checkin_method' => 'qr',
        ]);
        $activeInRange = Visit::query()->create([
            'tenant_id' => $tenantId,
            'code' => 'BULK-ACTIVE-001',
            'visitor_id' => $seedVisit->visitor_id,
            'host_employee_id' => $seedVisit->host_employee_id,
            'scheduled_at' => '2026-01-12 09:00:00',
            'status' => 'pending',
            'purpose' => 'Active visit in range',
            'checkin_method' => 'qr',
        ]);

        $approval = Approval::query()->create([
            'tenant_id' => $tenantId,
            'visit_id' => $target->id,
            'status' => 'approved',
        ]);
        $badge = Badge::query()->create([
            'tenant_id' => $tenantId,
            'badge_no' => 'BULK-BADGE-001',
            'visit_id' => $target->id,
            'status' => 'active',
            'issued_at' => now(),
        ]);
        $accessLog = AccessControlLog::query()->create([
            'tenant_id' => $tenantId,
            'visit_id' => $target->id,
            'badge_id' => $badge->id,
            'event' => 'checkout',
            'source' => 'test',
        ]);
        $notification = Notification::query()->create([
            'tenant_id' => $tenantId,
            'user_id' => $admin->id,
            'type' => 'visit.test',
            'level' => 'info',
            'title' => 'Bulk delete test',
            'message' => 'Related notification',
            'entity_type' => 'visit',
            'entity_id' => (string) $target->id,
        ]);

        $filters = [
            'from_date' => '2026-01-01',
            'to_date' => '2026-01-31',
            'status_scope' => 'history',
        ];

        $this->actingAs($admin)
            ->get(route('admin.visits.index'))
            ->assertOk()
            ->assertSee('Xóa hàng loạt')
            ->assertSee(route('admin.visits.bulk-delete.preview'), false);

        $this->actingAs($admin)
            ->getJson(route('admin.visits.bulk-delete.preview', $filters))
            ->assertOk()
            ->assertJsonPath('count', 1);

        $this->actingAs($admin)
            ->delete(route('admin.visits.bulk-delete'), [
                ...$filters,
                'confirm_bulk_delete' => '1',
            ])
            ->assertRedirect(route('admin.visits.index'))
            ->assertSessionHas('status');

        $this->assertDatabaseMissing('visits', ['id' => $target->id]);
        $this->assertDatabaseHas('visits', ['id' => $outsideRange->id]);
        $this->assertDatabaseHas('visits', ['id' => $activeInRange->id]);
        $this->assertDatabaseMissing('approvals', ['id' => $approval->id]);
        $this->assertDatabaseMissing('access_control_logs', ['id' => $accessLog->id]);
        $this->assertDatabaseMissing('notifications', ['id' => $notification->id]);
        $this->assertDatabaseHas('badges', [
            'id' => $badge->id,
            'visit_id' => null,
            'status' => 'available',
        ]);
        $this->assertTrue(AuditLog::query()
            ->where('action', 'visit.bulk_deleted')
            ->where('entity_id', 'bulk')
            ->exists());
    }

    public function test_bulk_delete_rejects_invalid_date_range_without_deleting_visits(): void
    {
        $this->seed(VmsSeeder::class);

        $admin = User::query()->where('email', 'superadmin@company.local')->firstOrFail();
        $visitCount = Visit::query()->count();

        $this->actingAs($admin)
            ->from(route('admin.visits.index'))
            ->delete(route('admin.visits.bulk-delete'), [
                'from_date' => '2026-02-01',
                'to_date' => '2026-01-01',
                'status_scope' => 'all',
                'confirm_bulk_delete' => '1',
            ])
            ->assertRedirect(route('admin.visits.index'))
            ->assertSessionHasErrors('to_date');

        $this->assertSame($visitCount, Visit::query()->count());
    }
}
