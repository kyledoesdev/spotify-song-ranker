<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable {
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'spotify_id',
        'name',
        'email',
        'avatar',
        'external_token',
        'external_refresh_token'
    ];

    protected $hidden = [
        'remember_token',
        'external_token',
        'external_refresh_token'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
