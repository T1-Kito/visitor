<?php

namespace Tests\Feature;

use App\Models\AccessControlLog;
use App\Models\Approval;
use App\Models\Employee;
use App\Models\User;
use App\Models\Visit;
use Database\Seeders\VmsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KioskFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_create_walk_in_request_from_kiosk(): void
    {
        $this->seed(VmsSeeder::class);

        $host = Employee::query()->where('is_active', true)->firstOrFail();

        $response = $this->post('/kiosk/checkin/manual', [
            'visitor_name' => 'Khach Kiosk Demo',
            'visitor_phone' => '0909000999',
            'visitor_email' => 'kiosk.demo@example.test',
            'visitor_company' => 'Demo Company',
            'host_employee_id' => $host->id,
            'purpose' => 'Họp',
            'expected_checkout_time' => now()->addHours(2)->format('H:i'),
            'visitor_note' => 'Tạo từ kiosk',
            'policy_accepted' => '1',
        ]);

        $visit = Visit::query()->whereHas('visitor', function ($query): void {
            $query->where('email', 'kiosk.demo@example.test');
        })->firstOrFail();

        $response
            ->assertRedirect(route('kiosk.checkin.status', $visit))
            ->assertSessionHas('status')
            ->assertSessionHas('kiosk_checkin_visit_id', $visit->id);

        $this->assertSame('pending', $visit->status);
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
}
