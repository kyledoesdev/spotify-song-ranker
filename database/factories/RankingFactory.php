<?php

namespace Database\Factories;

use App\Models\Artist;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RankingFactory extends Factory
{
    private bool $isRanked = false;
    private bool $isPublic = true;
    private ?string $completedAt = null;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'artist_id' => Artist::factory(),
            'name' => fake()->userName() . ' List',
            'is_ranked' => $this->isRanked,
            'is_public' => $this->isPublic,
            'completed_at' => $this->completedAt,
        ];
    }

    public function setVisibility(bool $visiblity): void
    {
        $this->isPublic = $visiblity;
    }

    public function setCompleted(bool $completed): void
    {
        $this->isRanked = $completed;
        $this->completedAt = $completed ? now() : null;
    }
}
