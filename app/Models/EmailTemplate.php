<?php

namespace App\Models;

class EmailTemplate extends Model
{
    protected $fillable = [
        'name',
        'subject',
        'content',
    ];
}
