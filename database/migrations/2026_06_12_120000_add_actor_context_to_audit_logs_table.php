<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('audit_logs', function (Blueprint $table): void {
            $table->string('actor_name', 160)->nullable()->after('user_id');
            $table->string('actor_email', 190)->nullable()->after('actor_name');
            $table->string('ip_address', 45)->nullable()->after('actor_email');
            $table->string('request_method', 12)->nullable()->after('ip_address');
            $table->text('request_url')->nullable()->after('request_method');
            $table->text('user_agent')->nullable()->after('request_url');
            $table->index(['actor_email', 'created_at'], 'audit_logs_actor_email_created_at_index');
        });

        DB::table('audit_logs')
            ->whereNotNull('user_id')
            ->orderBy('id')
            ->chunkById(200, function ($logs): void {
                $users = DB::table('users')
                    ->whereIn('id', $logs->pluck('user_id')->filter()->unique())
                    ->get(['id', 'name', 'email'])
                    ->keyBy('id');

                foreach ($logs as $log) {
                    $user = $users->get($log->user_id);
                    if ($user === null) {
                        continue;
                    }

                    DB::table('audit_logs')
                        ->where('id', $log->id)
                        ->update([
                            'actor_name' => $user->name,
                            'actor_email' => $user->email,
                        ]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('audit_logs', function (Blueprint $table): void {
            $table->dropIndex('audit_logs_actor_email_created_at_index');
            $table->dropColumn([
                'actor_name',
                'actor_email',
                'ip_address',
                'request_method',
                'request_url',
                'user_agent',
            ]);
        });
    }
};
