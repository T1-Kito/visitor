<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Notification;
use App\Models\User;
use App\Models\Visit;
use App\Models\Visitor;
use App\Models\Watchlist;
use Database\Seeders\VmsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OnpremAuditHotfixTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_visit_creation_saves_and_searches_identity_fields(): void
    {
        $this->seed(VmsSeeder::class);

        $admin = User::query()->where('email', 'superadmin@company.local')->firstOrFail();
        $hostVisit = Visit::query()->firstOrFail();

        $this->actingAs($admin)
            ->post(route('admin.visits.store'), [
                'visitor_name' => 'Nguyen Can Cuoc',
                'visitor_phone' => '0909911222',
                'visitor_email' => 'cccd@example.test',
                'visitor_company' => 'Identity Co',
                'visitor_identity_no' => '079200012345',
                'visitor_identity_issued_place' => 'Cuc CSQLHC ve TTXH',
                'visitor_identity_issued_date' => now()->subYear()->toDateString(),
                'host_employee_id' => $hostVisit->host_employee_id,
                'visit_date' => now()->addDay()->toDateString(),
                'visit_time' => '09:00',
                'expected_checkout_time' => '10:30',
                'purpose' => 'Kiem tra CCCD',
                'access_zone' => 'Tang 1 - Le tan',
                'checkin_method' => 'qr',
            ])
            ->assertRedirect();

        $visitor = Visitor::query()->where('identity_no', '079200012345')->firstOrFail();

        $this->assertSame('Cuc CSQLHC ve TTXH', $visitor->identity_issued_place);

        $this->actingAs($admin)
            ->getJson(route('admin.visitors.search', ['q' => '079200012345']))
            ->assertOk()
            ->assertJsonPath('data.0.identity_no', '079200012345')
            ->assertJsonPath('data.0.identity_issued_place', 'Cuc CSQLHC ve TTXH');
    }

    public function test_watchlist_is_scanned_again_when_pending_visit_is_approved(): void
    {
        $this->seed(VmsSeeder::class);

        $admin = User::query()->where('email', 'superadmin@company.local')->firstOrFail();
        $hostVisit = Visit::query()->firstOrFail();

        $this->actingAs($admin)
            ->post(route('admin.visits.store'), [
                'visitor_name' => 'Tran Watch Approve',
                'visitor_phone' => '0909911333',
                'visitor_email' => 'watch.approve@example.test',
                'visitor_company' => 'Watch Approve Co',
                'visitor_identity_no' => '012345678999',
                'host_employee_id' => $hostVisit->host_employee_id,
                'visit_date' => now()->addDay()->toDateString(),
                'visit_time' => '09:00',
                'expected_checkout_time' => '10:30',
                'purpose' => 'Kiem tra watchlist approve',
                'access_zone' => 'Tang 1 - Le tan',
                'checkin_method' => 'qr',
            ])
            ->assertRedirect();

        $visit = Visit::query()->whereHas('visitor', fn ($query) => $query->where('identity_no', '012345678999'))->firstOrFail();

        Watchlist::query()->create([
            'keyword' => '012345678999',
            'match_type' => 'identity_no',
            'level' => 'critical',
            'status' => 'active',
            'reason' => 'Canh bao khi duyet.',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.approvals.approve', $visit))
            ->assertRedirect();

        $this->assertTrue(AuditLog::query()
            ->where('action', 'watchlist.matched')
            ->get()
            ->contains(fn (AuditLog $log): bool => ($log->meta['event_type'] ?? null) === 'approval.approved'));

        $this->assertTrue(Notification::query()
            ->where('type', 'watchlist.match')
            ->where('level', 'danger')
            ->exists());
    }
}

