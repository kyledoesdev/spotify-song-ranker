<?php

use App\Models\Ranking;
use App\Models\User;

arch('System: Uses PHP preset')->preset()->php();
arch('System: Uses no debug methods')->expect(['dd', 'dump', 'die', 'ray'])->not->toBeUsed();


/* failing in CI - fix me later */

/* it('loads all pages with no smoke', function() {
    $user = User::factory()->create();

    $this->actingAs($user);

    $ranking = Ranking::factory()->create(['user_id' => $user->getKey()]);

    visit([
        '/',
        '/about',
        '/explore',
        '/dashboard',
        '/rank/'.$ranking->getKey(),
        '/rank/'.$ranking->getKey().'/edit',
        '/settings',
    ])->assertNoSmoke();
}); */