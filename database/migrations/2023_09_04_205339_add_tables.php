<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('artists', function (Blueprint $table) {
            $table->id();
            $table->string('artist_id');
            $table->string('artist_name');
            $table->string('artist_img');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('rankings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('artist_id');
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('songs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ranking_id');
            $table->string('spotify_song_id');
            $table->string('title');
            $table->unsignedBigInteger('rank')->default(0);
            $table->string('cover');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('artists');
        Schema::dropIfExists('rankings');
        Schema::dropIfExists('songs');
    }
};
