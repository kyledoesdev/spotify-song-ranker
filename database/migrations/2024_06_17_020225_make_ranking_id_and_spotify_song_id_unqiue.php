<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('songs', function (Blueprint $table) {
            $table->unique(['ranking_id', 'spotify_song_id']);
        });
    }

    public function down(): void {
        Schema::table('songs', function (Blueprint $table) {
            $table->dropUnique(['ranking_id', 'spotify_song_id']);
        });
    }
};
