<?php

namespace Tests\Feature;

use App\Models\SystemSetting;
use App\Models\User;
use App\Support\DynamicMailSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OnlineRegistrationEmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_is_told_to_configure_gmail_before_sending(): void
    {
        $admin = $this->visitsAdmin();
        SystemSetting::putMany([
            'mail.username' => null,
            'mail.password' => null,
            'mail.from_address' => null,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.online-registration.send-email'), [
                'recipient_email' => 'visitor@example.com',
            ])
            ->assertSessionHasErrors('recipient_email');
    }

    public function test_admin_can_send_registration_link_with_configured_gmail(): void
    {
        Mail::shouldReceive('purge')->once()->with('smtp');
        Mail::shouldReceive('html')->once();
        $admin = $this->visitsAdmin();
        SystemSetting::putMany([
            'mail.host' => 'smtp.gmail.com',
            'mail.port' => '587',
            'mail.scheme' => 'smtp',
            'mail.auth_mode' => 'login',
            'mail.username' => 'sender@gmail.com',
            'mail.password' => DynamicMailSettings::encryptPassword('gmail-app-password'),
            'mail.from_address' => 'sender@gmail.com',
            'mail.from_name' => 'VMS Test',
            'mail.timeout' => '30',
        ]);

        $this->actingAs($admin)
            ->withServerVariables(['HTTP_HOST' => 'vms.customer.test'])
            ->post(route('admin.online-registration.send-email'), [
                'recipient_email' => 'visitor@example.com',
            ])
            ->assertSessionHas('status');

    }

    private function visitsAdmin(): User
    {
        $admin = User::factory()->create();
        $admin->roles()->create(['name' => 'Admin', 'slug' => 'admin'])
            ->permissions()->create(['name' => 'Manage visits', 'slug' => 'visits.manage']);

        return $admin;
    }
}