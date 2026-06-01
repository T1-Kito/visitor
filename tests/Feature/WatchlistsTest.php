<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\User;
use App\Models\Visit;
use App\Models\Watchlist;
use Database\Seeders\VmsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WatchlistsTest extends TestCase
{
    use RefreshDatabase;

    public function test_security_user_can_manage_watchlist_and_visit_creation_triggers_match_notification(): void
    {
        $this->seed(VmsSeeder::class);

        $security = User::query()
            ->where('email', 'security.admin@company.local')
            ->firstOrFail();
        $admin = User::query()
            ->where('email', 'superadmin@company.local')
            ->firstOrFail();

        $this->actingAs($security)
            ->post('/watchlists', [
                'keyword' => 'Nguyen',
                'match_type' => 'name',
                'level' => 'critical',
                'status' => 'active',
                'reason' => 'Can theo doi khi vao cong ty.',
                'note' => 'Test rule.',
            ])
            ->assertRedirect();

        $watchlist = Watchlist::query()->where('keyword', 'Nguyen')->firstOrFail();

        $this->actingAs($security)
            ->get("/watchlists/{$watchlist->id}")
            ->assertOk()
            ->assertSee('Nguyen');

        $hostVisit = Visit::query()
            ->with(['visitor', 'hostEmployee'])
            ->firstOrFail();

        $this->actingAs($admin)
            ->post('/visits', [
                'visitor_name' => 'Nguyen Watch Match',
                'visitor_phone' => '0909000111',
                'visitor_email' => 'watch@example.test',
                'visitor_company' => 'Watch Company',
                'host_employee_id' => $hostVisit->host_employee_id,
                'visit_date' => now()->addDay()->toDateString(),
                'visit_time' => '09:00',
                'expected_checkout_time' => '10:30',
                'purpose' => 'Kiem tra watchlist',
                'checkin_method' => 'qr',
            ])
            ->assertRedirectToRoute('admin.visits.show', Visit::query()->latest('id')->first());

        $this->assertTrue(Notification::query()
            ->where('type', 'watchlist.match')
            ->where('title', 'Khach match watchlist')
            ->exists());
    }
}
