<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\VmsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportsEndpointsTest extends TestCase
{
    use RefreshDatabase;

    public function test_reports_endpoints_return_data_and_export_csv(): void
    {
        $this->seed(VmsSeeder::class);

        $user = User::query()
            ->where('email', 'superadmin@company.local')
            ->firstOrFail();

        foreach ([
            '/reports/visits',
            '/reports/by-department',
            '/reports/by-host',
            '/reports/current-visitors',
            '/reports/overstay',
            '/reports/rejected',
            '/reports/emergency-current-visitors',
        ] as $uri) {
            $this->actingAs($user)
                ->getJson($uri)
                ->assertOk()
                ->assertJsonStructure(['data']);
        }

        $this->actingAs($user)
            ->get('/reports/visits/export?type=visits')
            ->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=UTF-8');

        $xlsxResponse = $this->actingAs($user)
            ->get('/reports/visits/export-xlsx?type=visits')
            ->assertOk()
            ->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $this->assertStringStartsWith('PK', $xlsxResponse->streamedContent());
    }

    public function test_mobile_reports_page_renders_with_filters_and_summary(): void
    {
        $this->seed(VmsSeeder::class);

        $user = User::query()
            ->where('email', 'superadmin@company.local')
            ->firstOrFail();

        $this->actingAs($user)
            ->get(route('mobile.reports', [
                'from_date' => now()->startOfMonth()->toDateString(),
                'to_date' => now()->toDateString(),
                'status' => 'all',
            ]))
            ->assertOk()
            ->assertSeeText('Báo cáo')
            ->assertSeeText('Tổng lượt khách')
            ->assertSeeText('Hoạt động 7 ngày gần nhất')
            ->assertSeeText('Lượt khách gần nhất');

        $this->actingAs($user)
            ->get(route('mobile.home'))
            ->assertOk()
            ->assertSee(route('mobile.reports'), false);
    }
}
