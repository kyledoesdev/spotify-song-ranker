<?php

use App\Models\User;

test('settings page loads', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('settings.index'))
        ->assertOk()
        ->assertSee("Settings & Preferences");
});
