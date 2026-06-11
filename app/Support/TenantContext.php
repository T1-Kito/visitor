<?php

namespace App\Support;

use App\Models\Tenant;
use Illuminate\Support\Facades\Schema;

class TenantContext
{
    private ?int $tenantId = null;
    private bool $disabled = false;

    public function set(?int $tenantId): void
    {
        $this->tenantId = $tenantId;
    }

    public function id(): ?int
    {
        if ($this->disabled) {
            return null;
        }

        if ($this->tenantId !== null) {
            return $this->tenantId;
        }

        if ($this->isStrict()) {
            return null;
        }

        return $this->defaultTenantId();
    }

    public function isStrict(): bool
    {
        if ($this->disabled) {
            return false;
        }

        return config('saas.mode') === 'multi';
    }

    public function withoutTenant(callable $callback): mixed
    {
        $previous = $this->disabled;
        $this->disabled = true;

        try {
            return $callback();
        } finally {
            $this->disabled = $previous;
        }
    }

    private function defaultTenantId(): ?int
    {
        if (! Schema::hasTable('tenants')) {
            return null;
        }

        $slug = (string) config('saas.default_tenant_slug', 'default');

        return Tenant::query()
            ->where('slug', $slug)
            ->value('id');
    }
}

