<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\AdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_settings_dashboard(): void
    {
        $this->seed(AdminSeeder::class);
        $admin = User::query()->where('email', 'admin@company.local')->sole();

        $this->actingAs($admin)
            ->get(route('admin.settings.index'))
            ->assertOk()
            ->assertSeeText('Kiosk & thương hiệu', false)
            ->assertSeeText('Máy in')
            ->assertSeeText('Phân quyền')
            ->assertSeeText('Tài khoản nhân viên')
            ->assertSeeText('Nhật ký hệ thống');
    }
}
