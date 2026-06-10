<?php

namespace App\Support;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class LicenseManager
{
    /**
     * @return array{enabled: bool, valid: bool, status: string, message: string, device_id: string, payload: array<string, mixed>|null, expires_at: string|null, customer: string|null, days_remaining: int|null, trial_started_at: string|null, trial_ends_at: string|null, trial_days_remaining: int|null}
     */
    public function status(): array
    {
        $license = $this->readLicense();
        if ($license === null) {
            if (! $this->isEnforced()) {
                return $this->statusPayload(true, 'disabled', 'Chưa bật bắt buộc bản quyền.', null);
            }

            $trial = $this->trialStatus();
            if ($trial['valid']) {
                return $this->statusPayload(true, 'trial', 'Đang trong thời gian dùng thử.', null, $trial);
            }

            return $this->statusPayload(false, 'trial_expired', 'Thời gian dùng thử đã hết. Vui lòng kích hoạt bản quyền.', null, $trial);
        }

        $validation = $this->validateDocument($license);

        return $this->statusPayload(
            $validation['valid'],
            $validation['status'],
            $validation['message'],
            $validation['payload'],
        );
    }

    public function isEnforced(): bool
    {
        return (bool) config('license.enforced', false);
    }

    public function isValid(): bool
    {
        return $this->status()['valid'];
    }

    public function deviceId(): string
    {
        $path = $this->deviceIdPath();
        $existing = is_file($path) ? trim((string) file_get_contents($path)) : '';

        if ($this->looksLikeDeviceId($existing)) {
            return $existing;
        }

        $deviceId = 'VMS-'.strtoupper(implode('-', str_split(Str::random(24), 6)));
        $this->ensureDirectory(dirname($path));
        file_put_contents($path, $deviceId);

        return $deviceId;
    }

    /**
     * @return array{valid: bool, status: string, message: string, payload: array<string, mixed>|null}
     */
    public function validateLicenseText(string $licenseText): array
    {
        $document = json_decode($licenseText, true);
        if (! is_array($document)) {
            return [
                'valid' => false,
                'status' => 'invalid_json',
                'message' => 'File bản quyền không đúng định dạng JSON.',
                'payload' => null,
            ];
        }

        return $this->validateDocument($document);
    }

