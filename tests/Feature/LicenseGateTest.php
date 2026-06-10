<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Config;
use App\Support\LicenseManager;
use Tests\TestCase;

class LicenseGateTest extends TestCase
{
    public function test_unlicensed_install_can_use_system_during_trial_when_enforced(): void
    {
        $this->enableLicenseGate();

        $this->get('/dashboard')
            ->assertStatus(302)
            ->assertRedirect(route('login'));

        $this->get(route('license.show'))
            ->assertOk()
            ->assertSee('Kích hoạt bản quyền')
            ->assertSee('Dùng thử')
            ->assertSee('VMS-');
    }

    public function test_unlicensed_install_redirects_to_activation_page_after_trial_expires(): void
    {
        $this->enableLicenseGate();
        $this->writeTrialStartedAt(now()->subDays(20)->toDateString());

        $this->get('/dashboard')
            ->assertRedirect(route('license.show'));
    }

    public function test_json_requests_receive_locked_response_after_trial_expires(): void
    {
        $this->enableLicenseGate();
        $this->writeTrialStartedAt(now()->subDays(20)->toDateString());

        $this->getJson('/dashboard/summary')
            ->assertStatus(423);
    }

    public function test_activation_rejects_invalid_license_text(): void
    {
        $this->enableLicenseGate();

        $this->post(route('license.store'), [
            'license_text' => json_encode([
                'payload' => [
                    'product' => 'khach-moi-vms',
                    'device_id' => 'VMS-ABCDEF-ABCDEF-ABCDEF-ABCDEF',
                ],
                'signature' => 'invalid',
            ]),
        ])->assertSessionHas('error', 'Chữ ký bản quyền không hợp lệ.');
    }

    public function test_status_reads_existing_license_even_when_enforcement_is_disabled(): void
    {
        $this->enableLicenseGate();
        Config::set('license.enforced', false);

        $manager = app(LicenseManager::class);
        $document = $this->signedLicenseDocument($manager->deviceId(), 'Khách Test', '2026-12-31');
        $manager->install($document);

        $status = $manager->status();

        $this->assertTrue($status['valid']);
        $this->assertSame('Khách Test', $status['customer']);
        $this->assertSame('2026-12-31', $status['expires_at']);
    }

    private function enableLicenseGate(): void
    {
        $base = storage_path('framework/testing/license-'.spl_object_id($this));

        Config::set('license.enforced', true);
        Config::set('license.device_id_path', $base.'/device-id.txt');
        Config::set('license.storage_path', $base.'/license.json');
        Config::set('license.trial_started_at_path', $base.'/trial-started-at.txt');
    }

    private function writeTrialStartedAt(string $date): void
    {
        $path = (string) config('license.trial_started_at_path');
        if (! is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        file_put_contents($path, $date);
    }

    /**
     * @return array<string, mixed>
     */
    private function signedLicenseDocument(string $deviceId, string $customer, string $expiresAt): array
    {
        $privateKey = file_get_contents(base_path('license-issuer/private/license-private.pem'));
        $payload = [
            'product' => 'khach-moi-vms',
            'license_id' => 'LIC-TEST1234567890',
            'customer' => $customer,
            'device_id' => $deviceId,
            'edition' => 'standard',
            'features' => ['core'],
            'issued_at' => '2026-06-08T00:00:00Z',
            'expires_at' => $expiresAt,
        ];

        openssl_sign(
            json_encode($this->sortKeys($payload), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            $signature,
            $privateKey,
            OPENSSL_ALGO_SHA256,
        );

        return [
            'payload' => $payload,
            'signature' => rtrim(strtr(base64_encode($signature), '+/', '-_'), '='),
        ];
    }

    /**
     * @param  mixed  $value
     * @return mixed
     */
    private function sortKeys($value)
    {
        if (! is_array($value)) {
            return $value;
        }

        if (! array_is_list($value)) {
            ksort($value);
        }

        foreach ($value as $key => $item) {
            $value[$key] = $this->sortKeys($item);
        }

        return $value;
    }
}
