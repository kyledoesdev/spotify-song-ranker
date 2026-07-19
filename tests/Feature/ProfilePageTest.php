<?php

use App\Models\Ranking;
use App\Models\User;

use function Pest\Laravel\actingAs;

describe('profile page', function () {
    test('shows the users completed public rankings', function () {
        $user = User::factory()->createOne();

        $ranking = publicCompletedRanking(['user_id' => $user->getKey()]);

        actingAs($user)
            ->get(route('profile', ['id' => $user->spotify_id]))
            ->assertOk()
            ->assertSee($ranking->name);
    });

    test('shows unfinished rankings to the profile owner only', function () {
        $user = User::factory()->createOne();
        $otherUser = User::factory()->createOne();

        $ranking = Ranking::factory()->create([
            'user_id' => $user->getKey(),
            'is_public' => true,
            'is_ranked' => false,
            'completed_at' => null,
        ]);

        actingAs($user)
            ->get(route('profile', ['id' => $user->spotify_id]))
            ->assertOk()
            ->assertSee($ranking->name);

        actingAs($otherUser)
            ->get(route('profile', ['id' => $user->spotify_id]))
            ->assertOk()
            ->assertDontSee($ranking->name);
    });

    test('shows private rankings to the profile owner only', function () {
        $user = User::factory()->createOne();
        $otherUser = User::factory()->createOne();

        $ranking = Ranking::factory()->create([
            'user_id' => $user->getKey(),
            'is_public' => false,
            'is_ranked' => true,
            'completed_at' => now(),
        ]);

        actingAs($user)
            ->get(route('profile', ['id' => $user->spotify_id]))
            ->assertOk()
            ->assertSee($ranking->name);

        actingAs($otherUser)
            ->get(route('profile', ['id' => $user->spotify_id]))
            ->assertOk()
            ->assertDontSee($ranking->name);
    });
});
