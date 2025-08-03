<?php

use App\Livewire\Profile\Profile;
use App\Livewire\Ranking\EditRanking;
use App\Livewire\SongRank\SongRankSetup;
use App\Models\Artist;
use App\Models\Ranking;
use App\Models\Song;
use App\Models\User;
use Livewire\Livewire;

beforeEach(fn () => Artist::factory()->create());

test('can start ranking with valid request', function () {
    $user = User::factory()->create();

    $this->assertDatabaseMissing('artists', [
        'artist_id' => 'test-artist-id',
        'artist_name' => 'Local Natives',
    ]);

    $this->assertDatabaseMissing('rankings', [
        'name' => 'Local Natives List',
    ]);

    Livewire::actingAs($user)
        ->test(SongRankSetup::class)
        ->set('selectedArtist', [
            'id' => 'test-artist-id',
            'name' => 'Local Natives',
            'cover' => 'https://api.dicebear.com/7.x/initials/svg?seed=testing',
        ])
        ->set('selectedArtistTracks', collect([
            [
                'id' => 'ceilings-id',
                'name' => 'Ceilings',
                'cover' => 'https://api.dicebear.com/7.x/initials/svg?seed=ceilings',
            ],
            [
                'id' => 'sun-hands-id',
                'name' => 'Sun Hands',
                'cover' => 'https://api.dicebear.com/7.x/initials/svg?seed=sun_hands',
            ],
            [
                'id' => 'featherweight-id',
                'name' => 'Featherweight',
                'cover' => 'https://api.dicebear.com/7.x/initials/svg?seed=featherweight',
            ],
        ]))
        ->set('form.name', 'Local Natives List')
        ->set('form.is_public', true)
        ->call('beginRanking')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('artists', [
        'artist_id' => 'test-artist-id',
        'artist_name' => 'Local Natives',
    ]);

    $this->assertDatabaseHas('rankings', [
        'name' => 'Local Natives List',
    ]);
});

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

test('ranking owner can update ranking name and visibility', function () {
    $user = User::factory()->create();

    $ranking = Ranking::factory()
        ->has(Song::factory()->count(5))
        ->create(['user_id' => $user->getKey(), 'is_public' => false]);

    Livewire::actingAs($user)
        ->test(EditRanking::class, ['id' => $ranking->getKey()])
        ->set('form.name', 'new name')
        ->set('form.is_public', true)
        ->call('update')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('rankings', [
        'id' => $ranking->getKey(),
        'user_id' => $user->getKey(),
        'name' => 'new name',
        'is_public' => true,
    ]);
});

test('ranking non-owner can not update ranking name and visibility', function () {
    $user = User::factory()->create();

    $ranking = Ranking::factory()
        ->has(Song::factory()->count(5))
        ->create(['is_public' => false]);

    Livewire::actingAs($user)
        ->test(EditRanking::class, ['id' => $ranking->getKey()])
        ->assertForbidden();
});

test('ranking owner can delete their ranking', function () {
    $user = User::factory()
        ->has(Ranking::factory()->has(Song::factory()->count(10)))
        ->create();

    $ranking = $user->rankings->first();

    expect($ranking->deleted_at)->toBeNull();
    expect(Song::where('ranking_id', $ranking->getKey())->count())->toBe(10);

    $this->actingAs($user)
        ->get(route('profile', ['id' => $user->spotify_id]))
        ->assertOk();

    Livewire::actingAs($user)
        ->test(Profile::class, ['id' => $user->spotify_id])
        ->call('destroy', $ranking->getKey())
        ->assertHasNoErrors();

    expect(Ranking::withTrashed()->find($ranking->getKey())->deleted_at)->not->toBeNull();
    expect(Song::where('ranking_id', $ranking->getKey())->count())->toBe(0);
});

test('ranking non-owner can not delete someone elses rankings', function () {
    $user = User::factory()
        ->has(Ranking::factory()->has(Song::factory()->count(10)))
        ->create();

    $otherUser = User::factory()->create();
    $ranking = $user->rankings->first();

    expect($ranking->deleted_at)->toBeNull();
    expect(Song::where('ranking_id', $ranking->getKey())->count())->toBe(10);

    Livewire::actingAs($otherUser)
        ->test(Profile::class, ['id' => $user->spotify_id])
        ->call('destroy', $ranking->getKey())
        ->assertForbidden();

    expect($ranking->fresh()->deleted_at)->toBeNull();
    expect(Song::where('ranking_id', $ranking->getKey())->count())->toBe(10);
});
