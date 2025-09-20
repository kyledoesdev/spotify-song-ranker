<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPreference extends Model
{
    protected $fillable = [
        'user_id',
        'recieve_reminder_emails',
        'recieve_newsletter_emails',
    ];

    protected function casts(): array
    {
        return [
            'recieve_reminder_emails' => 'boolean',
            'recieve_newsletter_emails' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
