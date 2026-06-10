<?php

use App\Models\AuditLog;
use App\Models\Visit;
use App\Support\LicenseManager;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
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

Artisan::command('license:reset {--fresh-trial : Xoa license va bat dau lai 15 ngay dung thu} {--expire-trial : Xoa license va danh dau dung thu da het han} {--trial-days-left= : Dat lai dung thu theo so ngay con lai muon test} {--device : Xoa ma may de tao lai ma may moi} {--force : Cho phep chay tren production}', function (): int {
    if (app()->isProduction() && ! $this->option('force')) {
        $this->error('Lenh nay chi dung de test. Neu chac chan can chay tren production, them --force.');

        return 1;
    }

    if (($this->option('fresh-trial') || $this->option('expire-trial')) && $this->option('trial-days-left') !== null) {
        $this->error('Khong dung chung --trial-days-left voi --fresh-trial hoac --expire-trial.');
        return 1;
    }

    if ($this->option('fresh-trial') && $this->option('expire-trial')) {
        $this->error('Chi chon mot trong hai tuy chon: --fresh-trial hoac --expire-trial.');

        return 1;
    }

    $license = app(LicenseManager::class);
    $licensePath = $license->licensePath();
    $trialPath = $license->trialStartedAtPath();
    $devicePath = $license->deviceIdPath();

    if (is_file($licensePath)) {
        File::delete($licensePath);
        $this->info("Da xoa license: {$licensePath}");
    } else {
        $this->line("Khong co file license: {$licensePath}");
    }

    if ($this->option('device')) {
        if (is_file($devicePath)) {
            File::delete($devicePath);
            $this->info("Da xoa ma may: {$devicePath}");
        } else {
            $this->line("Khong co file ma may: {$devicePath}");
        }
    }

    if ($this->option('fresh-trial') || $this->option('expire-trial') || $this->option('trial-days-left') !== null) {
        File::ensureDirectoryExists(dirname($trialPath));
        $trialDays = max(0, (int) config('license.trial_days', 15));
        $daysLeftInput = $this->option('trial-days-left');
        $daysLeft = $daysLeftInput === null ? null : max(0, (int) $daysLeftInput);

        if ($daysLeft !== null) {
            $startedAt = CarbonImmutable::now()->startOfDay()->subDays(max(0, $trialDays - $daysLeft));
        } else {
            $startedAt = $this->option('expire-trial')
                ? CarbonImmutable::now()->startOfDay()->subDays($trialDays + 1)
                : CarbonImmutable::now()->startOfDay();
        }

        File::put($trialPath, $startedAt->toDateString());
        $mode = $daysLeft !== null
            ? "dat dung thu con lai {$daysLeft} ngay"
            : ($this->option('expire-trial') ? 'het han dung thu' : 'bat dau lai dung thu');
        $this->info("Da cap nhat dung thu ({$mode}): {$trialPath}");
    } else {
        $this->line("Giu nguyen moc dung thu: {$trialPath}");
    }

    $status = $license->status();
    $this->info("Trang thai hien tai: {$status['status']} - {$status['message']}");
    $this->line("Ma may: {$status['device_id']}");

    return 0;
})->purpose('Reset license state for local testing');

Schedule::command('vms:scan-overstay')->everyFiveMinutes();
