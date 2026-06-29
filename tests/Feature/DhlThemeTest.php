<?php

namespace Tests\Feature;

use App\Models\SystemSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DhlThemeTest extends TestCase
{
    use RefreshDatabase;

    public function test_dhl_colors_are_the_system_defaults(): void
    {
        $admin = SystemSetting::adminThemeDefaults();
        $kiosk = SystemSetting::values(SystemSetting::kioskDefaults());

        $this->assertSame('#ffcc00', $admin['admin.navbar_color']);
        $this->assertSame('#ffffff', $admin['admin.content_background']);
        $this->assertSame('#d40511', $admin['admin.primary_color']);
        $this->assertSame('#ffcc00', $admin['admin.secondary_color']);
        $this->assertSame('#d40511', $kiosk['kiosk.primary_color']);
        $this->assertSame('#ffcc00', $kiosk['kiosk.secondary_color']);
        $this->assertSame('#ffffff', $kiosk['kiosk.background_color']);
        $this->assertSame('#ffffff', $kiosk['kiosk.surface_color']);
    }

    public function test_registration_form_links_to_dhl_privacy_notice(): void
    {
        $this->get(route('kiosk.register'))
            ->assertOk()
            ->assertSee('Privacy Notice - DHL - Global')
            ->assertSee(route('kiosk.privacy-notice'));
    }
}