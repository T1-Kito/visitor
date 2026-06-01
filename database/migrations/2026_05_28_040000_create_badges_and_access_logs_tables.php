<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('badges', function (Blueprint $table): void {
            $table->id();
            $table->string('badge_no', 40)->unique();
            $table->foreignId('visit_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status', 30)->default('available')->index();
            $table->dateTime('issued_at')->nullable();
            $table->dateTime('revoked_at')->nullable();
            $table->dateTime('valid_until')->nullable();
            $table->timestamps();
        });

        Schema::create('access_control_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('visit_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('badge_id')->nullable()->constrained()->nullOnDelete();
            $table->string('event', 60);
            $table->string('source', 60)->default('system');
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['event', 'source']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('access_control_logs');
        Schema::dropIfExists('badges');
    }
};
