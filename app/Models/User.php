<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser
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
        'user_packet',
        'external_token',
        'external_refresh_token',
        'is_dev',
        'password',
    ];

    protected $hidden = [
        'remember_token',
        'external_token',
        'external_refresh_token',
        'ip_address',
        'user_packet',
        'password',
    ];

    protected function casts(): array
    {
        return [
            'user_packet' => 'object',
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
            ->whereHas('rankings', fn ($q) => $q->forReminders())
            ->with('rankings', fn ($q) => $q->forReminders())
            ->with('preferences');
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_dev;
    }
}
