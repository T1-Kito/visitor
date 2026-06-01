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
        Schema::create('roles', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 100)->unique();
            $table->string('slug', 120)->unique();
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 100)->unique();
            $table->string('slug', 150)->unique();
            $table->timestamps();
        });

        Schema::create('permission_role', function (Blueprint $table): void {
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->primary(['role_id', 'permission_id']);
        });

        Schema::create('role_user', function (Blueprint $table): void {
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->primary(['role_id', 'user_id']);
        });

        Schema::create('departments', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 32)->unique();
            $table->string('name', 120);
            $table->timestamps();
        });

        Schema::create('employees', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name', 120);
            $table->string('email', 160)->nullable()->unique();
            $table->string('phone', 30)->nullable();
            $table->string('job_title', 100)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('visitors', function (Blueprint $table): void {
            $table->id();
            $table->string('full_name', 120);
            $table->string('phone', 30)->nullable();
            $table->string('email', 160)->nullable();
            $table->string('company', 160)->nullable();
            $table->string('identity_no', 80)->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });

        Schema::create('visits', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 30)->unique();
            $table->foreignId('visitor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('host_employee_id')->constrained('employees')->restrictOnDelete();
            $table->dateTime('scheduled_at');
            $table->dateTime('expected_checkout_at')->nullable();
            $table->dateTime('actual_checkin_at')->nullable();
            $table->dateTime('actual_checkout_at')->nullable();
            $table->string('status', 30)->default('pending')->index();
            $table->string('purpose', 255);
            $table->string('access_zone', 120)->nullable();
            $table->string('checkin_method', 30)->default('qr');
            $table->string('qr_token', 80)->nullable()->unique();
            $table->dateTime('qr_expires_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            $table->index('scheduled_at');
        });

        Schema::create('approvals', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('visit_id')->constrained()->cascadeOnDelete()->unique();
            $table->foreignId('approver_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 30)->default('pending')->index();
            $table->text('note')->nullable();
            $table->dateTime('acted_at')->nullable();
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 120);
            $table->string('entity_type', 80);
            $table->string('entity_id', 80)->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['entity_type', 'entity_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('approvals');
        Schema::dropIfExists('visits');
        Schema::dropIfExists('visitors');
        Schema::dropIfExists('employees');
        Schema::dropIfExists('departments');
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('permission_role');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};
