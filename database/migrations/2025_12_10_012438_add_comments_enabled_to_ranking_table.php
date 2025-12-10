<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('rankings', function (Blueprint $table) {
            $table->boolean('comments_enabled')->default(true)->after('is_public');
            $table->boolean('comments_replies_enabled')->default(true)->after('comments_enabled');
        });
    }
};
