<?php

use App\Models\ApplicationDashboard;

use function Pest\Laravel\get;

describe('support page', function () {
    test('loads successfully', function () {
        ApplicationDashboard::create([
            'name' => 'Song Rank',
            'version' => '2.0',
            'support_page' => '<p>Support Song Rank</p>',
        ]);

        get(route('support'))->assertOk();
    });
});
