<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\User;
use App\Models\Visit;
use App\Models\Visitor;
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

    public function test_reports_show_manual_host_and_visit_department(): void
    {
        $this->seed(VmsSeeder::class);

        $user = User::query()->where('email', 'superadmin@company.local')->firstOrFail();
        $department = Department::query()->firstOrFail();
        $visitor = Visitor::query()->create(['full_name' => 'Khach Bao Cao']);
        $visit = Visit::query()->create([
            'code' => 'REPORT-MANUAL-HOST',
            'visitor_id' => $visitor->id,
            'host_employee_id' => null,
            'host_name' => 'Nguyen Nguoi Tiep',
            'department_id' => $department->id,
            'scheduled_at' => now(),
            'expected_checkout_at' => now()->addHour(),
            'status' => 'checked_in',
            'purpose' => 'Kiem tra bao cao',
            'checkin_method' => 'manual',
        ]);

        $this->actingAs($user)
            ->get(route('admin.reports.index'))
            ->assertOk()
            ->assertSeeText('Nguyen Nguoi Tiep')
            ->assertSeeText($department->name);

        $this->actingAs($user)
            ->getJson(route('admin.reports.by-host'))
            ->assertOk()
            ->assertJsonFragment([
                'host' => 'Nguyen Nguoi Tiep',
                'department' => $department->name,
            ]);

        $this->assertSame('Nguyen Nguoi Tiep', $visit->host_display_name);
    }
}
