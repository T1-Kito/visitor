<?php

namespace Tests\Feature;

use App\Models\AccessControlLog;
use App\Models\User;
use App\Models\Visit;
use Database\Seeders\VmsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckinCheckoutFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_check_in_and_check_out_by_visit_code_without_camera(): void
    {
        $this->seed(VmsSeeder::class);

        $admin = User::query()
            ->where('email', 'superadmin@company.local')
            ->firstOrFail();

        $visit = Visit::query()
            ->where('status', 'approved')
            ->firstOrFail();

        $visit->update([
            'qr_expires_at' => now()->subDay(),
        ]);

        $this->actingAs($admin)
            ->post('/checkin/scan-qr', [
                'qr_token' => $visit->code,
            ])
            ->assertRedirect('/checkin')
            ->assertSessionHas('checkin_scanned_visit_id', $visit->id)
            ->assertSessionHas('status');

        $this->actingAs($admin)
            ->post("/checkin/{$visit->id}/confirm")
            ->assertRedirect()
            ->assertSessionHas('status');

        $visit->refresh();

        $this->assertSame('checked_in', $visit->status);
        $this->assertNotNull($visit->actual_checkin_at);
        $this->assertTrue(AccessControlLog::query()
            ->where('visit_id', $visit->id)
            ->where('event', 'CHECK_IN')
            ->exists());

        $this->actingAs($admin)
            ->post('/checkout/scan-qr', [
                'qr_token' => $visit->code,
            ])
            ->assertRedirect('/checkout')
            ->assertSessionHas('checkout_scanned_visit_id', $visit->id)
            ->assertSessionHas('status');

        $this->actingAs($admin)
            ->post("/checkout/{$visit->id}/confirm")
            ->assertRedirect()
            ->assertSessionHas('status');

        $visit->refresh();

        $this->assertSame('checked_out', $visit->status);
        $this->assertNotNull($visit->actual_checkout_at);
        $this->assertTrue(AccessControlLog::query()
            ->where('visit_id', $visit->id)
            ->where('event', 'CHECK_OUT')
            ->exists());
    }

    public function test_checkout_rejects_visit_that_has_not_checked_in(): void
    {
        $this->seed(VmsSeeder::class);

        $admin = User::query()
            ->where('email', 'superadmin@company.local')
            ->firstOrFail();

        $visit = Visit::query()
            ->where('status', 'approved')
            ->firstOrFail();

        $this->actingAs($admin)
            ->post('/checkout/scan-qr', [
                'qr_token' => $visit->code,
            ])
            ->assertRedirect('/checkout')
            ->assertSessionHas('error');

        $visit->refresh();

        $this->assertSame('approved', $visit->status);
        $this->assertNull($visit->actual_checkout_at);
    }
}
