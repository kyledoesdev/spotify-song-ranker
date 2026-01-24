<?php

use App\Enums\RankingType;
use App\Livewire\Ranking\Card as ProfileRankingCard;
use App\Livewire\Ranking\EditRanking;
use App\Livewire\SongRank\SongRankProcess;
use App\Livewire\SongRank\SongRankSetup;
use App\Models\Artist;
use App\Models\Playlist;
use App\Models\Ranking;
use App\Models\RankingSortingState;
use App\Models\Song;
use App\Models\User;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;

beforeEach(function () {
    Artist::factory()->create();
    User::factory()->create();
});

describe('Ranking Creation', function () {
    test('can create a ranking for an artist', function () {
        expect(Artist::where('artist_id', 'test-artist-id')->exists())->toBeFalse();
        expect(Ranking::where('name', 'Local Natives List')->exists())->toBeFalse();

        Livewire::actingAs(User::first())
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
                    'uuid' => str()->uuid()->toString()
                ],
                [
                    'id' => 'sun-hands-id',
                    'name' => 'Sun Hands',
                    'cover' => 'https://api.dicebear.com/7.x/initials/svg?seed=sun_hands',
                    'uuid' => str()->uuid()->toString()
                ],
                [
                    'id' => 'featherweight-id',
                    'name' => 'Featherweight',
                    'cover' => 'https://api.dicebear.com/7.x/initials/svg?seed=featherweight',
                    'uuid' => str()->uuid()->toString()
                ],
            ]))
            ->set('type', RankingType::ARTIST)
            ->set('form.name', 'Local Natives List')
            ->set('form.is_public', true)
            ->call('beginRanking')
            ->assertHasNoErrors();

        $artist = Artist::where('artist_id', 'test-artist-id')->first();
        $ranking = Ranking::where('name', 'Local Natives List')->first();

        expect($artist)->not->toBeNull();
        expect($artist->artist_name)->toBe('Local Natives');

        expect($ranking)->not->toBeNull();
        expect($ranking->songs()->count())->toBe(3);
    });

    test('can create a ranking for a playlist', function () {
        expect(Playlist::where('playlist_id', 'test-playlist-id')->exists())->toBeFalse();
        expect(Ranking::where('name', 'Local Natives List')->exists())->toBeFalse();

        Livewire::actingAs(User::first())
            ->test(SongRankSetup::class)
            ->set('selectedPlaylist', [
                'id' => 'test-playlist-id',
                'name' => 'Playlist Name',
                'description' => 'Playlist Description',
                'creator' => [
                    'display_name' => 'test-creator-name',
                    'id' => 'test-creator-id',
                ],
                'cover' => 'playlist-cover',
                'track_count' => 3,
            ])
            ->set('selectedPlaylistTracks', collect([
                [
                    'id' => 'ceilings-id',
                    'name' => 'Ceilings',
                    'cover' => 'https://api.dicebear.com/7.x/initials/svg?seed=ceilings',
                    'uuid' => str()->uuid()->toString(),
                    'artist_id' => 'local-natives-id',
                    'artist_name' => 'Local Natives',
                    'is_podcast' => false,
                ],
                [
                    'id' => 'sun-hands-id',
                    'name' => 'Sun Hands',
                    'cover' => 'https://api.dicebear.com/7.x/initials/svg?seed=sun_hands',
                    'uuid' => str()->uuid()->toString(),
                    'artist_id' => 'local-natives-id',
                    'artist_name' => 'Local Natives',
                    'is_podcast' => false,
                ],
                [
                    'id' => 'featherweight-id',
                    'name' => 'Featherweight',
                    'cover' => 'https://api.dicebear.com/7.x/initials/svg?seed=featherweight',
                    'uuid' => str()->uuid()->toString(),
                    'artist_id' => 'local-natives-id',
                    'artist_name' => 'Local Natives',
                    'is_podcast' => false,
                ],
            ]))
            ->set('type', RankingType::PLAYLIST)
            ->set('form.name', 'Local Natives List')
            ->set('form.is_public', true)
            ->call('beginRanking')
            ->assertHasNoErrors();

        $playlist = Playlist::where('playlist_id', 'test-playlist-id')->first();
        $ranking = Ranking::where('name', 'Local Natives List')->first();

        expect($playlist)->not->toBeNull();
        expect($playlist->creator_id)->toBe('test-creator-id');
        expect($playlist->creator_name)->toBe('test-creator-name');
        expect($playlist->name)->toBe('Playlist Name');
        expect($playlist->description)->toBe('Playlist Description');
        expect($playlist->cover)->toBe('playlist-cover');
        expect($playlist->track_count)->toBe(3);

        expect($ranking)->not->toBeNull();
        expect($ranking->songs()->count())->toBe(3);
    });

    test('unauthenticated user cannot access ranking setup', function () {
        $this->get(route('dashboard'))->assertRedirect(route('welcome'));
    });

    test('cannot search for a playlist with an invalid URL', function () {
        Livewire::actingAs(User::first())
            ->test(SongRankSetup::class)
            ->set('playlistURL', 'abcdxyz')
            ->call('searchPlaylist')
            ->assertSet('selectedPlaylist', [])
            ->assertSet('selectedPlaylistTracks', null);
    });

    test('can search for a playlist with a valid URL', function () {
        Livewire::actingAs(User::first())
            ->test(SongRankSetup::class)
            ->set('playlistURL', 'https://open.spotify.com/playlist/1l9ToABW4nh8EdGfq3Qvei')
            ->call('searchPlaylist')
            ->assertSet('type', RankingType::PLAYLIST);
    });

    test('cannot search for an artist with an empty search term', function () {
        Livewire::actingAs(User::first())
            ->test(SongRankSetup::class)
            ->set('artistSearchTerm', '')
            ->call('searchArtist')
            ->assertSet('searchedArtists', null)
            ->assertSet('selectedArtistTracks', null);
    });
});

