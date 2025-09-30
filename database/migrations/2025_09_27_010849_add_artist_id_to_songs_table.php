<?php

use App\Models\Ranking;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('songs', function (Blueprint $table) {
            $table->foreignId('artist_id')->after('ranking_id')->nullable();
        });

        DB::transaction(function () {
            Ranking::query()
                ->withTrashed()
                ->with('artist', 'songs')
                ->get()
                ->each(function (Ranking $ranking) {
                    $ranking->songs()->withTrashed()->update(['artist_id' => $ranking->artist->getKey()]);
                });
        });
    }
};
