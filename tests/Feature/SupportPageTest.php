<?php

use App\Models\ApplicationDashboard;

test('support page loads successfully', function () {
    ApplicationDashboard::create([
        'name' => 'Song Rank',
        'version' => '2.0',
        'support_page' => '<p>Support Song Rank</p>',
    ]);

    $this->get(route('support'))->assertOk();
});

test('support page displays support content', function () {
    ApplicationDashboard::create([
        'name' => 'Song Rank',
        'version' => '2.0',
        'support_page' => '<h1>Support Us</h1><p>Please consider donating!</p>',
    ]);

    $this->get(route('support'))
        ->assertOk()
        ->assertSee('Support Us')
        ->assertSee('Please consider donating!');
});
