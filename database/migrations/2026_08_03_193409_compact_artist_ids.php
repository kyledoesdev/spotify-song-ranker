<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        /* Only MySQL burns ids this way, and the statements below are MySQL-only syntax.
           The test suite runs on SQLite, where this would fail outright. */
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        if (DB::table('artists')->count() === 0) {
            return;
        }

        $this->severDanglingReferences();
        $this->buildIdMap();
        $this->repointChildren();
        $this->rewriteArtistTable();

        DB::statement('DROP TABLE `artist_id_map`');
    }

    public function down(): void
    {
        throw new RuntimeException('compact_artist_ids cannot be reversed; the previous artist ids are not recoverable.');
    }

    /**
     * Null out child rows pointing at an artist that no longer exists. They are already
     * broken, and left alone the renumbering would silently attach them to whichever
     * artist inherits that id.
     */
    private function severDanglingReferences(): void
    {
        foreach (['songs', 'rankings'] as $table) {
            DB::table($table)
                ->whereNotNull('artist_id')
                ->whereNotIn('artist_id', fn ($query) => $query->select('id')->from('artists'))
                ->update(['artist_id' => null]);
        }
    }

    /**
     * Map every existing id to its position in the sequence. Ordering by the current id
     * keeps the relative order of the table intact.
     */
    private function buildIdMap(): void
    {
        DB::statement('DROP TABLE IF EXISTS `artist_id_map`');

        DB::statement('
            CREATE TABLE `artist_id_map` (
                `old_id` BIGINT UNSIGNED NOT NULL PRIMARY KEY,
                `new_id` BIGINT UNSIGNED NOT NULL,
                UNIQUE KEY `artist_id_map_new_id_unique` (`new_id`)
            )
        ');

        DB::statement('
            INSERT INTO `artist_id_map` (`old_id`, `new_id`)
            SELECT `id`, ROW_NUMBER() OVER (ORDER BY `id`) FROM `artists`
        ');
    }

    /**
     * These are plain foreign key columns with no uniqueness of their own, so they can be
     * rewritten in place with a join.
     */
    private function repointChildren(): void
    {
        DB::transaction(function () {
            foreach (['songs', 'rankings'] as $table) {
                DB::statement("
                    UPDATE `{$table}` AS child
                    JOIN `artist_id_map` AS map ON map.`old_id` = child.`artist_id`
                    SET child.`artist_id` = map.`new_id`
                ");
            }
        });
    }

    /**
     * Rebuild the table rather than updating ids in place. Rewriting a primary key row by
     * row risks colliding with an id the scan has not reached yet; copying into a fresh
     * table and swapping it in sidesteps that entirely.
     */
    private function rewriteArtistTable(): void
    {
        $columns = collect(Schema::getColumnListing('artists'));

        $target = $columns->map(fn (string $column) => "`{$column}`")->implode(', ');

        $source = $columns
            ->map(fn (string $column) => $column === 'id' ? 'map.`new_id`' : "artists.`{$column}`")
            ->implode(', ');

        DB::statement('DROP TABLE IF EXISTS `artists_renumbered`');
        DB::statement('CREATE TABLE `artists_renumbered` LIKE `artists`');

        DB::statement("
            INSERT INTO `artists_renumbered` ({$target})
            SELECT {$source}
            FROM `artists`
            JOIN `artist_id_map` AS map ON map.`old_id` = artists.`id`
        ");

        $suffix = Str::random(8);

        DB::statement("RENAME TABLE `artists` TO `artists_old_{$suffix}`, `artists_renumbered` TO `artists`");
        DB::statement("DROP TABLE `artists_old_{$suffix}`");

        $next = DB::table('artists')->max('id') + 1;

        DB::statement("ALTER TABLE `artists` AUTO_INCREMENT = {$next}");
    }
};
