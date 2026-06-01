<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\User;
use Database\Seeders\VmsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_and_mark_notifications_as_read(): void
    {
        $this->seed(VmsSeeder::class);

        $user = User::query()
            ->where('email', 'superadmin@company.local')
            ->firstOrFail();

        $notification = Notification::query()->create([
            'user_id' => $user->id,
            'type' => 'test.notification',
            'level' => 'info',
            'title' => 'Thong bao test',
            'message' => 'Noi dung thong bao test.',
            'action_url' => route('admin.dashboard'),
        ]);

        $this->actingAs($user)
            ->get('/notifications')
            ->assertOk()
            ->assertSee('Thong bao test');

        $this->actingAs($user)
            ->getJson('/notifications/unread-count')
            ->assertOk()
            ->assertJson([
                'unread_count' => Notification::query()
                    ->where('user_id', $user->id)
                    ->whereNull('read_at')
                    ->count(),
            ]);

        $this->actingAs($user)
            ->patch("/notifications/{$notification->id}/read")
            ->assertRedirect(route('admin.dashboard'));

        $this->assertNotNull($notification->fresh()->read_at);

        Notification::query()->create([
            'user_id' => $user->id,
            'type' => 'test.notification',
            'level' => 'warning',
            'title' => 'Thong bao test 2',
            'message' => 'Noi dung thong bao test 2.',
        ]);

        $this->actingAs($user)
            ->patch('/notifications/read-all')
            ->assertRedirect(route('admin.notifications.index'));

        $this->assertSame(
            0,
            Notification::query()
                ->where('user_id', $user->id)
                ->whereNull('read_at')
                ->count()
        );
    }
}
