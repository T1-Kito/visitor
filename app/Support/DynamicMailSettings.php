<?php

namespace App\Support;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;

class DynamicMailSettings
{
    /**
     * @return array<string, string|null>
     */
    public static function values(): array
    {
        $settings = SystemSetting::values(SystemSetting::mailDefaults());
        $passwordSetting = Schema::hasTable('system_settings')
            ? SystemSetting::query()->where('key', 'mail.password')->first()
            : null;
        $encryptedPassword = $passwordSetting?->value;
        $password = $passwordSetting
            ? self::decryptPassword($encryptedPassword)
            : config('mail.mailers.smtp.password');

        $settings['mail.password'] = $password;
        $settings['mail.password_configured'] = $password ? '1' : '0';

        return $settings;
    }

    /**
     * Apply the database-backed SMTP settings for the current request.
     *
     * @return array<string, string|null>
     */
    public static function apply(): array
    {
        $settings = self::values();
        $scheme = match ($settings['mail.scheme'] ?? null) {
            'smtps', 'ssl' => 'smtps',
            default => 'smtp',
        };

        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.transport' => 'smtp',
            'mail.mailers.smtp.scheme' => $scheme,
            'mail.mailers.smtp.host' => $settings['mail.host'] ?: 'smtp.gmail.com',
            'mail.mailers.smtp.port' => (int) ($settings['mail.port'] ?: 587),
            'mail.mailers.smtp.username' => $settings['mail.auth_mode'] === 'none' ? null : $settings['mail.username'],
            'mail.mailers.smtp.password' => $settings['mail.auth_mode'] === 'none' ? null : $settings['mail.password'],
            'mail.mailers.smtp.local_domain' => $settings['mail.local_domain'] ?: config('mail.mailers.smtp.local_domain'),
            'mail.mailers.smtp.timeout' => max(5, (int) ($settings['mail.timeout'] ?: 30)),
            'mail.from.address' => $settings['mail.from_address'],
            'mail.from.name' => $settings['mail.from_name'],
        ]);

        Mail::purge('smtp');

        return $settings;
    }

    public static function triggerEnabled(string $key): bool
    {
        $settings = SystemSetting::values(SystemSetting::mailDefaults());

        return ($settings[$key] ?? '1') === '1';
    }

    public static function encryptPassword(string $password): string
    {
        return Crypt::encryptString($password);
    }

    private static function decryptPassword(?string $password): ?string
    {
        if (! $password) {
            return null;
        }

        try {
            return Crypt::decryptString($password);
        } catch (\Throwable) {
            return null;
        }
    }
}
