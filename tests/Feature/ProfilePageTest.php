<?php

use App\Models\Ranking;
use App\Models\User;

test('profile page loads', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('profile.index') . "?user={$user->spotify_id}")
        ->assertOk();
});

test('profile page loaded user details', function() {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('profile.index') . "?user={$user->spotify_id}")
        ->assertOk();
        
    $this->actingAs($user)
        ->get(route('rank.pages', ['user' => $user->spotify_id]))
        ->assertOk()
        ->assertSeeHtml(get_formatted_name($user->name));
});

test('profile loaded user rankings', function() {
    $user = User::factory()
        ->has(Ranking::factory()->setVisibility(true)->setCompleted(true))
        ->create();

    $this->actingAs($user)
        ->get(route('profile.index') . "?user={$user->spotify_id}")
        ->assertOk();

    $this->actingAs($user)
        ->get(route('rank.pages', ['user' => $user->spotify_id]))
        ->assertOk()
        ->assertSee($user->rankings->first()->name);
});

test('profile shows unfinished rankings to profile owner only', function() {
    $user = User::factory()
        ->has(Ranking::factory()->setVisibility(true)->setCompleted(false))
        ->create();

    $otherUser = User::factory()->create();

    $this->actingAs($user)
        ->get(route('profile.index') . "?user={$user->spotify_id}")
        ->assertOk();

    $this->actingAs($user)
        ->get(route('rank.pages', ['user' => $user->spotify_id]))
        ->assertOk()
        ->assertSee($user->rankings->first()->name);

    $this->actingAs($otherUser)
        ->get(route('profile.index') . "?user={$user->spotify_id}")
        ->assertOk();

    $this->actingAs($otherUser)
        ->get(route('rank.pages', ['user' => $user->spotify_id]))
        ->assertOk()
        ->assertDontSee($user->rankings->first()->name);
});

test('profile shows private rankings to profile owner only', function() {
    $user = User::factory()
        ->has(Ranking::factory()->setVisibility(false)->setCompleted(true))
        ->create();

    $otherUser = User::factory()->create();

    $this->actingAs($user)
        ->get(route('profile.index') . "?user={$user->spotify_id}")
        ->assertOk();

    $this->actingAs($user)
        ->get(route('rank.pages', ['user' => $user->spotify_id]))
        ->assertOk()
        ->assertSee($user->rankings->first()->name);

    $this->actingAs($otherUser)
        ->get(route('profile.index') . "?user={$user->spotify_id}")
        ->assertOk();

    $this->actingAs($otherUser)
        ->get(route('rank.pages', ['user' => $user->spotify_id]))
        ->assertOk()
        ->assertDontSee($user->rankings->first()->name);
});
