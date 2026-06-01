<?php

use App\Models\AuditLog;
use App\Models\Visit;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('vms:scan-overstay', function (): int {
    $overstayCount = Visit::query()
        ->where('status', 'checked_in')
        ->whereNotNull('expected_checkout_at')
        ->where('expected_checkout_at', '<', now())
        ->count();

    $pendingApprovalCount = Visit::query()
        ->where('status', 'pending')
        ->where('scheduled_at', '<=', now()->addHour())
        ->count();

    AuditLog::query()->create([
        'user_id' => null,
        'action' => 'alerts.scan',
        'entity_type' => 'system',
        'entity_id' => 'alerts',
        'meta' => [
            'overstay_count' => $overstayCount,
            'pending_approval_count' => $pendingApprovalCount,
        ],
    ]);

    $this->info("Overstay: {$overstayCount}; pending approval: {$pendingApprovalCount}");

    return 0;
})->purpose('Scan VMS operational alerts and write an audit summary');

Schedule::command('vms:scan-overstay')->everyFiveMinutes();
