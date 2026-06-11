<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Support\TenantContext;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class TenantRun extends Command
{
    protected $signature = 'tenant:run
        {tenant : Tenant slug or id}
        {cmd : Artisan command to run, e.g. "cache:clear"}';

    protected $description = 'Run an artisan command in the context of a single tenant.';

    public function handle(TenantContext $context): int
    {
        $key = $this->argument('tenant');

        $tenant = Tenant::query()
            ->where('slug', $key)
            ->orWhere('id', is_numeric($key) ? (int) $key : 0)
            ->first();

        if (! $tenant) {
            $this->error("Tenant not found: {$key}");
            return self::FAILURE;
        }

        $context->set((int) $tenant->id);
        $this->info("Tenant context: {$tenant->name} (#{$tenant->id})");

        return Artisan::call($this->argument('cmd'), [], $this->output);
    }
}
