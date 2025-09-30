<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('playlists', function (Blueprint $table) {
            $table->id();
            $table->string('playlist_id')->unique();
            $table->string('creator_id');
            $table->string('creator_name')->nullable();
            $table->string('name');
            $table->string('description')->nullable();
            $table->text('cover')->nullable();
            $table->integer('track_count')->deafult(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('rankings', function (Blueprint $table) {
            $table->foreignId('playlist_id')->after('artist_id')->nullable();

            $table->foreignId('artist_id')->nullable()->change();
        });

        Schema::table('artists', function (Blueprint $table) {
            $table->string('artist_img')->nullable()->change();
        });
    }
};
