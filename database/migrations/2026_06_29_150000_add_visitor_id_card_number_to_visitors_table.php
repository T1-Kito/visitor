<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('visitors', function (Blueprint $table): void {
            if (! Schema::hasColumn('visitors', 'visitor_id_card_number')) {
                $table->string('visitor_id_card_number', 80)->nullable()->after('identity_no');
            }
        });
    }

    public function down(): void
    {
        Schema::table('visitors', function (Blueprint $table): void {
            if (Schema::hasColumn('visitors', 'visitor_id_card_number')) {
                $table->dropColumn('visitor_id_card_number');
            }
        });
    }
};