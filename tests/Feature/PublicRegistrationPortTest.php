<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\Visit;
use Database\Seeders\VmsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicRegistrationPortTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_port_only_allows_registration_endpoints(): void
    {
        $this->get('http://115.73.209.88:8443/kiosk/register')->assertOk();
        $this->get('http://115.73.209.88:8443/kiosk/privacy-notice')
            ->assertRedirect('https://www.dhl.com/global-en/home/footer/privacy-notice.html');
        $this->get('http://115.73.209.88:8443/login')->assertNotFound();
        $this->get('http://115.73.209.88:8443/kiosk')->assertNotFound();
        $this->get('http://115.73.209.88:8443/dashboard')->assertNotFound();
    }

    public function test_public_registration_shows_success_then_returns_to_the_same_form(): void
    {
        $this->seed(VmsSeeder::class);
        $host = Employee::query()->where('is_active', true)->firstOrFail();

        $response = $this->post('http://115.73.209.88:8443/kiosk/checkin/manual', [
            'visitor_name' => 'Khach Ngoai Mang',
            'visitor_phone' => '0909000123',
            'visitor_email' => 'external@example.test',
            'visitor_company' => 'Cong ty External',
            'host_employee_id' => $host->id,
            'host_name' => $host->name,
            'department_id' => $host->department_id,
            'purpose' => 'Hop',
            'expected_checkout_time' => now()->addHours(2)->format('H:i'),
            'policy_accepted' => '1',
            'safety_acknowledged' => '1',
        ]);

        $visit = Visit::query()->whereHas('visitor', fn ($query) => $query->where('email', 'external@example.test'))->firstOrFail();

        $response
            ->assertRedirect(route('kiosk.checkin.status', $visit))
            ->assertSessionHas('status')
            ->assertSessionHas('kiosk_success_return_url', route('kiosk.register'));

        $this->withSession(['kiosk_success_return_url' => route('kiosk.register')])
            ->get('http://115.73.209.88:8443/kiosk/checkin/status/'.$visit->id)
            ->assertOk()
            ->assertSee('id="ksReturnCountdown">30', false);
    }

    public function test_internal_port_keeps_normal_routes_available(): void
    {
        $this->get('http://127.0.0.1:8080/login')->assertOk();
        $this->get('http://127.0.0.1:8080/kiosk')->assertOk();
    }
}
