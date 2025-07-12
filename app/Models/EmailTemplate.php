<?php

namespace App\Models;

use App\Models\Model;

class EmailTemplate extends Model
{
    protected $fillable = [
        'name',
        'subject',
        'content'
    ];
}
