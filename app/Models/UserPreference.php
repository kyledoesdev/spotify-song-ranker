<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPreference extends Model
{
    protected $fillable = [
        'user_id',
        'recieve_newsletter_emails',
        'enabled_comment_mentions',
    ];

    protected function casts(): array
    {
        return [
            'recieve_newsletter_emails' => 'boolean',
            'enabled_comment_mentions' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
