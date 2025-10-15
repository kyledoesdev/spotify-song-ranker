<?php

namespace Database\Factories;

use App\Models\Artist;
use App\Models\Ranking;
use App\Models\Song;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RankingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'artist_id' => Artist::factory(),
            'name' => fake()->userName().' List',
            'is_ranked' => false,
            'is_public' => false,
            'completed_at' => null,
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Ranking $ranking) {
            for ($rank = 1; $rank <= 10; $rank++) {
                Song::factory()->create([
                    'ranking_id' => $ranking->getKey(),
                    'rank' => $rank
                ]);
            }
        });
    }
}
