<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 160);
            $table->string('slug', 120)->unique();
            $table->string('domain', 190)->nullable()->unique();
            $table->string('status', 30)->default('active')->index();
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        $tenantId = DB::table('tenants')->insertGetId([
            'name' => config('saas.default_tenant_name', 'Default Customer'),
            'slug' => config('saas.default_tenant_slug', 'default'),
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        foreach ($this->tenantTables() as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName): void {
                $table->foreignId('tenant_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('tenants')
                    ->nullOnDelete();
                $table->index('tenant_id', "{$tableName}_tenant_id_index");
            });

            DB::table($tableName)->update(['tenant_id' => $tenantId]);
        }

        $this->moveUniqueIndexesToTenant();
    }

    public function down(): void
    {
        $this->restoreGlobalUniqueIndexes();

        foreach (array_reverse($this->tenantTables()) as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName): void {
                $table->dropForeign(["tenant_id"]);
                $table->dropIndex("{$tableName}_tenant_id_index");
                $table->dropColumn('tenant_id');
            });
        }

        Schema::dropIfExists('tenants');
    }

    private function restoreGlobalUniqueIndexes(): void
    {
        Schema::table('system_settings', function (Blueprint $table): void {
            $table->dropUnique('system_settings_tenant_key_unique');
            $table->unique('key', 'system_settings_key_unique');
        });

        Schema::table('badges', function (Blueprint $table): void {
            $table->dropUnique('badges_tenant_badge_no_unique');
            $table->unique('badge_no', 'badges_badge_no_unique');
        });

        Schema::table('visits', function (Blueprint $table): void {
            $table->dropUnique('visits_tenant_code_unique');
            $table->dropUnique('visits_tenant_qr_token_unique');
            $table->unique('code', 'visits_code_unique');
            $table->unique('qr_token', 'visits_qr_token_unique');
        });

        Schema::table('employees', function (Blueprint $table): void {
            $table->dropUnique('employees_tenant_email_unique');
            $table->unique('email', 'employees_email_unique');
        });

        Schema::table('departments', function (Blueprint $table): void {
            $table->dropUnique('departments_tenant_code_unique');
            $table->unique('code', 'departments_code_unique');
        });
    }

    private function moveUniqueIndexesToTenant(): void
    {
        Schema::table('departments', function (Blueprint $table): void {
            $table->dropUnique('departments_code_unique');
            $table->unique(['tenant_id', 'code'], 'departments_tenant_code_unique');
        });

        Schema::table('employees', function (Blueprint $table): void {
            $table->dropUnique('employees_email_unique');
            $table->unique(['tenant_id', 'email'], 'employees_tenant_email_unique');
        });

        Schema::table('visits', function (Blueprint $table): void {
            $table->dropUnique('visits_code_unique');
            $table->dropUnique('visits_qr_token_unique');
            $table->unique(['tenant_id', 'code'], 'visits_tenant_code_unique');
            $table->unique(['tenant_id', 'qr_token'], 'visits_tenant_qr_token_unique');
        });

        Schema::table('badges', function (Blueprint $table): void {
            $table->dropUnique('badges_badge_no_unique');
            $table->unique(['tenant_id', 'badge_no'], 'badges_tenant_badge_no_unique');
        });

        Schema::table('system_settings', function (Blueprint $table): void {
            $table->dropUnique('system_settings_key_unique');
            $table->unique(['tenant_id', 'key'], 'system_settings_tenant_key_unique');
        });
    }

    /**
     * @return array<int, string>
     */
    private function tenantTables(): array
    {
        return [
            'users',
            'departments',
            'employees',
            'visitors',
            'visits',
            'approvals',
            'audit_logs',
            'badges',
            'access_control_logs',
            'notifications',
            'watchlists',
            'system_settings',
            'user_mobile_favorites',
        ];
    }
};
