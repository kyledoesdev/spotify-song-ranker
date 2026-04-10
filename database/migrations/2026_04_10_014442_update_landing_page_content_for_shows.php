<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('landing_page_contents')
            ->where('slug', 'rank-types-podcasts-title')
            ->update(['text' => 'Podcast & Show Episodes']);

        DB::table('landing_page_contents')
            ->where('slug', 'rank-types-podcasts-text')
            ->update(['text' => 'Rank episodes of your favorite podcasts or shows. Enter the show link to rank the entire catalog. You can even rank audiobooks!']);
    }

    public function down(): void
    {
        DB::table('landing_page_contents')
            ->where('slug', 'rank-types-podcasts-title')
            ->update(['text' => 'Podcast Episodes']);

        DB::table('landing_page_contents')
            ->where('slug', 'rank-types-podcasts-text')
            ->update(['text' => 'Rank episodes of your favorite podcasts. Great for recommending the best starting points to new listeners.']);
    }
};
