<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Deleting an account erases the Spotify tokens, so the columns have to accept null.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('external_token')->nullable()->change();
            $table->text('external_refresh_token')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('external_token')->nullable(false)->change();
            $table->text('external_refresh_token')->nullable(false)->change();
        });
    }
};
