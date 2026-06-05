<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Visitor extends Model
{
    use HasFactory;

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

    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class);
    }

    public function watchlists(): HasMany
    {
        return $this->hasMany(Watchlist::class);
    }
}
