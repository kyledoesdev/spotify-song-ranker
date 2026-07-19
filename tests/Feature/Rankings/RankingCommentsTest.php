<?php

use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

describe('ranking comments', function () {
    test('displays comments component when comments are enabled', function () {
        $user = User::factory()->createOne();

        $ranking = publicCompletedRanking([
            'user_id' => $user->getKey(),
            'comments_enabled' => true,
        ]);

        actingAs($user)
            ->get(route('ranking', ['id' => $ranking->getKey()]))
            ->assertOk()
            ->assertSeeLivewire('comments');
    });

    test('hides comments component when comments are disabled', function () {
        $user = User::factory()->createOne();

        $ranking = publicCompletedRanking([
            'user_id' => $user->getKey(),
            'comments_enabled' => false,
        ]);

        actingAs($user)
            ->get(route('ranking', ['id' => $ranking->getKey()]))
            ->assertOk()
            ->assertDontSeeLivewire('comments');
    });
});

describe('ranking comment replies', function () {
    test('allows replies when comment replies are enabled', function () {
        $ranking = publicCompletedRanking([
            'comments_enabled' => true,
            'comments_replies_enabled' => true,
        ]);

        get(route('ranking', ['id' => $ranking->getKey()]))
            ->assertOk()
            ->assertSee('&quot;showReplies&quot;:true', false);
    });

    test('disables replies when comment replies are disabled', function () {
        $ranking = publicCompletedRanking([
            'comments_enabled' => true,
            'comments_replies_enabled' => false,
        ]);

        get(route('ranking', ['id' => $ranking->getKey()]))
            ->assertOk()
            ->assertSee('&quot;showReplies&quot;:false', false);
    });
});
