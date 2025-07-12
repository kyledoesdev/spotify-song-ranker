<?php

use App\Models\Song;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('songs', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->after('spotify_song_id');
        });

        DB::transaction(function () {
            Song::query()->withTrashed()->chunk(1000, function ($songs) {
                $songs->each(fn (Song $song) => $song->update(['uuid' => Str::uuid()]));
            });
        });

        Schema::table('songs', function (Blueprint $table) {
            $table->uuid('uuid')->nullable(false)->change();
        });

        Schema::table('songs', function (Blueprint $table) {
            $table->dropUnique('songs_ranking_id_spotify_song_id_unique');
            $table->unique(['ranking_id', 'uuid']);
        });
    }
};
