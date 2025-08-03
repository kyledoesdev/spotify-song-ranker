<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserPreference;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'spotify_id' => 'spotify-id-'.str()->random(32),
            'name' => fake()->username(),
            'email' => fake()->unique()->safeEmail(),
            'avatar' => fake()->imageUrl(200, 200, 'avatar', true),
            'timezone' => fake()->timezone(),
            'ip_address' => fake()->localIpv4(),
            'external_token' => str()->random(32),
            'external_refresh_token' => str()->random(32),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (User $user) {
            UserPreference::factory()->create([
                'user_id' => $user->getKey(),
            ]);
        });
    }
}
