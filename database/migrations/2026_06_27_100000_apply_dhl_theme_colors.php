<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->applyColors([
            'admin.navbar_color' => '#ffcc00',
            'admin.content_background' => '#ffffff',
            'admin.primary_color' => '#d40511',
            'admin.secondary_color' => '#ffcc00',
            'kiosk.primary_color' => '#d40511',
            'kiosk.secondary_color' => '#ffcc00',
            'kiosk.background_color' => '#ffffff',
            'kiosk.surface_color' => '#ffffff',
        ]);
    }

    public function down(): void
    {
        $this->applyColors([
            'admin.navbar_color' => '#ffffff',
            'admin.content_background' => '#f8fafc',
            'admin.primary_color' => '#d40511',
            'admin.secondary_color' => '#ffcc00',
            'kiosk.primary_color' => '#146bd7',
            'kiosk.secondary_color' => '#0cb4d8',
            'kiosk.background_color' => '#f4f8fd',
            'kiosk.surface_color' => '#ffffff',
        ]);
    }

    /** @param array<string, string> $colors */
    private function applyColors(array $colors): void
    {
        if (! Schema::hasTable('system_settings')) {
            return;
        }

        foreach ($colors as $key => $value) {
            DB::table('system_settings')
                ->where('key', $key)
                ->update(['value' => $value, 'updated_at' => now()]);
        }
    }
};