describe('Ranking Management and Authorization', function () {
    test('owner can view the edit page', function () {
        $user = User::factory()
            ->has(Ranking::factory()->count(1))
            ->create();

        $this->actingAs($user)
            ->get(route('rank.edit', ['id' => $user->rankings->first()->getKey()]))
            ->assertOk();
    });

    test('non-owner cannot view the edit page', function () {
        $ranking = Ranking::factory()->create();

        $this->actingAs(User::first())
            ->get(route('rank.edit', ['id' => $ranking->getKey()]))
            ->assertForbidden();
    });

    test('owner can update name and visibility', function () {
        $user = User::factory()->create();

        $ranking = Ranking::factory()
            ->has(Song::factory()->count(5))
            ->create([
                'user_id' => $user->getKey(),
                'is_public' => false,
            ]);

        Livewire::actingAs($user)
            ->test(EditRanking::class, ['id' => $ranking->getKey()])
            ->set('form.name', 'new name')
            ->set('form.is_public', true)
            ->call('update')
            ->assertHasNoErrors();

        $ranking->refresh();

        expect($ranking->name)->toBe('new name');
        expect($ranking->is_public)->toBeTrue();
    });

    test('non-owner cannot update name and visibility', function () {
        $user = User::factory()->create();

        $ranking = Ranking::factory()
            ->has(Song::factory()->count(5))
            ->create(['is_public' => false]);

        Livewire::actingAs($user)
            ->test(EditRanking::class, ['id' => $ranking->getKey()])
            ->assertForbidden();
    });

    test('owner can delete their ranking', function () {
        $user = User::factory()
            ->has(Ranking::factory())
            ->create();

        $ranking = $user->rankings->first();

        expect($ranking->deleted_at)->toBeNull();
        expect(Song::where('ranking_id', $ranking->getKey())->count())->toBe(10);

        Livewire::actingAs($user)
            ->test(ProfileRankingCard::class, ['ranking' => $ranking])
            ->call('destroy')
            ->assertDispatched('rankings-updated');

        $ranking->refresh();

        expect($ranking->deleted_at)->not->toBeNull();
        expect(Song::where('ranking_id', $ranking->getKey())->count())->toBe(0);
    });

    test('non-owner cannot delete another user\'s ranking', function () {
        $owner = User::factory()
            ->has(Ranking::factory())
            ->create();

        $nonOwner = User::factory()->create();
        $ranking = $owner->rankings->first();

        expect($ranking->deleted_at)->toBeNull();
        expect(Song::where('ranking_id', $ranking->getKey())->count())->toBe(10);

        Livewire::actingAs($nonOwner)
            ->test(ProfileRankingCard::class, ['ranking' => $ranking])
            ->call('destroy')
            ->assertForbidden();

        $ranking->refresh();

        expect($ranking->deleted_at)->toBeNull();
        expect(Song::where('ranking_id', $ranking->getKey())->count())->toBe(10);
    });

    test('non-owner can view a public completed ranking', function () {
        $owner = User::factory()->create();
        $visitor = User::factory()->create();

        $ranking = Ranking::factory()
            ->has(Song::factory()->count(5))
            ->create([
                'user_id' => $owner->getKey(),
                'is_public' => true,
                'is_ranked' => true,
            ]);

        $this->actingAs($visitor)
            ->get(route('ranking', ['id' => $ranking->getKey()]))
            ->assertOk();
    });

    test('non-owner cannot view a private ranking', function () {
        $owner = User::factory()->create();
        $visitor = User::factory()->create();

        $ranking = Ranking::factory()
            ->has(Song::factory()->count(5))
            ->create([
                'user_id' => $owner->getKey(),
                'is_public' => false,
                'is_ranked' => true,
            ]);

        $this->actingAs($visitor)
            ->get(route('ranking', ['id' => $ranking->getKey()]))
            ->assertStatus(404);
    });

    test('owner can view their own private ranking', function () {
        $owner = User::factory()->create();

        $ranking = Ranking::factory()
            ->has(Song::factory()->count(5))
            ->create([
                'user_id' => $owner->getKey(),
                'is_public' => false,
                'is_ranked' => true,
            ]);

        $this->actingAs($owner)
            ->get(route('ranking', ['id' => $ranking->getKey()]))
            ->assertOk();
    });
});

