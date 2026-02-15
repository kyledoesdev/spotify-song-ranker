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