<?php

namespace Tests\Feature;

use App\Models\SystemSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicAssetSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_internal_storage_urls_are_portable_between_localhost_and_lan(): void
    {
        SystemSetting::putMany([
            'admin.logo_url' => 'http://localhost:8080/storage/kiosk/logo.png',
        ]);

        $settings = SystemSetting::values(SystemSetting::kioskDefaults());

        $this->assertSame('/storage/kiosk/logo.png', $settings['admin.logo_url']);
    }
}
