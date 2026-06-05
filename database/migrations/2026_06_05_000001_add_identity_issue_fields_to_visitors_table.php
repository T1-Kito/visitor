<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('visitors', function (Blueprint $table): void {
            if (! Schema::hasColumn('visitors', 'identity_issued_place')) {
                $table->string('identity_issued_place', 160)->nullable()->after('identity_no');
            }

            if (! Schema::hasColumn('visitors', 'identity_issued_date')) {
                $table->date('identity_issued_date')->nullable()->after('identity_issued_place');
            }
        });
    }

    public function down(): void
    {
        Schema::table('visitors', function (Blueprint $table): void {
            if (Schema::hasColumn('visitors', 'identity_issued_date')) {
                $table->dropColumn('identity_issued_date');
            }

            if (Schema::hasColumn('visitors', 'identity_issued_place')) {
                $table->dropColumn('identity_issued_place');
            }
        });
    }
};
