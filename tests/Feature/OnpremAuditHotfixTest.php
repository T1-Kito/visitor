<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Approval;
use App\Models\Department;
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
        $formToken = 'visit-form-token-cccd';

        $this->actingAs($admin)
            ->withSession(['visit_create_token_'.$formToken => now()->addMinutes(30)->toIso8601String()])
            ->post(route('admin.visits.store'), [
                'visit_form_token' => $formToken,
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
        $createdVisit = Visit::query()->where('visitor_id', $visitor->id)->firstOrFail();

        $this->assertSame('Cuc CSQLHC ve TTXH', $visitor->identity_issued_place);
        $this->assertSame($admin->id, $createdVisit->created_by_user_id);

        $visitsResponse = $this->actingAs($admin)
            ->get(route('admin.visits.index'))
            ->assertOk()
            ->assertSee('Người tạo')
            ->assertSee('Kiem tra CCCD');

        $createdVisitRow = collect($visitsResponse->viewData('visits'))
            ->firstWhere('id', $createdVisit->id);

        $this->assertNotNull($createdVisitRow);
        $this->assertSame($admin->name, $createdVisitRow['creator']);
        $this->assertSame('Nguyen Can Cuoc', $createdVisitRow['visitor']);
        $this->assertSame('Kiem tra CCCD', $createdVisitRow['purpose']);

        $this->actingAs($admin)
            ->getJson(route('admin.visitors.search', ['q' => '079200012345']))
            ->assertOk()
            ->assertJsonPath('data.0.identity_no', '079200012345')
            ->assertJsonPath('data.0.identity_issued_place', 'Cuc CSQLHC ve TTXH');
    }

    public function test_admin_visitor_search_matches_vietnamese_name_without_accents(): void
    {
        $this->seed(VmsSeeder::class);

        $admin = User::query()->where('email', 'superadmin@company.local')->firstOrFail();

        Visitor::query()->create([
            'full_name' => 'Nguyễn Ngọc Uẩn',
            'phone' => '0982751075',
            'email' => 'uan@example.test',
        ]);

        $this->actingAs($admin)
            ->getJson(route('admin.visitors.search', ['q' => 'nguye']))
            ->assertOk()
            ->assertJsonFragment([
                'full_name' => 'Nguyễn Ngọc Uẩn',
                'phone' => '0982751075',
            ]);
    }

    public function test_watchlist_is_scanned_again_when_pending_visit_is_approved(): void
    {
        $this->seed(VmsSeeder::class);

        $admin = User::query()->where('email', 'superadmin@company.local')->firstOrFail();
        $hostVisit = Visit::query()->firstOrFail();
        $formToken = 'visit-form-token-watchlist';

        $this->actingAs($admin)
            ->withSession(['visit_create_token_'.$formToken => now()->addMinutes(30)->toIso8601String()])
            ->post(route('admin.visits.store'), [
                'visit_form_token' => $formToken,
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

    public function test_admin_cannot_reapprove_the_same_visit_twice(): void
    {
        $this->seed(VmsSeeder::class);

        $admin = User::query()->where('email', 'superadmin@company.local')->firstOrFail();
        $visit = Visit::query()->where('status', 'pending')->firstOrFail();

        $this->actingAs($admin)
            ->post(route('admin.approvals.approve', $visit))
            ->assertRedirect();

        $visit->refresh();

        $this->assertSame('approved', $visit->status);
        $this->assertSame(1, Approval::query()->where('visit_id', $visit->id)->count());

        $this->actingAs($admin)
            ->post(route('admin.approvals.approve', $visit))
            ->assertRedirect();

        $visit->refresh();

        $this->assertSame('approved', $visit->status);
        $this->assertSame(1, Approval::query()->where('visit_id', $visit->id)->count());
        $this->assertSame(0, Approval::query()->where('visit_id', $visit->id)->where('status', 'pending')->count());
    }

    public function test_admin_visit_creation_rejects_reused_submit_token(): void
    {
        $this->seed(VmsSeeder::class);

        $admin = User::query()->where('email', 'superadmin@company.local')->firstOrFail();
        $hostVisit = Visit::query()->firstOrFail();
        $formToken = 'visit-form-token-double-submit';
        $payload = [
            'visit_form_token' => $formToken,
            'visitor_name' => 'Double Submit Visitor',
            'visitor_phone' => '0909911444',
            'visitor_email' => 'double.submit@example.test',
            'visitor_company' => 'Double Submit Co',
            'visitor_identity_no' => '079200099999',
            'host_employee_id' => $hostVisit->host_employee_id,
            'visit_date' => now()->addDay()->toDateString(),
            'visit_time' => '09:00',
            'expected_checkout_time' => '10:30',
            'purpose' => 'Kiem tra double submit',
            'access_zone' => 'Tang 1 - Le tan',
            'checkin_method' => 'qr',
        ];

        $this->actingAs($admin)
            ->withSession(['visit_create_token_'.$formToken => now()->addMinutes(30)->toIso8601String()])
            ->post(route('admin.visits.store'), $payload)
            ->assertRedirect();

        $this->actingAs($admin)
            ->post(route('admin.visits.store'), $payload)
            ->assertRedirect(route('admin.visits.create'));

        $visitor = Visitor::query()->where('identity_no', '079200099999')->firstOrFail();

        $this->assertSame(1, Visit::query()->where('visitor_id', $visitor->id)->count());
    }

    public function test_department_tree_data_includes_parent_child_hierarchy(): void
    {
        $this->seed(VmsSeeder::class);

        $admin = User::query()->where('email', 'superadmin@company.local')->firstOrFail();

        $response = $this->actingAs($admin)
            ->get(route('admin.departments.index'))
            ->assertOk();

        $departments = collect($response->viewData('departments'));

        $rootDepartment = $departments->firstWhere('code', 'HQ');
        $childDepartment = $departments->firstWhere('code', 'SALES');

        $this->assertNotNull($rootDepartment);
        $this->assertSame(null, $rootDepartment->parent_id);
        $this->assertGreaterThan(0, (int) $rootDepartment->children_count);

        $this->assertNotNull($childDepartment);
        $this->assertInstanceOf(Department::class, $childDepartment->parent);
        $this->assertSame('HQ', $childDepartment->parent?->code);
    }

    public function test_audit_log_keeps_actor_and_request_context_snapshot(): void
    {
        $this->seed(VmsSeeder::class);

        $admin = User::query()->where('email', 'superadmin@company.local')->firstOrFail();
        $originalName = $admin->name;
        $originalEmail = $admin->email;

        $this->actingAs($admin)
            ->withServerVariables([
                'REMOTE_ADDR' => '10.20.30.40',
                'HTTP_USER_AGENT' => 'VMS-Audit-Test',
            ])
            ->post(route('admin.departments.store'), [
                'name' => 'Audit Snapshot Department',
                'parent_id' => null,
            ])
            ->assertRedirect();

        $log = AuditLog::query()
            ->where('action', 'department.created')
            ->latest('id')
            ->firstOrFail();

        $this->assertSame($admin->id, $log->user_id);
        $this->assertSame($originalName, $log->actor_name);
        $this->assertSame($originalEmail, $log->actor_email);
        $this->assertSame('10.20.30.40', $log->ip_address);
        $this->assertSame('POST', $log->request_method);
        $this->assertStringContainsString('/departments', (string) $log->request_url);
        $this->assertSame('VMS-Audit-Test', $log->user_agent);

        $admin->update([
            'name' => 'Renamed Administrator',
            'email' => 'renamed-admin@example.test',
        ]);

        $log->refresh();

        $this->assertSame($originalName, $log->actor_name);
        $this->assertSame($originalEmail, $log->actor_email);

        $this->actingAs($admin)
            ->get(route('admin.audit-logs.index', [
                'from_date' => now()->toDateString(),
                'to_date' => now()->toDateString(),
            ]))
            ->assertOk()
            ->assertSee($originalName)
            ->assertSee($originalEmail)
            ->assertSee('10.20.30.40')
            ->assertSee('POST');
    }
}
