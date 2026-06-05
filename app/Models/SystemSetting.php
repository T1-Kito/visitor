<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class SystemSetting extends Model
{
    protected $fillable = [
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

        return array_merge($defaults, $stored);
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
            'kiosk.owner_logo_url' => config('services.kiosk.owner_logo_url'),
            'kiosk.customer_logo_url' => config('services.kiosk.customer_logo_url'),
            'kiosk.logo_url' => config('services.kiosk.logo_url'),
            'kiosk.background_url' => config('services.kiosk.background_url'),
            'kiosk.primary_color' => config('services.kiosk.primary_color'),
            'app.favicon_url' => config('services.kiosk.favicon_url'),
        ];
    }
}
