<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\User;
use App\Models\Visit;
use App\Models\Visitor;
use Carbon\Carbon;
use Database\Seeders\AdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccessListDateFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_access_list_rows_and_summary_cards_use_the_selected_date_range(): void
    {
        $this->travelTo(Carbon::parse('2026-07-23 09:00:00'));
        $this->seed(AdminSeeder::class);

        $admin = User::query()->where('email', 'admin@company.local')->firstOrFail();
        $department = Department::query()->create([
            'code' => 'OPS',
            'name' => 'Vận hành',
        ]);

        $oldCurrentVisit = $this->createAccessVisit(
            'OLD-CURRENT',
            $department,
            'checked_in',
            '2026-07-22 14:34:00',
        );
        $rangeVisit = $this->createAccessVisit(
            'IN-RANGE',
            $department,
            'checked_out',
            '2026-07-01 09:00:00',
            '2026-07-01 10:00:00',
        );
        $todayVisit = $this->createAccessVisit(
            'TODAY-CURRENT',
            $department,
            'checked_in',
            '2026-07-23 08:00:00',
        );

        $response = $this->actingAs($admin)->get(route('admin.access.lists', [
            'type' => 'all',
            'from' => '2026-07-01',
            'to' => '2026-07-01',
        ]));

        $response
            ->assertOk()
            ->assertSeeText('Khách vào theo lọc')
            ->assertSeeText('Khách ra theo lọc')
            ->assertSeeText($rangeVisit->code)
            ->assertDontSeeText($oldCurrentVisit->code)
            ->assertDontSeeText($todayVisit->code)
            ->assertViewHas('listStats', fn (array $stats): bool => $stats === [
                'inside' => 2,
                'in_range' => 1,
                'out_range' => 1,
                'all_range' => 1,
            ]);

        foreach (['in', 'out'] as $type) {
            $this->actingAs($admin)
                ->get(route('admin.access.lists', [
                    'type' => $type,
                    'from' => '2026-07-01',
                    'to' => '2026-07-01',
                ]))
                ->assertOk()
                ->assertSeeText($rangeVisit->code)
                ->assertDontSeeText($oldCurrentVisit->code)
                ->assertDontSeeText($todayVisit->code)
                ->assertViewHas('listStats', fn (array $stats): bool => $stats['all_range'] === 1
                    && $stats['in_range'] === 1
                    && $stats['out_range'] === 1);
        }

        $this->actingAs($admin)
            ->get(route('admin.access.lists', [
                'type' => 'inside',
                'from' => '2026-07-01',
                'to' => '2026-07-01',
            ]))
            ->assertOk()
            ->assertSeeText($oldCurrentVisit->code)
            ->assertSeeText($todayVisit->code)
            ->assertDontSeeText($rangeVisit->code)
            ->assertViewHas('listStats', fn (array $stats): bool => $stats['inside'] === 2
                && $stats['all_range'] === 1);
    }

    private function createAccessVisit(
        string $code,
        Department $department,
        string $status,
        string $checkinAt,
        ?string $checkoutAt = null,
    ): Visit {
        $visitor = Visitor::query()->create([
            'full_name' => 'Khách '.$code,
        ]);

        return Visit::query()->create([
            'code' => $code,
            'visitor_id' => $visitor->id,
            'host_name' => 'Người tiếp '.$code,
            'department_id' => $department->id,
            'scheduled_at' => $checkinAt,
            'expected_checkout_at' => Carbon::parse($checkinAt)->addHours(2),
            'actual_checkin_at' => $checkinAt,
            'actual_checkout_at' => $checkoutAt,
            'status' => $status,
            'purpose' => 'Kiểm tra bộ lọc',
            'checkin_method' => 'manual',
        ]);
    }
}
