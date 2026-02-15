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
            $table->longText('support_page')->nullable()->after('about_page');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('application_dashboard', function (Blueprint $table) {
            $table->dropColumn('support_page');
        });
    }
};
