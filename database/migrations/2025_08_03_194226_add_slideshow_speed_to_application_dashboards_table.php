<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('application_dashboard', function (Blueprint $table) {
            $table->integer('slideshow_speed')->after('about_page')->default(30);
        });
    }
};
