<?php

use App\Enums\RankingType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rankings', function (Blueprint $table) {
            $table->unsignedBigInteger('source_id')->nullable()->after('user_id');
            $table->string('type')->nullable()->after('source_id');
        });

        DB::transaction(function () {
            $this->backfillSourceColumns();
        });

        Schema::table('rankings', function (Blueprint $table) {
            $table->index(['type', 'source_id']);
        });

        Schema::table('rankings', function (Blueprint $table) {
            $table->dropColumn(['artist_id', 'playlist_id', 'show_id']);
        });
    }

    public function down(): void
    {
        Schema::table('rankings', function (Blueprint $table) {
            $table->unsignedBigInteger('artist_id')->nullable()->after('user_id');
            $table->unsignedBigInteger('playlist_id')->nullable()->after('artist_id');
            $table->unsignedBigInteger('show_id')->nullable()->after('playlist_id');
        });

        DB::transaction(function () {
            foreach ($this->columnForType() as $type => $column) {
                DB::table('rankings')
                    ->where('type', $type)
                    ->whereNotNull('source_id')
                    ->update([$column => DB::raw('source_id')]);
            }
        });

        Schema::table('rankings', function (Blueprint $table) {
            $table->dropIndex(['type', 'source_id']);
        });

        Schema::table('rankings', function (Blueprint $table) {
            $table->dropColumn(['type', 'source_id']);
        });
    }

    private function columnForType(): array
    {
        return [
            RankingType::SHOW->value => 'show_id',
            RankingType::PLAYLIST->value => 'playlist_id',
            RankingType::ARTIST->value => 'artist_id',
        ];
    }

    private function backfillSourceColumns(): void
    {
        foreach ($this->columnForType() as $type => $column) {
            DB::table('rankings')
                ->whereNull('type')
                ->whereNotNull($column)
                ->update([
                    'type' => $type,
                    'source_id' => DB::raw($column),
                ]);
        }

        /* A row with no source id at all read as an artist ranking before; keep it that way. */
        DB::table('rankings')
            ->whereNull('type')
            ->update(['type' => RankingType::ARTIST->value]);
    }
};
