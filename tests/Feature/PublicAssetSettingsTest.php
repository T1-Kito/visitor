<?php

namespace Tests\Feature;

use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PublicAssetSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_logo_uses_the_bundled_default_when_not_customized(): void
    {
        $settings = SystemSetting::values(SystemSetting::kioskDefaults());

        $this->assertSame('/dhl-logo-default.jpg', $settings['admin.logo_url']);
        $this->assertFileExists(public_path('dhl-logo-default.jpg'));

        SystemSetting::putMany(['admin.logo_url' => null]);

        $this->assertSame('/dhl-logo-default.jpg', SystemSetting::values(SystemSetting::kioskDefaults())['admin.logo_url']);

        $this->assertSame('/dhl-logo-default.jpg', $settings['login.logo_url']);
        $this->assertSame('/dhl-logo-default.jpg', $settings['kiosk.owner_logo_url']);
        $this->assertSame('/dhl-logo-default.jpg', $settings['kiosk.customer_logo_url']);
        $this->assertSame('/dhl-logo-default.jpg', $settings['kiosk.logo_url']);
    }

    public function test_lobby_mode_is_enabled_by_default_for_new_installations(): void
    {
        $settings = SystemSetting::values(SystemSetting::kioskDefaults());

        $this->assertSame('1', $settings['kiosk.lobby_mode_enabled']);
    }

    public function test_internal_storage_urls_are_portable_between_localhost_and_lan(): void
    {
        SystemSetting::putMany([
            'admin.logo_url' => 'http://localhost:8080/storage/kiosk/logo.png',
        ]);

        $settings = SystemSetting::values(SystemSetting::kioskDefaults());

        $this->assertSame('/storage/kiosk/logo.png', $settings['admin.logo_url']);
    }

    public function test_admin_can_replace_customer_logo_from_kiosk_settings(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create();
        $admin->roles()->create(['name' => 'Admin', 'slug' => 'admin'])
            ->permissions()->create(['name' => 'Manage system', 'slug' => 'system.manage']);

        $this->actingAs($admin)
            ->put(route('admin.settings.kiosk.update'), [
                'company_name' => 'DHL',
                'system_name' => 'VMS Kiosk',
                'subtitle' => 'Visitor management',
                'welcome_title' => 'Welcome',
                'welcome_description' => 'Please register your visit.',
                'hotline' => '1900 0000',
                'working_hours' => '07:30 - 18:00',
                'login_title' => 'Visitor Management System',
                'login_subtitle' => 'Sign in',
                'primary_color' => '#d40511',
                'secondary_color' => '#ffcc00',
                'background_color' => '#ffffff',
                'surface_color' => '#ffffff',
                'lobby_mode_enabled' => '1',
                'customer_logo_file' => UploadedFile::fake()->image('customer-logo.jpg', 240, 80),
            ])
            ->assertRedirect(route('admin.settings.kiosk'));

        $storedUrl = SystemSetting::query()
            ->where('key', 'kiosk.customer_logo_url')
            ->value('value');

        $this->assertIsString($storedUrl);
        $this->assertStringStartsWith('/storage/kiosk/customer-logo-', $storedUrl);
        Storage::disk('public')->assertExists(ltrim(str_replace('/storage/', '', $storedUrl), '/'));
    }
}
