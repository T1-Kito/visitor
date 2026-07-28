<?php

namespace Tests\Feature;

use App\Models\Badge;
use App\Models\Department;
use App\Models\Visit;
use App\Models\Visitor;
use App\Support\BadgeLifecycle;
use Database\Seeders\VmsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KioskVisitorCardRefreshTest extends TestCase
{
    use RefreshDatabase;

    public function test_available_card_endpoint_reflects_checkout_without_reloading_the_form(): void
    {
        $this->seed(VmsSeeder::class);

        $visit = Visit::query()
            ->with('visitor')
            ->where('status', 'approved')
            ->firstOrFail();
        $badge = Badge::query()
            ->where('status', 'available')
            ->orderByDesc('id')
            ->firstOrFail();

        $badge->update([
            'label_vi' => 'Thẻ khách vừa trả',
            'label_en' => 'Returned visitor card',
        ]);
        $visit->update(['requested_badge_id' => $badge->id]);
        $visit->visitor->update(['visitor_id_card_number' => $badge->badge_no]);

        $this->assertSame('checked_in', app(BadgeLifecycle::class)->checkIn($visit, now())['result']);

        $whileActive = $this->getJson(route('kiosk.visitor-cards'))->assertOk();
        $this->assertNotContains($badge->badge_no, collect($whileActive->json('data'))->pluck('value')->all());
        $this->assertStringContainsString('no-store', (string) $whileActive->headers->get('Cache-Control'));

        $this->assertSame('checked_out', app(BadgeLifecycle::class)->checkOut($visit, now()->addHour())['result']);

        $this->getJson(route('kiosk.visitor-cards'))
            ->assertOk()
            ->assertJsonFragment([
                'value' => $badge->badge_no,
                'label_vi' => 'Thẻ khách vừa trả',
                'label_en' => 'Returned visitor card',
            ]);
    }

    public function test_endpoint_returns_an_empty_list_instead_of_fake_cards_when_none_are_available(): void
    {
        $this->seed(VmsSeeder::class);

        Badge::query()->update(['status' => 'revoked']);

        $this->getJson(route('kiosk.visitor-cards'))
            ->assertOk()
            ->assertExactJson(['data' => []]);
    }

    public function test_stale_card_selection_is_rejected_before_creating_a_visit(): void
    {
        $this->seed(VmsSeeder::class);

        $badge = Badge::query()
            ->where('status', 'available')
            ->firstOrFail();
        $department = Department::query()->firstOrFail();
        $visitorCount = Visitor::query()->count();
        $visitCount = Visit::query()->count();
        $checkin = now()->addMinute();
        $checkout = $checkin->copy()->addHours(4);

        $badge->update(['status' => 'active']);

        $this->post(route('kiosk.checkin.manual'), [
            'registration_form' => 'kiosk_v2',
            'visitor_name' => 'Khách chọn thẻ cũ',
            'visitor_phone' => '0909000999',
            'visitor_company' => 'Test Company',
            'visitor_identity_no' => 'TEST-CARD-STALE',
            'visitor_id_card_number' => $badge->badge_no,
            'host_name' => 'Người tiếp khách',
            'department_id' => $department->id,
            'purpose' => 'Họp',
            'checkin_date' => $checkin->toDateString(),
            'checkin_time' => $checkin->format('H:i'),
            'checkout_date' => $checkout->toDateString(),
            'checkout_time' => $checkout->format('H:i'),
            'policy_accepted' => '1',
            'safety_acknowledged' => '1',
        ])
            ->assertSessionHasErrors('visitor_id_card_number');

        $this->assertSame($visitorCount, Visitor::query()->count());
        $this->assertSame($visitCount, Visit::query()->count());
    }

    public function test_kiosk_form_exposes_the_live_card_endpoint_and_refresh_hooks(): void
    {
        $this->seed(VmsSeeder::class);

        $this->get(route('kiosk.index'))
            ->assertOk()
            ->assertSee('id="kioskVisitorCardSelect"', false)
            ->assertSee('data-options-url="'.route('kiosk.visitor-cards').'"', false)
            ->assertSee('kiosk:options-updated', false)
            ->assertSee('refreshVisitorCards', false);
    }
}
