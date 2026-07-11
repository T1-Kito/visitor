<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('departments', function (Blueprint $table): void {
            $table->string('name_vi', 120)->nullable()->after('name');
            $table->string('name_en', 120)->nullable()->after('name_vi');
        });

        Schema::table('badges', function (Blueprint $table): void {
            $table->string('label_vi', 120)->nullable()->after('badge_no');
            $table->string('label_en', 120)->nullable()->after('label_vi');
        });

        DB::table('departments')->orderBy('id')->chunkById(200, function ($departments): void {
            foreach ($departments as $department) {
                DB::table('departments')->where('id', $department->id)->update([
                    'name_vi' => $department->name,
                    'name_en' => $department->name,
                ]);
            }
        });

        DB::table('badges')->orderBy('id')->chunkById(200, function ($badges): void {
            foreach ($badges as $badge) {
                $english = $badge->badge_no;
                $vietnamese = preg_replace('/^Visitor\s+card\s+(\d+)$/i', 'Thẻ khách $1', $badge->badge_no);
                DB::table('badges')->where('id', $badge->id)->update([
                    'label_vi' => $vietnamese,
                    'label_en' => $english,
                ]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('badges', function (Blueprint $table): void {
            $table->dropColumn(['label_vi', 'label_en']);
        });
        Schema::table('departments', function (Blueprint $table): void {
            $table->dropColumn(['name_vi', 'name_en']);
        });
    }
};
