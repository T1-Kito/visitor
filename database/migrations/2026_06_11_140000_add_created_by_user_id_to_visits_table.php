<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('visits', function (Blueprint $table): void {
            $table->foreignId('created_by_user_id')
                ->nullable()
                ->after('host_employee_id')
                ->constrained('users')
                ->nullOnDelete();
        });

        DB::table('audit_logs')
            ->where('action', 'visit.created')
            ->where('entity_type', 'visit')
            ->whereNotNull('entity_id')
            ->whereNotNull('user_id')
            ->orderBy('id')
            ->each(function (object $log): void {
                DB::table('visits')
                    ->where('id', (int) $log->entity_id)
                    ->whereNull('created_by_user_id')
                    ->update(['created_by_user_id' => $log->user_id]);
            });
    }

    public function down(): void
    {
        Schema::table('visits', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('created_by_user_id');
        });
    }
};
