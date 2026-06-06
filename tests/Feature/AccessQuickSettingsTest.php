<?php

namespace Tests\Feature;

use App\Models\SystemSetting;
use App\Models\User;
use App\Models\Visit;
use Database\Seeders\VmsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccessQuickSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_quick_access_settings(): void
    {
        $this->seed(VmsSeeder::class);

        $admin = User::query()
            ->where('email', 'superadmin@company.local')
            ->firstOrFail();

        $this->actingAs($admin)
            ->put(route('admin.access.quick-settings.update'), [
                'allow_early_checkin' => '1',
                'early_checkin_minutes' => 45,
                'late_checkin_minutes' => 90,
                'warning_enabled' => '1',
                'warning_message' => 'Vui lòng liên hệ lễ tân.',
                'return_mode' => 'checkout',
            ])
            ->assertRedirect(route('admin.access.index', ['mode' => 'checkout']))
            ->assertSessionHas('status');

        $this->assertSame('1', SystemSetting::query()->where('key', 'access.allow_early_checkin')->value('value'));
        $this->assertSame('45', SystemSetting::query()->where('key', 'access.early_checkin_minutes')->value('value'));
        $this->assertSame('0', SystemSetting::query()->where('key', 'access.allow_late_checkin')->value('value'));
        $this->assertSame('90', SystemSetting::query()->where('key', 'access.late_checkin_minutes')->value('value'));
        $this->assertSame('1', SystemSetting::query()->where('key', 'access.warning_enabled')->value('value'));
        $this->assertSame('Vui lòng liên hệ lễ tân.', SystemSetting::query()->where('key', 'access.warning_message')->value('value'));
    }

    public function test_checkin_is_blocked_before_the_configured_window(): void
    {
        $this->seed(VmsSeeder::class);

        $admin = User::query()
            ->where('email', 'superadmin@company.local')
            ->firstOrFail();
        $visit = Visit::query()
            ->where('status', 'approved')
            ->firstOrFail();

        $visit->update(['scheduled_at' => now()->addHours(2)]);

        SystemSetting::putMany([
            'access.allow_early_checkin' => '1',
            'access.early_checkin_minutes' => '30',
            'access.allow_late_checkin' => '1',
            'access.late_checkin_minutes' => '60',
            'access.warning_enabled' => '1',
            'access.warning_message' => 'Ngoài khung giờ.',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.checkin.confirm', $visit))
            ->assertSessionHas('error', fn (string $message): bool => str_contains($message, 'Ngoài khung giờ.'));

        $this->assertSame('approved', $visit->fresh()->status);
        $this->assertNull($visit->fresh()->actual_checkin_at);
    }

    public function test_quick_settings_from_mobile_returns_to_the_active_mobile_screen(): void
    {
        $this->seed(VmsSeeder::class);

        $admin = User::query()
            ->where('email', 'superadmin@company.local')
            ->firstOrFail();

        $this->actingAs($admin)
            ->put(route('admin.access.quick-settings.update'), [
                'allow_early_checkin' => '1',
                'early_checkin_minutes' => 30,
                'allow_late_checkin' => '1',
                'late_checkin_minutes' => 60,
                'warning_enabled' => '1',
                'warning_message' => 'Vui lòng liên hệ lễ tân.',
                'return_mode' => 'checkout',
                'return_mobile' => '1',
            ])
            ->assertRedirect(route('mobile.checkout'))
            ->assertSessionHas('status');
    }
}
