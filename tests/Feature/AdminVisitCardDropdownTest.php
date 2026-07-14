<?php

namespace Tests\Feature;

use App\Models\Badge;
use App\Models\User;
use App\Models\Visit;
use Database\Seeders\VmsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminVisitCardDropdownTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_visit_uses_available_admin_cards_with_bilingual_labels(): void
    {
        $this->seed(VmsSeeder::class);

        $admin = User::query()->where('email', 'superadmin@company.local')->firstOrFail();
        $availableCard = Badge::query()->where('status', 'available')->firstOrFail();
        $availableCard->update([
            'badge_no' => 'CARD-BILINGUAL',
            'label_vi' => 'The khach song ngu',
            'label_en' => 'Bilingual visitor card',
        ]);

        $unavailableCard = Badge::query()->whereKeyNot($availableCard->id)->firstOrFail();
        $unavailableCard->update([
            'badge_no' => 'CARD-IN-USE',
            'label_vi' => 'The dang su dung',
            'label_en' => 'Card in use',
            'status' => 'active',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.visits.create'))
            ->assertOk()
            ->assertSee('<select id="visitorIdCardNumber"', false)
            ->assertSee('value="CARD-BILINGUAL"', false)
            ->assertSee('VI: The khach song ngu — EN: Bilingual visitor card')
            ->assertDontSee('CARD-IN-USE');
    }

    public function test_edit_visit_keeps_its_current_card_even_when_card_is_in_use(): void
    {
        $this->seed(VmsSeeder::class);

        $admin = User::query()->where('email', 'superadmin@company.local')->firstOrFail();
        $visit = Visit::query()
            ->whereNotIn('status', ['checked_in', 'checked_out', 'cancelled'])
            ->with('visitor')
            ->firstOrFail();
        $card = Badge::query()->firstOrFail();
        $card->update([
            'badge_no' => 'CURRENT-CARD',
            'label_vi' => 'The hien tai',
            'label_en' => 'Current card',
            'status' => 'active',
            'visit_id' => $visit->id,
        ]);
        $visit->visitor->update(['visitor_id_card_number' => $card->badge_no]);

        $this->actingAs($admin)
            ->get(route('admin.visits.edit', $visit))
            ->assertOk()
            ->assertSee('value="CURRENT-CARD" selected', false)
            ->assertSee('VI: The hien tai — EN: Current card');
    }
}
