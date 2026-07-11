<?php

namespace Tests\Feature;

use App\Models\AccessControlLog;
use App\Models\Approval;
use App\Models\Department;
use App\Models\Employee;
use App\Models\SystemSetting;
use App\Models\User;
use App\Models\Visit;
use Database\Seeders\VmsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KioskFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_kiosk_form_matches_dhl_visitor_requirements(): void
    {
        $response = $this->get('/kiosk');

        $response
            ->assertOk()
            ->assertSee('1. Visitor Information')
            ->assertSee('2. Check-in/out Information')
            ->assertSee('3. Meeting Information')
            ->assertSee('4. Visiting Information')
            ->assertSee('name="visitor_identity_no"', false)
            ->assertSee('name="visitor_id_card_number"', false)
            ->assertSee('Visitor card 20')
            ->assertSee('name="safety_acknowledged"', false)
            ->assertDontSee('id="employeeSearch"', false)
            ->assertDontSee('id="employeeResults"', false)
            ->assertDontSee('No matching employee')
            ->assertSee('name="checkin_date"', false)
            ->assertSee('name="checkin_time"', false)
            ->assertSee('name="checkout_date"', false)
            ->assertSee('name="checkout_time"', false)
            ->assertSee('DHL Privacy Notice')
            ->assertSee('Thông báo Bảo mật của DHL tại')
            ->assertSee('Privacy Notice - DHL - Global')
            ->assertDontSee('name="visitor_identity_issued_place"', false)
            ->assertDontSee('name="visitor_identity_issued_date"', false)
            ->assertDontSee('name="expected_checkout_time"', false);
    }

    public function test_kiosk_can_search_active_employee(): void
    {
        $this->seed(VmsSeeder::class);

        $employee = Employee::query()->where('is_active', true)->firstOrFail();
        $term = substr((string) $employee->email, 0, 4);

        $this->getJson(route('kiosk.employees.search', ['q' => $term]))
            ->assertOk()
            ->assertJsonFragment([
                'id' => $employee->id,
                'name' => $employee->name,
            ]);
    }
    public function test_lobby_mode_hides_visit_code_and_uses_admin_support_number(): void
    {
        $this->seed(VmsSeeder::class);

        $admin = User::query()->where('email', 'superadmin@company.local')->firstOrFail();
        foreach ([
            'kiosk.lobby_mode_enabled' => '1',
            'kiosk.hotline' => '0901 234 567',
        ] as $key => $value) {
            SystemSetting::withoutGlobalScopes()->updateOrCreate(
                ['tenant_id' => $admin->tenant_id, 'key' => $key],
                ['value' => $value],
            );
        }

        $visit = Visit::query()->firstOrFail();

        $this->get(route('kiosk.checkin.status', $visit))
            ->assertOk()
            ->assertSee('0901 234 567')
            ->assertDontSee('Mã lịch hẹn của bạn')
            ->assertDontSee($visit->code)
            ->assertDontSee('mã QR/check-in sẽ được gửi qua Gmail');
    }
    public function test_guest_can_create_walk_in_request_from_kiosk(): void
    {
        $this->seed(VmsSeeder::class);

        $host = Employee::query()->where('is_active', true)->firstOrFail();
        $checkinAt = now()->addMinutes(30)->startOfMinute();
        $checkoutAt = $checkinAt->copy()->addHours(4);

        $response = $this->post('/kiosk/checkin/manual', [
            'registration_form' => 'kiosk_v2',
            'visitor_name' => 'Khach Kiosk Demo',
            'visitor_phone' => '0909000999',
            'visitor_email' => '',
            'visitor_company' => 'Demo Company',
            'visitor_identity_no' => 'P12345678',
            'visitor_id_card_number' => '1',
            'host_employee_id' => $host->id,
            'host_name' => $host->name,
            'department_id' => $host->department_id ?? Department::query()->value('id'),
            'purpose' => 'Họp',
            'checkin_date' => $checkinAt->toDateString(),
            'checkin_time' => $checkinAt->format('H:i'),
            'checkout_date' => $checkoutAt->toDateString(),
            'checkout_time' => $checkoutAt->format('H:i'),
            'visitor_note' => 'Tạo từ kiosk',
            'policy_accepted' => '1',
            'safety_acknowledged' => '1',
        ]);

        $visit = Visit::query()->whereHas('visitor', function ($query): void {
            $query->where('full_name', 'Khach Kiosk Demo');
        })->firstOrFail();

        $response
            ->assertRedirect(route('kiosk.checkin.status', $visit))
            ->assertSessionHas('status')
            ->assertSessionHas('kiosk_checkin_visit_id', $visit->id);

        $this->assertSame('pending', $visit->status);
        $this->assertNull($visit->visitor->email);
        $this->assertSame('1', $visit->visitor->visitor_id_card_number);
        $this->assertSame($checkinAt->format('Y-m-d H:i'), $visit->scheduled_at->format('Y-m-d H:i'));
        $this->assertSame($checkoutAt->format('Y-m-d H:i'), $visit->expected_checkout_at->format('Y-m-d H:i'));
        $this->assertNotNull($visit->qr_token);
        $this->assertTrue(Approval::query()
            ->where('visit_id', $visit->id)
            ->where('status', 'pending')
            ->exists());

        $this->withSession(['kiosk_last_visit_id' => $visit->id])
            ->get('/kiosk')
            ->assertOk()
            ->assertSee($visit->code)
            ->assertSee('Trạng thái yêu cầu gần nhất');
    }

    public function test_kiosk_requires_checkout_after_checkin(): void
    {
        $this->seed(VmsSeeder::class);
        $host = Employee::query()->where('is_active', true)->firstOrFail();
        $checkinAt = now()->addHours(2)->startOfMinute();
        $checkoutAt = $checkinAt->copy()->subHour();

        $this->from('/kiosk')->post('/kiosk/checkin/manual', [
            'registration_form' => 'kiosk_v2',
            'visitor_name' => 'Invalid Schedule',
            'visitor_phone' => '0909000111',
            'visitor_email' => 'invalid.schedule@example.test',
            'visitor_company' => 'Demo Company',
            'visitor_identity_no' => 'P87654321',
            'visitor_id_card_number' => '2',
            'host_name' => $host->name,
            'department_id' => $host->department_id ?? Department::query()->value('id'),
            'host_employee_id' => $host->id,
            'purpose' => 'Họp',
            'checkin_date' => $checkinAt->toDateString(),
            'checkin_time' => $checkinAt->format('H:i'),
            'checkout_date' => $checkoutAt->toDateString(),
            'checkout_time' => $checkoutAt->format('H:i'),
            'policy_accepted' => '1',
            'safety_acknowledged' => '1',
        ])
            ->assertRedirect('/kiosk')
            ->assertSessionHasErrors('checkout_time');
    }
    public function test_guest_can_check_in_by_visit_code_after_approval_without_camera(): void
    {
        $this->seed(VmsSeeder::class);

        $visit = Visit::query()
            ->where('status', 'approved')
            ->firstOrFail();

        $visit->update([
            'qr_expires_at' => now()->subDay(),
        ]);

        $this->post('/kiosk/checkin/scan-qr', [
            'qr_token' => $visit->code,
        ])
            ->assertRedirect(route('kiosk.checkin.status', $visit))
            ->assertSessionHas('status')
            ->assertSessionHas('kiosk_checkin_visit_id', $visit->id);

        $this->post("/kiosk/checkin/{$visit->id}/confirm")
            ->assertRedirect(route('kiosk.checkin.status', $visit))
            ->assertSessionHas('status');

        $visit->refresh();

        $this->assertSame('checked_in', $visit->status);
        $this->assertNotNull($visit->actual_checkin_at);
        $this->assertTrue(AccessControlLog::query()
            ->where('visit_id', $visit->id)
            ->where('event', 'CHECK_IN')
            ->where('source', 'kiosk')
            ->exists());
    }

    public function test_guest_can_lookup_visit_by_code_as_json_for_kiosk_modal(): void
    {
        $this->seed(VmsSeeder::class);

        $visit = Visit::query()
            ->with(['visitor', 'hostEmployee.department'])
            ->where('status', 'approved')
            ->firstOrFail();

        $response = $this->postJson('/kiosk/checkin/scan-qr', [
            'qr_token' => $visit->code,
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('ok', true)
            ->assertJsonPath('visit.code', $visit->code)
            ->assertJsonPath('visit.can_confirm', true)
            ->assertJsonPath('visit.status_label', 'Đã được duyệt')
            ->assertSessionHas('kiosk_checkin_visit_id', $visit->id)
            ->assertSessionHas('kiosk_last_visit_id', $visit->id);
    }

    public function test_kiosk_rejects_pending_visit_check_in(): void
    {
        $this->seed(VmsSeeder::class);

        $visit = Visit::query()
            ->where('status', 'pending')
            ->firstOrFail();

        $this->post('/kiosk/checkin/scan-qr', [
            'qr_token' => $visit->code,
        ])
            ->assertRedirect(route('kiosk.checkin.status', $visit))
            ->assertSessionHas('error');

        $visit->refresh();

        $this->assertSame('pending', $visit->status);
        $this->assertNull($visit->actual_checkin_at);
    }
    public function test_kiosk_accepts_manual_meeting_person_and_requires_visitor_card(): void
    {
        $this->seed(VmsSeeder::class);
        $department = Department::query()->firstOrFail();
        $checkinAt = now()->startOfMinute();
        $checkoutAt = $checkinAt->copy()->addHours(4);

        $this->post('/kiosk/checkin/manual', [
            'registration_form' => 'kiosk_v2',
            'visitor_name' => 'Manual Host Visitor',
            'visitor_phone' => '0909000222',
            'visitor_email' => '',
            'visitor_company' => 'Manual Company',
            'visitor_identity_no' => 'P-MANUAL-01',
            'visitor_id_card_number' => '9',
            'host_name' => 'Nguyen Van Ngoai',
            'department_id' => $department->id,
            'purpose' => 'Họp',
            'checkin_date' => $checkinAt->toDateString(),
            'checkin_time' => $checkinAt->format('H:i'),
            'checkout_date' => $checkoutAt->toDateString(),
            'checkout_time' => $checkoutAt->format('H:i'),
            'policy_accepted' => '1',
            'safety_acknowledged' => '1',
        ])->assertRedirect();

        $visit = Visit::query()->where('host_name', 'Nguyen Van Ngoai')->firstOrFail();
        $this->assertNull($visit->host_employee_id);
        $this->assertSame($department->id, $visit->department_id);
        $this->assertSame('9', $visit->visitor->visitor_id_card_number);
        $this->assertNull($visit->visitor->email);

        $this->from('/kiosk')->post('/kiosk/checkin/manual', [
            'registration_form' => 'kiosk_v2',
            'visitor_name' => 'Missing Card',
            'visitor_phone' => '0909000333',
            'visitor_company' => 'Manual Company',
            'visitor_identity_no' => 'P-MANUAL-02',
            'host_name' => 'Nguyen Van Ngoai',
            'department_id' => $department->id,
            'purpose' => 'Họp',
            'checkin_date' => $checkinAt->toDateString(),
            'checkin_time' => $checkinAt->format('H:i'),
            'checkout_date' => $checkoutAt->toDateString(),
            'checkout_time' => $checkoutAt->format('H:i'),
            'policy_accepted' => '1',
            'safety_acknowledged' => '1',
        ])->assertSessionHasErrors('visitor_id_card_number');
    }
}
