<?php

namespace Tests\Feature;

use App\Jobs\Concerns\TenantAware;
use App\Models\Department;
use App\Models\SystemSetting;
use App\Models\Tenant;
use App\Models\User;
use App\Support\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_records_are_assigned_to_the_current_tenant(): void
    {
        $tenant = Tenant::query()->create([
            'name' => 'Customer A',
            'slug' => 'customer-a',
            'status' => 'active',
        ]);

        app(TenantContext::class)->set($tenant->id);

        $department = Department::query()->create([
            'code' => 'HR-A',
            'name' => 'Nhan su A',
        ]);

        $this->assertSame($tenant->id, $department->tenant_id);
    }

    public function test_tenant_scopes_keep_customer_data_separate(): void
    {
        $tenantA = Tenant::query()->create([
            'name' => 'Customer A',
            'slug' => 'customer-a',
            'status' => 'active',
        ]);
        $tenantB = Tenant::query()->create([
            'name' => 'Customer B',
            'slug' => 'customer-b',
            'status' => 'active',
        ]);

        app(TenantContext::class)->set($tenantA->id);
        Department::query()->create(['code' => 'OPS-A', 'name' => 'Van hanh A']);
        SystemSetting::query()->create(['key' => 'kiosk.system_name', 'value' => 'VMS A']);

        app(TenantContext::class)->set($tenantB->id);
        Department::query()->create(['code' => 'OPS-B', 'name' => 'Van hanh B']);
        SystemSetting::query()->create(['key' => 'kiosk.system_name', 'value' => 'VMS B']);

        $this->assertSame(['OPS-B'], Department::query()->pluck('code')->all());
        $this->assertSame('VMS B', SystemSetting::query()->where('key', 'kiosk.system_name')->value('value'));

        app(TenantContext::class)->set($tenantA->id);

        $this->assertSame(['OPS-A'], Department::query()->pluck('code')->all());
        $this->assertSame('VMS A', SystemSetting::query()->where('key', 'kiosk.system_name')->value('value'));

        $totalDepartments = app(TenantContext::class)->withoutTenant(
            fn () => Department::query()->count(),
        );

        $this->assertSame(2, $totalDepartments);
    }

    public function test_strict_mode_blocks_queries_when_no_tenant_is_set(): void
    {
        config(['saas.mode' => 'multi']);

        $tenant = Tenant::query()->create([
            'name' => 'Customer A',
            'slug' => 'customer-a',
            'status' => 'active',
        ]);

        app(TenantContext::class)->set($tenant->id);
        Department::query()->create(['code' => 'OPS-A', 'name' => 'Van hanh A']);

        app(TenantContext::class)->set(null);

        $this->assertSame(0, Department::query()->count());

        $total = app(TenantContext::class)->withoutTenant(
            fn () => Department::query()->count(),
        );
        $this->assertSame(1, $total);
    }

    public function test_users_email_is_unique_per_tenant_not_globally(): void
    {
        $tenantA = Tenant::query()->create([
            'name' => 'Customer A', 'slug' => 'customer-a', 'status' => 'active',
        ]);
        $tenantB = Tenant::query()->create([
            'name' => 'Customer B', 'slug' => 'customer-b', 'status' => 'active',
        ]);

        app(TenantContext::class)->set($tenantA->id);
        User::query()->create([
            'name' => 'Admin A',
            'email' => 'admin@company.local',
            'password' => Hash::make('secret'),
        ]);

        app(TenantContext::class)->set($tenantB->id);
        $userB = User::query()->create([
            'name' => 'Admin B',
            'email' => 'admin@company.local',
            'password' => Hash::make('secret'),
        ]);

        $this->assertSame($tenantB->id, $userB->tenant_id);

        $total = app(TenantContext::class)->withoutTenant(
            fn () => User::query()->where('email', 'admin@company.local')->count(),
        );
        $this->assertSame(2, $total);
    }

    public function test_tenant_aware_trait_captures_and_applies_tenant(): void
    {
        config(['saas.mode' => 'multi']);

        $tenant = Tenant::query()->create([
            'name' => 'Customer A', 'slug' => 'customer-a', 'status' => 'active',
        ]);

        app(TenantContext::class)->set($tenant->id);

        $job = new class {
            use TenantAware;
        };
        $job->captureTenant();

        app(TenantContext::class)->set(null);
        $this->assertNull(app(TenantContext::class)->id());

        $job->applyTenant();
        $this->assertSame($tenant->id, app(TenantContext::class)->id());
    }
}
