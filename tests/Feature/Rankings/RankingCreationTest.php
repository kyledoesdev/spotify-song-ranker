<?php

use App\Enums\RankingType;
use App\Livewire\SongRank\SongRankSetup;
use App\Models\Artist;
use App\Models\Playlist;
use App\Models\Ranking;
use App\Models\Show;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    Artist::factory()->create();
    User::factory()->create();
});

test('unauthenticated user cannot access ranking setup', function () {
    $this->get(route('dashboard'))->assertRedirect(route('welcome'));
});

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
        ->set('selectedTracks', collect([
            [
                'id' => 'ceilings-id',
                'name' => 'Ceilings',
                'cover' => 'https://api.dicebear.com/7.x/initials/svg?seed=ceilings',
                'uuid' => str()->uuid()->toString(),
            ],
            [
                'id' => 'sun-hands-id',
                'name' => 'Sun Hands',
                'cover' => 'https://api.dicebear.com/7.x/initials/svg?seed=sun_hands',
                'uuid' => str()->uuid()->toString(),
            ],
            [
                'id' => 'featherweight-id',
                'name' => 'Featherweight',
                'cover' => 'https://api.dicebear.com/7.x/initials/svg?seed=featherweight',
                'uuid' => str()->uuid()->toString(),
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
    expect(Ranking::where('name', 'Test Playlist List')->exists())->toBeFalse();

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
        ->set('selectedTracks', collect([
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
        ->set('form.name', 'Test Playlist List')
        ->set('form.is_public', true)
        ->call('beginRanking')
        ->assertHasNoErrors();

    $playlist = Playlist::where('playlist_id', 'test-playlist-id')->first();
    $ranking = Ranking::where('name', 'Test Playlist List')->first();

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

test('can create a ranking for a show', function () {
    expect(Show::where('show_id', 'test-show-id')->exists())->toBeFalse();
    expect(Ranking::where('name', 'Test Show List')->exists())->toBeFalse();

    Livewire::actingAs(User::first())
        ->test(SongRankSetup::class)
        ->set('selectedShow', [
            'id' => 'test-show-id',
            'name' => 'Test Show',
            'publisher' => 'Test Publisher',
            'publisher_image' => 'https://api.dicebear.com/7.x/initials/svg?seed=publisher',
            'description' => 'A test show',
            'cover' => 'show-cover',
            'episode_count' => 3,
            'data' => [],
        ])
        ->set('selectedTracks', collect([
            [
                'id' => 'episode-1-id',
                'name' => 'Episode 1',
                'cover' => 'https://api.dicebear.com/7.x/initials/svg?seed=ep1',
                'uuid' => str()->uuid()->toString(),
            ],
            [
                'id' => 'episode-2-id',
                'name' => 'Episode 2',
                'cover' => 'https://api.dicebear.com/7.x/initials/svg?seed=ep2',
                'uuid' => str()->uuid()->toString(),
            ],
            [
                'id' => 'episode-3-id',
                'name' => 'Episode 3',
                'cover' => 'https://api.dicebear.com/7.x/initials/svg?seed=ep3',
                'uuid' => str()->uuid()->toString(),
            ],
        ]))
        ->set('type', RankingType::SHOW)
        ->set('form.name', 'Test Show List')
        ->set('form.is_public', true)
        ->call('beginRanking')
        ->assertHasNoErrors();

    $show = Show::where('show_id', 'test-show-id')->first();
    $ranking = Ranking::where('name', 'Test Show List')->first();

    expect($show)->not->toBeNull();
    expect($show->name)->toBe('Test Show');
    expect($show->publisher)->toBe('Test Publisher');

    expect($ranking)->not->toBeNull();
    expect($ranking->songs()->count())->toBe(3);
});

test('cannot search for an artist with an empty search term', function () {
    Livewire::actingAs(User::first())
        ->test(SongRankSetup::class)
        ->set('searchTerm', '')
        ->call('searchArtist')
        ->assertSet('searchedArtists', null)
        ->assertSet('selectedTracks', null);
});

test('cannot search for a playlist with an invalid URL', function () {
    Livewire::actingAs(User::first())
        ->test(SongRankSetup::class)
        ->set('searchTerm', 'abcdxyz')
        ->call('searchPlaylist')
        ->assertSet('selectedPlaylist', [])
        ->assertSet('selectedTracks', null);
});

test('can search for a playlist with a valid URL', function () {
    Livewire::actingAs(User::first())
        ->test(SongRankSetup::class)
        ->set('type', RankingType::PLAYLIST)
        ->set('searchTerm', 'https://open.spotify.com/playlist/1l9ToABW4nh8EdGfq3Qvei')
        ->call('searchPlaylist')
        ->assertSet('type', RankingType::PLAYLIST);
});

test('cannot change ranking type after selecting an artist', function () {
    Livewire::actingAs(User::first())
        ->test(SongRankSetup::class)
        ->set('selectedArtist', [
            'id' => 'test-artist-id',
            'name' => 'Local Natives',
            'cover' => 'https://api.dicebear.com/7.x/initials/svg?seed=testing',
        ])
        ->set('selectedTracks', collect([
            ['id' => 'track-1', 'name' => 'Track 1', 'cover' => 'cover', 'uuid' => str()->uuid()->toString()],
            ['id' => 'track-2', 'name' => 'Track 2', 'cover' => 'cover', 'uuid' => str()->uuid()->toString()],
        ]))
        ->set('type', RankingType::PLAYLIST->value)
        ->assertSet('type', RankingType::ARTIST)
        ->assertNotSet('selectedArtist', null);
});

test('resets search results when changing type during artist search before selection', function () {
    Livewire::actingAs(User::first())
        ->test(SongRankSetup::class)
        ->set('searchedArtists', collect([
            ['id' => 'artist-1', 'name' => 'Artist 1', 'cover' => 'cover'],
        ]))
        ->set('type', RankingType::PLAYLIST->value)
        ->assertSet('type', RankingType::PLAYLIST)
        ->assertSet('searchedArtists', null);
});
