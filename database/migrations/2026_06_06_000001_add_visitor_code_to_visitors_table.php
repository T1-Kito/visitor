<?php

use App\Models\Visitor;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('visitors', 'visitor_code')) {
            Schema::table('visitors', function (Blueprint $table): void {
                $table->string('visitor_code', 30)->nullable()->unique()->after('id');
            });
        }

        DB::table('visitors')
            ->whereNull('visitor_code')
            ->orderBy('id')
            ->chunkById(200, function ($visitors): void {
                foreach ($visitors as $visitor) {
                    DB::table('visitors')
                        ->where('id', $visitor->id)
                        ->update(['visitor_code' => Visitor::codeFromId($visitor->id)]);
                }
            });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('visitors', 'visitor_code')) {
            return;
        }

        Schema::table('visitors', function (Blueprint $table): void {
            $table->dropUnique(['visitor_code']);
            $table->dropColumn('visitor_code');
        });
    }
};
