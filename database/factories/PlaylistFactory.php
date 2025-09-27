<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PlaylistFactory extends Factory
{
    public function definition(): array
    {
        return [
            'playlist_id' => str()->random(16),
            'creator_id' => str()->random(16),
            'creator_name' => fake()->userName(),
            'name' => fake()->sentance(1),
            'description' => fake()->sentance(1),
            'cover' => fake()->imageUrl(200, 200, 'playlist', true),
            'track_count' => rand(5, 100),
        ];
    }
}
