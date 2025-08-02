<?php

use App\Models\Ranking;
use App\Models\Song;
use App\Models\User;

/* test('can start ranking with valid request', function () {
    $request = [
        'artist_id' => str()->random(16),
        'artist_name' => 'Local Natives',
        'artist_img' => 'https://api.dicebear.com/7.x/initials/svg?seed=testing',
        'name' => 'Local Natives List',
        'is_public' => true,
        'songs' => [
            'Ceilings' => [
                'id' => str()->random(22),
                'name' => 'Ceilings',
                'cover' => 'https://api.dicebear.com/7.x/initials/svg?seed=ceilings',
            ],
            'Sun Hands' => [
                'id' => str()->random(22),
                'name' => 'Sun Hands',
                'cover' => 'https://api.dicebear.com/7.x/initials/svg?seed=sun_hands',
            ],
            'Featherweight' => [
                'id' => str()->random(22),
                'name' => 'Featherweight',
                'cover' => 'https://api.dicebear.com/7.x/initials/svg?seed=featherweight',
            ],
        ],
    ];

    $user = User::factory()->create();

    $this->assertDatabaseMissing('artists', [
        'artist_id' => $request['artist_id'],
        'artist_name' => $request['artist_name'],
        'artist_img' => $request['artist_img'],
    ]);

    $this->assertDatabaseMissing('rankings', [
        'name' => 'Local Natives List',
    ]);

    $response = $this->actingAs($user)
        ->postJson(route('rank.create'), $request)
        ->assertOk();

    $this->assertDatabaseHas('artists', [
        'artist_id' => $request['artist_id'],
        'artist_name' => $request['artist_name'],
        'artist_img' => $request['artist_img'],
    ]);

    $this->assertDatabaseHas('rankings', [
        'name' => 'Local Natives List',
    ]);
}); */

test('ranking owner can view the ranking edit page', function () {
    $user = User::factory()
        ->has(Ranking::factory()->count(1))
        ->create();

    $this->actingAs($user)
        ->get(route('rank.edit', ['id' => $user->rankings->first()->getKey()]))
        ->assertOk();
});

test('ranking non-owner can not view a ranking edit page that does not belong to them', function () {
    $user = User::factory()->create();
    $ranking = Ranking::factory()->create();

    $this->actingAs($user)
        ->get(route('rank.edit', ['id' => $ranking->getKey()]))
        ->assertForbidden();
});

/* test('ranking owner can update ranking name and visibility', function () {
    $user = User::factory()->create();

    $ranking = Ranking::factory()
        ->has(Song::factory()->count(5))
        ->create(['user_id' => $user->getKey(), 'is_public' => false]);

    $this->assertDatabaseHas('rankings', [
        'id' => $ranking->getKey(),
        'user_id' => $user->getKey(),
        'name' => $ranking->name,
        'is_public' => false,
    ]);

    $this->actingAs($user)
        ->postJson(route('rank.update', ['id' => $ranking->getKey()]), [
            'name' => 'new name',
            'is_public' => true,
        ])
        ->assertOk();

    $this->assertDatabaseHas('rankings', [
        'id' => $ranking->getKey(),
        'user_id' => $user->getKey(),
        'name' => 'new name',
        'is_public' => true,
    ]);
}); */

/* test('ranking non-owner can not update ranking name and visibility', function () {
    $user = User::factory()->create();

    $ranking = Ranking::factory()
        ->has(Song::factory()->count(5))
        ->create(['is_public' => false]);

    $this->assertDatabaseHas('rankings', [
        'id' => $ranking->getKey(),
        'user_id' => $ranking->user->getKey(),
        'name' => $ranking->name,
        'is_public' => false,
    ]);

    $this->actingAs($user)
        ->postJson(route('rank.update', ['id' => $ranking->getKey()]), [
            'name' => 'new name',
            'is_public' => true,
        ])
        ->assertForbidden();
}); */

/* test('ranking owner can delete their ranking', function () {
    $user = User::factory()
        ->has(Ranking::factory()->has(Song::factory()->count(10)))
        ->create();

    $rankingId = $user->rankings->first()->getKey();

    expect(null, Ranking::findOrFail($rankingId)->deleted_at);
    expect(10, Song::where('ranking_id', $rankingId)->count());

    $this->actingAs($user)
        ->postJson(route('rank.destroy'), ['rankingId' => $rankingId])
        ->assertOk();

    expect(now(), Ranking::withTrashed()->findOrFail($rankingId)->deleted_at);
    expect(0, Song::where('ranking_id', $rankingId)->count());
}); */

/* test('ranking non-owner can not delete someone elses rankings', function () {
    $user = User::factory()
        ->has(Ranking::factory()->has(Song::factory()->count(10)))
        ->create();

    $otherUser = User::factory()->create();

    $rankingId = $user->rankings->first()->getKey();

    expect(null, Ranking::findOrFail($rankingId)->deleted_at);
    expect(10, Song::where('ranking_id', $rankingId)->count());

    $this->actingAs($otherUser)
        ->postJson(route('rank.destroy'), ['rankingId' => $rankingId])
        ->assertForbidden();

    expect(null, Ranking::withTrashed()->findOrFail($rankingId)->deleted_at);
    expect(10, Song::where('ranking_id', $rankingId)->count());
}); */
