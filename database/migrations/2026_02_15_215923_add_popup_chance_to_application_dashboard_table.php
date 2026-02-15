<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('application_dashboard', function (Blueprint $table) {
            $table->unsignedInteger('popup_chance')->default(8)->after('seo_terms');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('application_dashboard', function (Blueprint $table) {
            $table->dropColumn('popup_chance');
        });
    }
};
