<?php

use App\Models\Ranking;
use App\Models\User;

test('profile page loads', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('profile.show', ['id' => $user->spotify_id]))
        ->assertOk();
});

test('profile loaded user rankings', function () {
    $user = User::factory()->create();

    $ranking = Ranking::factory()->create([
        'user_id' => $user->getKey(),
        'is_public' => true,
        'is_ranked' => true,
        'completed_at' => now(),
    ]);

    $this->actingAs($user)
        ->get(route('profile.show', ['id' => $user->spotify_id]))
        ->assertOk()
        ->assertSee($user->rankings->first()->name);
});

test('profile shows unfinished rankings to profile owner only', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $ranking = Ranking::factory()->create([
        'user_id' => $user->getKey(),
        'is_public' => true,
        'is_ranked' => false,
        'completed_at' => null,
    ]);

    $this->actingAs($user)
        ->get(route('profile.show', ['id' => $user->spotify_id]))
        ->assertOk()
        ->assertSee($user->rankings->first()->name);

    $this->actingAs($otherUser)
        ->get(route('profile.show', ['id' => $user->spotify_id]))
        ->assertOk()
        ->assertDontSee($user->rankings->first()->name);
});

test('profile shows private rankings to profile owner only', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $ranking = Ranking::factory()->create([
        'user_id' => $user->getKey(),
        'is_public' => false,
        'is_ranked' => true,
        'completed_at' => now(),
    ]);

    $this->actingAs($user)
        ->get(route('profile.show', ['id' => $user->spotify_id]))
        ->assertOk()
        ->assertSee($user->rankings->first()->name);

    $this->actingAs($otherUser)
        ->get(route('profile.show', ['id' => $user->spotify_id]))
        ->assertOk()
        ->assertDontSee($user->rankings->first()->name);
});
