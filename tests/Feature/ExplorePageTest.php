<?php

use App\Models\Ranking;
use App\Models\User;

test('explore page loads', function () {
    $response = $this->get('/explore');
    $response->assertOk();
});

test('can explore public completed rankings on explore page', function() {
    $user = User::factory()
        ->has(Ranking::factory()->setVisibility(true)->setCompleted(true))
        ->create();

    $this->actingAs($user)->get(route('explore'))->assertOk();

    $response = $this->actingAs($user)->get(route('explore.pages'));
    $response->assertOk();
    $response->assertSee($user->rankings->first()->name);
});

test('can not explore public uncompleted rankings on explore page', function() {
    $user = User::factory()
        ->has(Ranking::factory()->setVisibility(true)->setCompleted(false))
        ->create();

    $this->actingAs($user)->get(route('explore'))->assertOk();

    $response = $this->actingAs($user)->get(route('explore.pages'));
    $response->assertOk();
    $response->assertDontSee($user->rankings->first()->name);
});

test('can not explore private completed rankings on explore page', function() {
    $user = User::factory()
        ->has(Ranking::factory()->setVisibility(false)->setCompleted(true))
        ->create();

    $this->actingAs($user)->get(route('explore'))->assertOk();

    $response = $this->actingAs($user)->get(route('explore.pages'));
    $response->assertOk();
    $response->assertDontSee($user->rankings->first()->name);
});

test('can not explore private uncompleted rankings on explore page', function() {
    $user = User::factory()
        ->has(Ranking::factory()->setVisibility(false)->setCompleted(false))
        ->create();

    $this->actingAs($user)->get(route('explore'))->assertOk();

    $response = $this->actingAs($user)->get(route('explore.pages'));
    $response->assertOk();
    $response->assertDontSee($user->rankings->first()->name);
});