    /**
     * @param  array<string, mixed>  $document
     */
    public function install(array $document): void
    {
        $this->ensureDirectory(dirname($this->licensePath()));
        file_put_contents(
            $this->licensePath(),
            json_encode($document, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        );
    }

    /**
     * @return array<string, mixed>|null
     */
    public function readLicense(): ?array
    {
        $path = $this->licensePath();
        if (! is_file($path)) {
            return null;
        }

        $document = json_decode((string) file_get_contents($path), true);

        return is_array($document) ? $document : null;
    }

    public function licensePath(): string
    {
        return (string) config('license.storage_path');
    }

    public function trialStartedAtPath(): string
    {
        return (string) config('license.trial_started_at_path');
    }

    public function deviceIdPath(): string
    {
        return (string) config('license.device_id_path');
    }

    /**
     * @param  array<string, mixed>  $document
     * @return array{valid: bool, status: string, message: string, payload: array<string, mixed>|null}
     */
    public function validateDocument(array $document): array
    {
        $payload = $document['payload'] ?? null;
        $signature = $document['signature'] ?? null;

        if (! is_array($payload) || ! is_string($signature) || $signature === '') {
            return [
                'valid' => false,
                'status' => 'malformed',
                'message' => 'File bản quyền thiếu thông tin cần thiết.',
                'payload' => null,
            ];
        }

        if (! $this->verifySignature($payload, $signature)) {
            return [
                'valid' => false,
                'status' => 'bad_signature',
                'message' => 'Chữ ký bản quyền không hợp lệ.',
                'payload' => $payload,
            ];
        }

        if (($payload['product'] ?? null) !== 'khach-moi-vms') {
            return [
                'valid' => false,
                'status' => 'wrong_product',
                'message' => 'Bản quyền không dành cho phần mềm này.',
                'payload' => $payload,
            ];
        }

        if (($payload['device_id'] ?? null) !== $this->deviceId()) {
            return [
                'valid' => false,
                'status' => 'wrong_device',
                'message' => 'Bản quyền không đúng mã máy chủ này.',
                'payload' => $payload,
            ];
        }

        $expiresAt = $payload['expires_at'] ?? null;
        if (is_string($expiresAt) && $expiresAt !== '') {
            try {
                $expiry = CarbonImmutable::parse($expiresAt);
            } catch (\Throwable) {
                return [
                    'valid' => false,
                    'status' => 'bad_expiry',
                    'message' => 'Ngày hết hạn bản quyền không hợp lệ.',
                    'payload' => $payload,
                ];
            }

            $skewMinutes = max(0, (int) config('license.clock_skew_minutes', 10));
            if ($expiry->endOfDay()->addMinutes($skewMinutes)->isPast()) {
                return [
                    'valid' => false,
                    'status' => 'expired',
                    'message' => 'Bản quyền đã hết hạn.',
                    'payload' => $payload,
                ];
            }
        }

        return [
            'valid' => true,
            'status' => 'active',
            'message' => 'Bản quyền đang hoạt động.',
            'payload' => $payload,
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function verifySignature(array $payload, string $signature): bool
    {
        $publicKey = (string) config('license.public_key', '');
        if ($publicKey === '') {
            return false;
        }

        $decodedSignature = $this->base64UrlDecode($signature);
        if ($decodedSignature === null) {
            return false;
        }

        return openssl_verify(
            $this->canonicalJson($payload),
            $decodedSignature,
            $publicKey,
            OPENSSL_ALGO_SHA256,
        ) === 1;
    }

    /**
     * @param  array<string, mixed>|null  $payload
     * @param  array<string, mixed>|null  $trial
     * @return array{enabled: bool, valid: bool, status: string, message: string, device_id: string, payload: array<string, mixed>|null, expires_at: string|null, customer: string|null, days_remaining: int|null, trial_started_at: string|null, trial_ends_at: string|null, trial_days_remaining: int|null}
     */
    private function statusPayload(bool $valid, string $status, string $message, ?array $payload, ?array $trial = null): array
    {
        $expiresAt = is_string($payload['expires_at'] ?? null) ? $payload['expires_at'] : null;
        $daysRemaining = null;
        if ($expiresAt !== null) {
            try {
                $expiry = CarbonImmutable::parse($expiresAt)->startOfDay();
                $daysRemaining = now()->startOfDay()->diffInDays($expiry, false);
            } catch (\Throwable) {
                $daysRemaining = null;
            }
        }

        return [
            'enabled' => $this->isEnforced(),
            'valid' => $valid,
            'status' => $status,
            'message' => $message,
            'device_id' => $this->deviceId(),
            'payload' => $payload,
            'expires_at' => $expiresAt,
            'customer' => is_string($payload['customer'] ?? null) ? $payload['customer'] : null,
            'days_remaining' => $trial !== null && is_int($trial['days_remaining'] ?? null) ? $trial['days_remaining'] : $daysRemaining,
            'trial_started_at' => is_string($trial['started_at'] ?? null) ? $trial['started_at'] : null,
            'trial_ends_at' => is_string($trial['ends_at'] ?? null) ? $trial['ends_at'] : null,
            'trial_days_remaining' => is_int($trial['days_remaining'] ?? null) ? $trial['days_remaining'] : null,
        ];
    }

    /**
     * @return array{valid: bool, started_at: string, ends_at: string, days_remaining: int}
     */
    private function trialStatus(): array
    {
        $startedAt = $this->trialStartedAt();
        $trialDays = max(0, (int) config('license.trial_days', 15));
        $endsAt = $startedAt->addDays($trialDays)->endOfDay();
        $daysRemaining = (int) max(0, floor(now()->startOfDay()->diffInDays($endsAt, false)));

        return [
            'valid' => $trialDays > 0 && ! $endsAt->isPast(),
            'started_at' => $startedAt->toDateString(),
            'ends_at' => $endsAt->toDateString(),
            'days_remaining' => $daysRemaining,
        ];
    }

    private function trialStartedAt(): CarbonImmutable
    {
        $path = $this->trialStartedAtPath();
        $existing = is_file($path) ? trim((string) file_get_contents($path)) : '';

        if ($existing !== '') {
            try {
                return CarbonImmutable::parse($existing)->startOfDay();
            } catch (\Throwable) {
                // Replace unreadable trial metadata with a fresh install date.
            }
        }

        $startedAt = CarbonImmutable::now()->startOfDay();
        $this->ensureDirectory(dirname($path));
        file_put_contents($path, $startedAt->toDateString());

        return $startedAt;
    }

    private function looksLikeDeviceId(string $deviceId): bool
    {
        return preg_match('/^VMS-[A-Z0-9]{6}-[A-Z0-9]{6}-[A-Z0-9]{6}-[A-Z0-9]{6}$/', $deviceId) === 1;
    }

    private function ensureDirectory(string $directory): void
    {
        if (! is_dir($directory)) {
            File::makeDirectory($directory, 0755, true, true);
        }
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function canonicalJson(array $payload): string
    {
        $normalized = $this->sortKeys($payload);

        return json_encode($normalized, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
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

    private function base64UrlDecode(string $value): ?string
    {
        $normalized = strtr($value, '-_', '+/');
        $padding = strlen($normalized) % 4;
        if ($padding > 0) {
            $normalized .= str_repeat('=', 4 - $padding);
        }

        $decoded = base64_decode($normalized, true);

        return $decoded === false ? null : $decoded;
    }
}
