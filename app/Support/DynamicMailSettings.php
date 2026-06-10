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

        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.transport' => 'smtp',
            'mail.mailers.smtp.scheme' => $settings['mail.scheme'] ?: null,
            'mail.mailers.smtp.host' => $settings['mail.host'] ?: 'smtp.gmail.com',
            'mail.mailers.smtp.port' => (int) ($settings['mail.port'] ?: 587),
            'mail.mailers.smtp.username' => $settings['mail.username'],
            'mail.mailers.smtp.password' => $settings['mail.password'],
            'mail.from.address' => $settings['mail.from_address'],
            'mail.from.name' => $settings['mail.from_name'],
        ]);

        Mail::purge('smtp');

        return $settings;
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
