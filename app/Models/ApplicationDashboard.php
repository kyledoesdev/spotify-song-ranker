<?php

namespace App\Models;

use App\Models\Model;

class ApplicationDashboard extends Model
{
    protected $table = 'application_dashboard';

    protected $fillable = [
        'name',
        'about_page',
        'version',
    ];
}
