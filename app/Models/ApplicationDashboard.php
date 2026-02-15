<?php

namespace App\Models;

class ApplicationDashboard extends Model
{
    protected $table = 'application_dashboard';

    protected $fillable = [
        'name',
        'about_page',
        'support_page',
        'version',
        'slideshow_speed',
        'seo_terms',
        'popup_chance',
    ];
}