describe('Ranking Comments Display', function () {
    test('displays comments component when comments are enabled', function () {
        $user = User::factory()->create();

        $ranking = Ranking::factory()
            ->has(Song::factory()->count(5))
            ->create([
                'user_id' => $user->getKey(),
                'is_public' => true,
                'comments_enabled' => true,
                'is_ranked' => true,
            ]);

        $this->actingAs($user)
            ->get(route('ranking', ['id' => $ranking->getKey()]))
            ->assertOk()
            ->assertSeeLivewire('comments');
    });

    test('hides comments component when comments are disabled', function () {
        $user = User::factory()->create();

        $ranking = Ranking::factory()
            ->has(Song::factory()->count(5))
            ->create([
                'user_id' => $user->getKey(),
                'is_public' => true,
                'comments_enabled' => false,
                'is_ranked' => true,
            ]);

        $this->actingAs($user)
            ->get(route('ranking', ['id' => $ranking->getKey()]))
            ->assertOk()
            ->assertDontSeeLivewire('comments');
    });
});

describe('Ranking Algorithm', function () {
    test('ranks songs in the expected order based on user selections', function () {
        $user = User::factory()->create();

        $artist = Artist::factory()->create([
            'artist_name' => 'Test Artist',
            'is_podcast' => false,
        ]);

        $ranking = Ranking::create([
            'user_id' => $user->getKey(),
            'artist_id' => $artist->getKey(),
            'name' => 'Algorithm Test Ranking',
            'is_ranked' => false,
            'is_public' => true,
        ]);

        $expectedSongTitles = [
            1 => 'Should be number 1',
            2 => 'Should be number 2',
            3 => 'Should be number 3',
            4 => 'Should be number 4',
            5 => 'Should be number 5',
        ];

        foreach ($expectedSongTitles as $title) {
            Song::factory()->create([
                'ranking_id' => $ranking->getKey(),
                'artist_id' => $artist->getKey(),
                'title' => $title,
                'rank' => 0,
            ]);
        }

        $sortingState = RankingSortingState::create([
            'ranking_id' => $ranking->getKey(),
        ]);

        $component = Livewire::actingAs($user)
            ->test(SongRankProcess::class, [
                'ranking' => $ranking,
                'sortingState' => $sortingState,
            ]);

        simulateRankingComparisons($component, maxComparisons: 50);

        $ranking->refresh();
        $sortingState->refresh();

        expect($ranking->is_ranked)->toBeTrue();
        expect($ranking->completed_at)->not->toBeNull();
        expect($sortingState->sorting_state)->toBeNull();

        foreach ($ranking->songs()->get() as $song) {
            expect($song->rank)->toBeGreaterThan(0);
            expect($song->title)->toBe($expectedSongTitles[$song->rank]);
        }
    });

    test('completes ranking with minimum of 2 songs', function () {
        $user = User::factory()->create();

        $artist = Artist::factory()->create([
            'artist_name' => 'Test Artist',
            'is_podcast' => false,
        ]);

        $ranking = Ranking::create([
            'user_id' => $user->getKey(),
            'artist_id' => $artist->getKey(),
            'name' => 'Two Song Ranking',
            'is_ranked' => false,
            'is_public' => true,
        ]);

        $expectedSongTitles = [
            1 => 'Should be number 1',
            2 => 'Should be number 2',
        ];

        foreach ($expectedSongTitles as $title) {
            Song::factory()->create([
                'ranking_id' => $ranking->getKey(),
                'artist_id' => $artist->getKey(),
                'title' => $title,
                'rank' => 0,
            ]);
        }

        $sortingState = RankingSortingState::create([
            'ranking_id' => $ranking->getKey(),
            'sorting_state' => null,
            'aprox_comparisons' => 0,
            'completed_comparisons' => 0,
        ]);

        $component = Livewire::actingAs($user)
            ->test(SongRankProcess::class, [
                'ranking' => $ranking,
                'sortingState' => $sortingState,
            ]);

        simulateRankingComparisons($component, maxComparisons: 5);

        $ranking->refresh();

        expect($ranking->is_ranked)->toBeTrue();
        expect($ranking->songs()->count())->toBe(2);

        foreach ($ranking->songs()->get() as $song) {
            expect($song->title)->toBe($expectedSongTitles[$song->rank]);
        }
    });

    test('completes ranking with 10 songs', function () {
        $user = User::factory()->create();

        $artist = Artist::factory()->create([
            'artist_name' => 'Test Artist',
            'is_podcast' => false,
        ]);

        $ranking = Ranking::create([
            'user_id' => $user->getKey(),
            'artist_id' => $artist->getKey(),
            'name' => 'Ten Song Ranking',
            'is_ranked' => false,
            'is_public' => true,
        ]);

        $expectedSongTitles = collect(range(1, 10))
            ->mapWithKeys(fn ($i) => [$i => "Should be number {$i}"])
            ->toArray();

        foreach ($expectedSongTitles as $title) {
            Song::factory()->create([
                'ranking_id' => $ranking->getKey(),
                'artist_id' => $artist->getKey(),
                'title' => $title,
                'rank' => 0,
            ]);
        }

        $sortingState = RankingSortingState::create([
            'ranking_id' => $ranking->getKey(),
        ]);

        $component = Livewire::actingAs($user)
            ->test(SongRankProcess::class, [
                'ranking' => $ranking,
                'sortingState' => $sortingState,
            ]);

        simulateRankingComparisons($component, maxComparisons: 100);

        $ranking->refresh();

        expect($ranking->is_ranked)->toBeTrue();
        expect($ranking->songs()->count())->toBe(10);

        foreach ($ranking->songs()->get() as $song) {
            expect($song->title)->toBe($expectedSongTitles[$song->rank]);
        }
    });

    test('can resume ranking progress after interruption', function () {
        $user = User::factory()->create();

        $artist = Artist::factory()->create([
            'artist_name' => 'Test Artist',
            'is_podcast' => false,
        ]);

        $ranking = Ranking::create([
            'user_id' => $user->getKey(),
            'artist_id' => $artist->getKey(),
            'name' => 'Resumable Ranking',
            'is_ranked' => false,
            'is_public' => true,
        ]);

        $expectedSongTitles = [
            1 => 'Should be number 1',
            2 => 'Should be number 2',
            3 => 'Should be number 3',
            4 => 'Should be number 4',
            5 => 'Should be number 5',
        ];

        foreach ($expectedSongTitles as $title) {
            Song::factory()->create([
                'ranking_id' => $ranking->getKey(),
                'artist_id' => $artist->getKey(),
                'title' => $title,
                'rank' => 0,
            ]);
        }

        $sortingState = RankingSortingState::create([
            'ranking_id' => $ranking->getKey(),
            'sorting_state' => null,
            'aprox_comparisons' => 0,
            'completed_comparisons' => 0,
        ]);

        // First session - make 3 comparisons then "leave"
        $firstSession = Livewire::actingAs($user)
            ->test(SongRankProcess::class, [
                'ranking' => $ranking,
                'sortingState' => $sortingState,
            ]);

        simulateRankingComparisons($firstSession, maxComparisons: 3);

        $sortingState->refresh();

        expect($sortingState->completed_comparisons)->toBeGreaterThan(0);
        expect($ranking->fresh()->is_ranked)->toBeFalse();

        // Second session - resume and complete
        $secondSession = Livewire::actingAs($user)
            ->test(SongRankProcess::class, [
                'ranking' => $ranking->fresh(),
                'sortingState' => $sortingState->fresh(),
            ]);

        simulateRankingComparisons($secondSession, maxComparisons: 50);

        $ranking->refresh();

        expect($ranking->is_ranked)->toBeTrue();

        foreach ($ranking->songs()->get() as $song) {
            expect($song->title)->toBe($expectedSongTitles[$song->rank]);
        }
    });
});

/**
 * Simulate user selections during the ranking process.
 * Always picks the song with the lower number (higher rank).
 */
function simulateRankingComparisons(Testable $component, int $maxComparisons): void
{
    for ($i = 0; $i < $maxComparisons; $i++) {
        $leftSong = $component->get('currentSong1');
        $rightSong = $component->get('currentSong2');

        if (empty($leftSong['title']) || empty($rightSong['title'])) {
            break;
        }

        preg_match('/(\d+)/', $leftSong['title'], $leftMatches);
        preg_match('/(\d+)/', $rightSong['title'], $rightMatches);

        $leftSongRank = (int) $leftMatches[1];
        $rightSongRank = (int) $rightMatches[1];

        $winningSongId = $leftSongRank < $rightSongRank
            ? $leftSong['id']
            : $rightSong['id'];

        $component->call('chooseSong', $winningSongId);
    }
}