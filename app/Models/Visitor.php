<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Visitor extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::created(function (Visitor $visitor): void {
            if (blank($visitor->visitor_code)) {
                $visitor->forceFill([
                    'visitor_code' => self::codeFromId($visitor->getKey()),
                ])->saveQuietly();
            }
        });
    }

    protected $fillable = [
        'full_name',
        'phone',
        'email',
        'company',
        'identity_no',
        'identity_issued_place',
        'identity_issued_date',
        'note',
    ];

    protected $casts = [
        'identity_issued_date' => 'date',
    ];

    public static function codeFromId(int|string $id): string
    {
        return sprintf('KH-%06d', (int) $id);
    }

    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class);
    }

    public function watchlists(): HasMany
    {
        return $this->hasMany(Watchlist::class);
    }
}
