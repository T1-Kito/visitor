<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'user_id',
        'tenant_id',
        'actor_name',
        'actor_email',
        'ip_address',
        'request_method',
        'request_url',
        'user_agent',
        'action',
        'entity_type',
        'entity_id',
        'meta',
    ];

    protected static function booted(): void
    {
        static::creating(function (AuditLog $log): void {
            $actor = auth()->user();

            if ($actor === null && $log->user_id !== null) {
                $actor = User::query()->find($log->user_id);
            }

            $log->actor_name ??= $actor?->name ?? self::systemActorName($log->action);
            $log->actor_email ??= $actor?->email;

            if (! app()->bound('request')) {
                return;
            }

            $request = request();
            $log->ip_address ??= $request->ip();
            $log->request_method ??= $request->method();
            $log->request_url ??= $request->fullUrl();
            $log->user_agent ??= $request->userAgent();
        });
    }

    protected function casts(): array
    {
        return [
            'meta' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    private static function systemActorName(string $action): string
    {
        if (str_starts_with($action, 'kiosk.')) {
            return 'Kiosk';
        }

        return 'Hệ thống';
    }
}
