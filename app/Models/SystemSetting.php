<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class SystemSetting extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'key',
        'value',
    ];

    /**
     * @param  array<string, string|null>  $defaults
     * @return array<string, string|null>
     */
    public static function values(array $defaults): array
    {
        if (! Schema::hasTable('system_settings')) {
            return $defaults;
        }

        $stored = self::query()
            ->whereIn('key', array_keys($defaults))
            ->pluck('value', 'key')
            ->all();

        $values = array_merge($defaults, $stored);
        $publicAssetKeys = [
            'admin.logo_url',
            'login.logo_url',
            'kiosk.owner_logo_url',
            'kiosk.customer_logo_url',
            'kiosk.logo_url',
            'kiosk.background_url',
            'app.favicon_url',
            'app.desktop_icon_url',
        ];

        foreach ($publicAssetKeys as $key) {
            $value = $values[$key] ?? null;
            if (! is_string($value) || $value === '') {
                continue;
            }

            $path = parse_url($value, PHP_URL_PATH);
            if (is_string($path) && str_starts_with($path, '/storage/')) {
                $values[$key] = $path;
            }
        }

        return $values;
    }

    /**
     * @param  array<string, string|null>  $values
     */
    public static function putMany(array $values): void
    {
        foreach ($values as $key => $value) {
            self::query()->updateOrCreate(
                ['key' => $key],
                ['value' => $value],
            );
        }
    }

    /**
     * @return array<string, string|null>
     */
    public static function kioskDefaults(): array
    {
        return [
            'kiosk.company_name' => config('services.kiosk.company_name'),
            'kiosk.system_name' => config('services.kiosk.system_name'),
            'kiosk.subtitle' => config('services.kiosk.subtitle'),
            'kiosk.welcome_title' => config('services.kiosk.welcome_title'),
            'kiosk.welcome_description' => config('services.kiosk.welcome_description'),
            'kiosk.hotline' => config('services.kiosk.hotline'),
            'kiosk.working_hours' => config('services.kiosk.working_hours'),
            'admin.logo_url' => config('services.kiosk.admin_logo_url'),
            'login.logo_url' => config('services.kiosk.login_logo_url'),
            'login.title' => config('services.kiosk.login_title') ?? 'Visitor Management System',
            'login.subtitle' => config('services.kiosk.login_subtitle') ?? 'Đăng nhập vào hệ thống vận hành',
            'kiosk.owner_logo_url' => config('services.kiosk.owner_logo_url'),
            'kiosk.customer_logo_url' => config('services.kiosk.customer_logo_url'),
            'kiosk.logo_url' => config('services.kiosk.logo_url'),
            'kiosk.background_url' => config('services.kiosk.background_url'),
            'kiosk.primary_color' => config('services.kiosk.primary_color'),
            'kiosk.secondary_color' => config('services.kiosk.secondary_color', '#0cb4d8'),
            'kiosk.background_color' => config('services.kiosk.background_color', '#f4f8fd'),
            'kiosk.surface_color' => config('services.kiosk.surface_color', '#ffffff'),
            'app.favicon_url' => config('services.kiosk.favicon_url'),
            'app.desktop_icon_url' => config('services.kiosk.desktop_icon_url'),
        ];
    }

    /**
     * @return array<string, string|null>
     */
    public static function adminThemeDefaults(): array
    {
        return [
            'admin.navbar_color' => '#ffffff',
            'admin.content_background' => '#f8fafc',
            'admin.primary_color' => '#d40511',
            'admin.secondary_color' => '#ffcc00',
        ];
    }

    /**
     * @return array<string, string|null>
     */
    public static function accessDefaults(): array
    {
        return [
            'access.allow_early_checkin' => '1',
            'access.early_checkin_minutes' => '30',
            'access.allow_late_checkin' => '1',
            'access.late_checkin_minutes' => '60',
            'access.warning_enabled' => '1',
            'access.warning_message' => 'Khách đến ngoài khung giờ check-in được cho phép.',
        ];
    }

    /**
     * @return array<string, string|null>
     */
    public static function mailDefaults(): array
    {
        return [
            'mail.host' => 'smtp.gmail.com',
            'mail.port' => '587',
            'mail.scheme' => 'smtp',
            'mail.auth_mode' => 'login',
            'mail.username' => config('mail.mailers.smtp.username'),
            'mail.password' => null,
            'mail.from_address' => config('mail.from.address'),
            'mail.from_name' => config('mail.from.name') ?: config('app.name'),
            'mail.reply_to' => null,
            'mail.local_domain' => config('mail.mailers.smtp.local_domain'),
            'mail.timeout' => '30',
            'mail.trigger_qr_approved' => '1',
            'mail.trigger_host_checkin' => '1',
        ];
    }
}
