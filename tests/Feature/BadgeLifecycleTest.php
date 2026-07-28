<?php

namespace Tests\Feature;

use App\Models\Badge;
use App\Models\Visit;
use App\Support\BadgeLifecycle;
use Database\Seeders\VmsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BadgeLifecycleTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkin_uses_the_selected_badge_only_once_and_checkout_returns_it_to_the_pool(): void
    {
        $this->seed(VmsSeeder::class);

        $visit = Visit::query()
            ->with('visitor')
            ->where('status', 'approved')
            ->firstOrFail();
        $selectedBadge = Badge::query()
            ->where('status', 'available')
            ->orderByDesc('id')
            ->firstOrFail();

        $visit->update(['requested_badge_id' => $selectedBadge->id]);
        $visit->visitor->update(['visitor_id_card_number' => $selectedBadge->badge_no]);

        $firstCheckin = app(BadgeLifecycle::class)->checkIn($visit, now());
        $repeatedCheckin = app(BadgeLifecycle::class)->checkIn($visit, now()->addSecond());

        $this->assertSame('checked_in', $firstCheckin['result']);
        $this->assertSame($selectedBadge->id, $firstCheckin['badge']?->id);
        $this->assertSame('already_checked_in', $repeatedCheckin['result']);
        $this->assertSame($selectedBadge->id, $repeatedCheckin['badge']?->id);
        $this->assertSame(1, Badge::query()
            ->where('visit_id', $visit->id)
            ->where('status', 'active')
            ->count());

        $checkout = app(BadgeLifecycle::class)->checkOut($visit, now()->addHour());

        $this->assertSame('checked_out', $checkout['result']);
        $this->assertDatabaseHas('visits', [
            'id' => $visit->id,
            'status' => 'checked_out',
        ]);
        $this->assertDatabaseHas('badges', [
            'id' => $selectedBadge->id,
            'visit_id' => null,
            'status' => 'available',
            'issued_at' => null,
            'revoked_at' => null,
            'valid_until' => null,
        ]);
    }

    public function test_unavailable_selected_badge_does_not_check_the_visit_in_or_substitute_another_card(): void
    {
        $this->seed(VmsSeeder::class);

        $visit = Visit::query()
            ->with('visitor')
            ->where('status', 'approved')
            ->firstOrFail();
        $selectedBadge = Badge::query()
            ->where('status', 'available')
            ->firstOrFail();

        $selectedBadge->update(['status' => 'revoked']);
        $visit->update(['requested_badge_id' => $selectedBadge->id]);
        $visit->visitor->update(['visitor_id_card_number' => $selectedBadge->badge_no]);

        $result = app(BadgeLifecycle::class)->checkIn($visit, now());

        $this->assertSame('requested_badge_unavailable', $result['result']);
        $this->assertSame($selectedBadge->badge_no, $result['requested_badge_no']);
        $this->assertDatabaseHas('visits', [
            'id' => $visit->id,
            'status' => 'approved',
            'actual_checkin_at' => null,
        ]);
        $this->assertDatabaseMissing('badges', [
            'visit_id' => $visit->id,
            'status' => 'active',
        ]);
    }

    public function test_manual_visit_without_a_requested_card_can_still_check_in_when_the_pool_is_empty(): void
    {
        $this->seed(VmsSeeder::class);

        $visit = Visit::query()
            ->with('visitor')
            ->where('status', 'approved')
            ->firstOrFail();

        $visit->update(['requested_badge_id' => null]);
        $visit->visitor->update(['visitor_id_card_number' => null]);
        Badge::query()->update(['status' => 'revoked']);

        $result = app(BadgeLifecycle::class)->checkIn($visit, now());

        $this->assertSame('checked_in', $result['result']);
        $this->assertNull($result['badge']);
        $this->assertDatabaseHas('visits', [
            'id' => $visit->id,
            'status' => 'checked_in',
        ]);
    }

    public function test_badge_repair_migration_is_safe_to_retry_after_a_partial_install(): void
    {
        $migration = require database_path('migrations/2026_07_27_000001_fix_badge_lifecycle_and_visit_assignment.php');

        $migration->up();
        $migration->up();

        $this->assertTrue(\Illuminate\Support\Facades\Schema::hasColumn('visits', 'requested_badge_id'));
        $this->assertTrue(\Illuminate\Support\Facades\Schema::hasIndex('badges', 'badges_visit_id_unique', 'unique'));
    }
}
