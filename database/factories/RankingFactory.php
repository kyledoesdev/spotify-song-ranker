<?php

namespace Database\Factories;

use App\Enums\RankingType;
use App\Models\Artist;
use App\Models\Playlist;
use App\Models\Ranking;
use App\Models\Show;
use App\Models\Song;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ranking>
 */
class RankingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'type' => RankingType::ARTIST->value,
            'source_id' => Artist::factory(),
            'name' => fake()->userName().' List',
            'is_ranked' => false,
            'is_public' => false,
            'completed_at' => null,
        ];
    }

    public function artist(?Artist $artist = null): static
    {
        return $this->for($artist ?? Artist::factory(), 'source');
    }

    public function playlist(?Playlist $playlist = null): static
    {
        return $this->for($playlist ?? Playlist::factory(), 'source');
    }

    public function show(?Show $show = null): static
    {
        return $this->for($show ?? Show::factory(), 'source');
    }

    public function configure()
    {
        return $this->afterCreating(function (Ranking $ranking) {
            for ($rank = 1; $rank <= 10; $rank++) {
                Song::factory()->create([
                    'ranking_id' => $ranking->getKey(),
                    'rank' => $rank,
                ]);
            }
        });
    }
}
