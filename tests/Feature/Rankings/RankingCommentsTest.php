<?php

use App\Models\Artist;
use App\Models\Ranking;
use App\Models\Song;
use App\Models\User;

beforeEach(function () {
    Artist::factory()->create();
    User::factory()->create();
});

test('displays comments component when comments are enabled', function () {
    $user = User::factory()->create();

    $ranking = Ranking::factory()
        ->has(Song::factory()->count(5))
        ->create([
            'user_id' => $user->getKey(),
            'is_public' => true,
            'comments_enabled' => true,
            'is_ranked' => true,
        ]);

    $this->actingAs($user)
        ->get(route('ranking', ['id' => $ranking->getKey()]))
        ->assertOk()
        ->assertSeeLivewire('comments');
});

test('hides comments component when comments are disabled', function () {
    $user = User::factory()->create();

    $ranking = Ranking::factory()
        ->has(Song::factory()->count(5))
        ->create([
            'user_id' => $user->getKey(),
            'is_public' => true,
            'comments_enabled' => false,
            'is_ranked' => true,
        ]);

    $this->actingAs($user)
        ->get(route('ranking', ['id' => $ranking->getKey()]))
        ->assertOk()
        ->assertDontSeeLivewire('comments');
});
