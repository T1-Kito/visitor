<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\AdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_only_one_admin_user_with_all_permissions(): void
    {
        $this->seed(AdminSeeder::class);
        $this->seed(AdminSeeder::class);

        $this->assertSame(1, User::query()->count());

        $admin = User::query()->with('roles.permissions')->sole();

        $this->assertSame('admin@company.local', $admin->email);
        $this->assertTrue($admin->is_active);
        $this->assertSame(['admin'], $admin->roles->pluck('slug')->all());
        $this->assertCount(11, $admin->roles->first()->permissions);
    }
}
