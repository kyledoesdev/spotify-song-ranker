<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    protected $fillable = [
        'spotify_id',
        'name',
        'email',
        'avatar',
        'timezone',
        'ip_address',
        'user_agent',
        'user_platform',
        'external_token',
        'external_refresh_token',
        'is_dev',
    ];

    protected $hidden = [
        'remember_token',
        'external_token',
        'external_refresh_token',
        'ip_address'
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
        ];
    }

    public function rankings(): HasMany
    {
        return $this->hasMany(Ranking::class);
    }

    public function preferences(): HasOne
    {
        return $this->hasOne(UserPreference::class);
    }

    /* scopes */

    public function scopeForRankingReminders(Builder $query)
    {
        $query->newQuery()
            ->select('id', 'name', 'email')
            ->whereHas('rankings', fn($q) => $q->forReminders())
            ->with('rankings', fn($q) => $q->forReminders())
            ->with('preferences');
    }
}
