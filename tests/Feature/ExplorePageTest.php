<?php

use App\Models\Ranking;
use App\Models\User;

test('explore page loads', function () {
    $this->get(route('explore.index'))->assertOk();
});

test('can explore public completed rankings on explore page', function () {
    $user = User::factory()->create();

    $ranking = Ranking::factory()->create([
        'user_id' => $user->getKey(),
        'is_public' => true,
        'is_ranked' => true,
        'completed_at' => now(),
    ]);

    $this->actingAs($user)->get(route('explore.index'))->assertOk();

    $response = $this->actingAs($user)->get(route('explore.pages'));
    $response->assertOk();
    $response->assertSee($user->rankings->first()->name);
});

test('can not explore public uncompleted rankings on explore page', function () {
    $user = User::factory()->create();

    $ranking = Ranking::factory()->create([
        'user_id' => $user->getKey(),
        'is_public' => true,
        'is_ranked' => false,
        'completed_at' => null,
    ]);

    $this->actingAs($user)->get(route('explore.index'))->assertOk();

    $response = $this->actingAs($user)->get(route('explore.pages'));
    $response->assertOk();
    $response->assertDontSee($user->rankings->first()->name);
});

test('can not explore private completed rankings on explore page', function () {
    $user = User::factory()->create();

    $ranking = Ranking::factory()->create([
        'user_id' => $user->getKey(),
        'is_public' => false,
        'is_ranked' => true,
        'completed_at' => now(),
    ]);

    $this->actingAs($user)->get(route('explore.index'))->assertOk();

    $response = $this->actingAs($user)->get(route('explore.pages'));
    $response->assertOk();
    $response->assertDontSee($user->rankings->first()->name);
});

test('can not explore private uncompleted rankings on explore page', function () {
    $user = User::factory()->create();

    $ranking = Ranking::factory()->create([
        'user_id' => $user->getKey(),
        'is_public' => false,
        'is_ranked' => false,
        'completed_at' => null,
    ]);

    $this->actingAs($user)->get(route('explore.index'))->assertOk();

    $this->actingAs($user)
        ->get(route('explore.pages'))
        ->assertOk()
        ->assertDontSee($user->rankings->first()->name);
});
