<?php

use App\Livewire\Dashboard\Dashboard;
use App\Models\Artist;
use App\Models\User;
use Livewire\Livewire;

test('home page loads', function () {
    $user = User::factory()->create();

    Artist::factory()->create();

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->assertOk()
        ->assertSee('Search for an artist to rank to get started.');
});
