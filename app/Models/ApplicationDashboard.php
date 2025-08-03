<?php

namespace App\Models;

class ApplicationDashboard extends Model
{
    protected $table = 'application_dashboard';

    protected $fillable = [
        'name',
        'about_page',
        'version',
    ];
}
