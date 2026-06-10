<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\SystemSetting;
use App\Models\Tenant;
use App\Support\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}

