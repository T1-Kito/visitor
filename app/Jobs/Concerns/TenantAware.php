<?php

namespace App\Jobs\Concerns;

use App\Support\TenantContext;

trait TenantAware
{
    public ?int $tenantId = null;

    public function captureTenant(): void
    {
        if ($this->tenantId === null) {
            $this->tenantId = app(TenantContext::class)->id();
        }
    }

    public function applyTenant(): void
    {
        if ($this->tenantId !== null) {
            app(TenantContext::class)->set($this->tenantId);
        }
    }
}
