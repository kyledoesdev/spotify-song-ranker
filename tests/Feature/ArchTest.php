<?php

use App\Models\Ranking;
use App\Models\User;

arch('System: Uses PHP preset')->preset()->php();
arch('System: Uses no debug methods')->expect(['dd', 'dump', 'die', 'ray'])->not->toBeUsed();


it('loads all pages with no smoke', function() {
    $user = User::factory()->create();

    $this->actingAs($user);

    $ranking = Ranking::factory()->create(['user_id' => $user]);

    visit([
        '/',
        '/about',
        '/explore',
        '/dashboard',
        '/rank/'.$ranking->getKey(),
        '/rank/'.$ranking->getKey().'/edit',
        '/settings',
    ])->assertNoSmoke();
});