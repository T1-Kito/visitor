<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\VmsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardEndpointsTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_json_endpoints_return_operational_data(): void
    {
        $this->seed(VmsSeeder::class);

        $user = User::query()
            ->where('email', 'superadmin@company.local')
            ->firstOrFail();

        $this->actingAs($user)
            ->getJson('/dashboard/summary')
            ->assertOk()
            ->assertJsonStructure([
                'date',
                'today_visits',
                'in_company',
                'pending',
                'approved',
                'checked_out_today',
                'rejected_today',
                'overstay',
                'updated_at',
            ]);

        $this->actingAs($user)
            ->getJson('/dashboard/live-visitors')
            ->assertOk()
            ->assertJsonStructure([
                'data',
                'updated_at',
            ]);

        $this->actingAs($user)
            ->getJson('/dashboard/alerts')
            ->assertOk()
            ->assertJsonStructure([
                'data',
                'updated_at',
            ]);
    }
}
