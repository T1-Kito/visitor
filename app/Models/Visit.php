<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Visit extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'code',
        'tenant_id',
        'visitor_id',
        'host_employee_id',
        'created_by_user_id',
        'scheduled_at',
        'expected_checkout_at',
        'actual_checkin_at',
        'actual_checkout_at',
        'status',
        'purpose',
        'access_zone',
        'checkin_method',
        'qr_token',
        'qr_expires_at',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'expected_checkout_at' => 'datetime',
            'actual_checkin_at' => 'datetime',
            'actual_checkout_at' => 'datetime',
            'qr_expires_at' => 'datetime',
        ];
    }

    public function visitor(): BelongsTo
    {
        return $this->belongsTo(Visitor::class);
    }

    public function hostEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'host_employee_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function approval(): HasOne
    {
        return $this->hasOne(Approval::class);
    }

    public function badges(): HasMany
    {
        return $this->hasMany(Badge::class);
    }

    public function activeBadge(): HasOne
    {
        return $this->hasOne(Badge::class)->where('status', 'active');
    }
}
