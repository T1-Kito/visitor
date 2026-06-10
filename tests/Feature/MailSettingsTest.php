<?php

namespace Tests\Feature;

use App\Models\SystemSetting;
use App\Models\User;
use App\Support\DynamicMailSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MailSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_save_gmail_settings_with_encrypted_password(): void
    {
        $admin = User::factory()->create();
        $admin->roles()->create(['name' => 'Admin', 'slug' => 'admin'])
            ->permissions()->create(['name' => 'Manage system', 'slug' => 'system.manage']);

        $this->actingAs($admin)
            ->put(route('admin.settings.mail.update'), [
                'from_name' => 'Cong ty ABC',
                'from_address' => 'company@gmail.com',
                'app_password' => 'abcd efgh ijkl mnop',
            ])
            ->assertRedirect(route('admin.settings.mail'));

        $storedPassword = SystemSetting::query()
            ->where('key', 'mail.password')
            ->value('value');

        $this->assertNotSame('abcdefghijklmnop', $storedPassword);
        $this->assertSame('abcdefghijklmnop', DynamicMailSettings::values()['mail.password']);
        $this->assertDatabaseHas('system_settings', [
            'key' => 'mail.from_name',
            'value' => 'Cong ty ABC',
        ]);
    }

    public function test_blank_password_keeps_the_existing_password(): void
    {
        SystemSetting::putMany([
            'mail.password' => DynamicMailSettings::encryptPassword('abcdefghijklmnop'),
        ]);

        $admin = User::factory()->create();
        $admin->roles()->create(['name' => 'Admin', 'slug' => 'admin'])
            ->permissions()->create(['name' => 'Manage system', 'slug' => 'system.manage']);

        $this->actingAs($admin)
            ->put(route('admin.settings.mail.update'), [
                'from_name' => 'Cong ty XYZ',
                'from_address' => 'xyz@gmail.com',
                'app_password' => '',
            ])
            ->assertRedirect(route('admin.settings.mail'));

        $this->assertSame('abcdefghijklmnop', DynamicMailSettings::values()['mail.password']);
    }
}
