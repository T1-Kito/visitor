<?php

namespace Tests\Feature;

use App\Models\SystemSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicAssetSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_logo_uses_the_bundled_default_when_not_customized(): void
    {
        $settings = SystemSetting::values(SystemSetting::kioskDefaults());

        $this->assertSame('/admin-logo-default.png', $settings['admin.logo_url']);
        $this->assertFileExists(public_path('admin-logo-default.png'));

        SystemSetting::putMany(['admin.logo_url' => null]);

        $this->assertSame('/admin-logo-default.png', SystemSetting::values(SystemSetting::kioskDefaults())['admin.logo_url']);
    }

    public function test_internal_storage_urls_are_portable_between_localhost_and_lan(): void
    {
        SystemSetting::putMany([
            'admin.logo_url' => 'http://localhost:8080/storage/kiosk/logo.png',
        ]);

        $settings = SystemSetting::values(SystemSetting::kioskDefaults());

        $this->assertSame('/storage/kiosk/logo.png', $settings['admin.logo_url']);
    }
}
