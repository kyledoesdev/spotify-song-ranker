<?php

namespace Database\Seeders;

use App\Models\Ranking;
use App\Models\Song;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Ranking::factory()
            ->count(10)
            ->create([
                'is_public' => true,
                'is_ranked' => true,
                'completed_at' => now(),
            ])->each(function ($ranking) {
                Song::factory()->count(10)->create([
                    'ranking_id' => $ranking->id,
                ])->each(function ($song, $index) {
                    $song->update([
                        'rank' => $index + 1,
                    ]);
                });
            });
    }
}
