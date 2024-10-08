<?php

use App\Models\User;

test('home page loads', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('home'))
        ->assertOk()
        ->assertSee($user->name);
});
