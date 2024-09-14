<?php

namespace Database\Factories;

use App\Models\Artist;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RankingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'artist_id' => Artist::factory(),
            'name' => fake()->userName() . ' List',
            'is_ranked' => false,
            'is_public' => false,
            'completed_at' => null,
        ];
    }

    public function setVisibility(bool $visibility): self
    {
        return $this->state(function (array $attributes) use ($visibility) {
            return [
                'is_public' => $visibility,
            ];
        });
    }

    public function setCompleted(bool $completed): self
    {
        return $this->state(function (array $attributes) use ($completed) {
            return [
                'is_ranked' => $completed,
                'completed_at' => $completed ? now() : null,
            ];
        });
    }
}
