<?php

namespace Database\Factories;

use App\Models\Ranking;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SongFactory extends Factory
{
    public function definition(): array
    {
        return [
            'ranking_id' => Ranking::factory(),
            'spotify_song_id' => str()->random(16),
            'title' => fake()->sentence(rand(1, 5)),
            'rank' => 0,
            'cover' => fake()->imageUrl(200, 200, 'song', true),
            'uuid' => Str::uuid(),
        ];
    }
}
