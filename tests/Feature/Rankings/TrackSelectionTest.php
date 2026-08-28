<?php

use App\Livewire\SongRank\Setup\ArtistSetup;
use App\Models\Ranking;
use App\Models\User;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;

describe('removed tracks bank', function () {
    test('restoring a removed track puts it back in the ranking', function () {
        $component = setupWithAlbums();
        $uuid = $component->get('selectedTracks')->first()['uuid'];

        $component
            ->call('removeTrack', $uuid)
            ->call('restoreTrack', $uuid);

        expect($component->get('removedTrackUuids'))->toBeEmpty();
    });

    test('restoring a track dispatches a tracks-batch-restored event', function () {
        $component = setupWithAlbums();
        $uuid = $component->get('selectedTracks')->first()['uuid'];

        $component
            ->call('removeTrack', $uuid)
            ->call('restoreTrack', $uuid)
            ->assertDispatched('tracks-batch-restored');
    });

    test('removedTracks returns only the tracks that have been removed', function () {
        $component = setupWithAlbums();
        $tracks = $component->get('selectedTracks');

        $component->call('removeTrack', $tracks[0]['uuid']);

        $component->call('removedTracks')->assertReturned(function ($removed) use ($tracks) {
            return count($removed) === 1 && $removed[0]['uuid'] === $tracks[0]['uuid'];
        });
    });

    test('removedTracks is empty when nothing has been removed', function () {
        $component = setupWithAlbums();

        $component->call('removedTracks')->assertReturned(function ($removed) {
            return count($removed) === 0;
        });
    });

    test('restoring one track does not affect other removed tracks', function () {
        $component = setupWithAlbums();
        $tracks = $component->get('selectedTracks');

        $component
            ->call('removeTrack', $tracks[0]['uuid'])
            ->call('removeTrack', $tracks[1]['uuid'])
            ->call('restoreTrack', $tracks[0]['uuid']);

        expect($component->get('removedTrackUuids'))->toHaveCount(1)
            ->toContain($tracks[1]['uuid']);
    });

    test('reset clears all removed tracks', function () {
        $component = setupWithAlbums();
        $uuid = $component->get('selectedTracks')->first()['uuid'];

        $component
            ->call('removeTrack', $uuid)
            ->call('resetSetup');

        expect($component->get('removedTrackUuids'))->toBeEmpty();
    });
});

describe('album filtering', function () {
    test('albums method returns grouped album metadata', function () {
        $component = setupWithAlbums();

        $component->call('albums')->assertReturned(function ($albums) {
            return count($albums) === 2;
        });
    });

    test('toggling an album off removes all its tracks', function () {
        $component = setupWithAlbums();
        $albumACount = $component->get('selectedTracks')->where('album_id', 'album-a-id')->count();

        $component->call('toggleAlbum', 'album-a-id');

        expect($component->get('removedTrackUuids'))->toHaveCount($albumACount);
    });

    test('toggling an album off dispatches tracks-batch-removed', function () {
        $component = setupWithAlbums();

        $component
            ->call('toggleAlbum', 'album-a-id')
            ->assertDispatched('tracks-batch-removed');
    });

    test('toggling a fully-removed album back on restores its tracks', function () {
        $component = setupWithAlbums();

        $component
            ->call('toggleAlbum', 'album-a-id')
            ->call('toggleAlbum', 'album-a-id');

        expect($component->get('removedTrackUuids'))->toBeEmpty();
    });

    test('toggling a fully-removed album back on dispatches tracks-batch-restored', function () {
        $component = setupWithAlbums();

        $component
            ->call('toggleAlbum', 'album-a-id')
            ->call('toggleAlbum', 'album-a-id')
            ->assertDispatched('tracks-batch-restored');
    });

    test('toggling a partially-removed album removes its remaining tracks', function () {
        $component = setupWithAlbums();
        $albumATracks = $component->get('selectedTracks')->where('album_id', 'album-a-id');
        $firstUuid = $albumATracks->first()['uuid'];

        $component
            ->call('removeTrack', $firstUuid)
            ->call('toggleAlbum', 'album-a-id');

        $albumAUuids = $albumATracks->pluck('uuid')->toArray();
        $removed = $component->get('removedTrackUuids');

        foreach ($albumAUuids as $uuid) {
            expect($removed)->toContain($uuid);
        }
    });

    test('restoring a track after album toggle works correctly', function () {
        $component = setupWithAlbums();
        $albumATracks = $component->get('selectedTracks')->where('album_id', 'album-a-id');
        $firstUuid = $albumATracks->first()['uuid'];

        $component
            ->call('toggleAlbum', 'album-a-id')
            ->call('restoreTrack', $firstUuid);

        expect($component->get('removedTrackUuids'))->toHaveCount(1)
            ->not->toContain($firstUuid);
    });

    test('album selection state reflects individual track removals', function () {
        $component = setupWithAlbums();
        $albumATracks = $component->get('selectedTracks')->where('album_id', 'album-a-id');

        foreach ($albumATracks as $track) {
            $component->call('removeTrack', $track['uuid']);
        }

        $component->call('albums')->assertReturned(function ($albums) {
            $albumA = collect($albums)->firstWhere('id', 'album-a-id');

            return $albumA['none_selected'] === true && $albumA['selected_count'] === 0;
        });
    });

    test('beginning a ranking after album filtering only includes selected tracks', function () {
        $component = setupWithAlbums();

        $component
            ->call('toggleAlbum', 'album-a-id')
            ->call('beginRanking')
            ->assertHasNoErrors();

        $ranking = Ranking::where('name', 'Test Album List')->firstOrFail();

        expect($ranking->songs()->count())->toBe(2);
        expect($ranking->songs()->pluck('title')->sort()->values()->all())
            ->toBe(['Song B1', 'Song B2']);
    });
});

// -- Helpers --

function setupWithAlbums(): Testable
{
    return Livewire::actingAs(User::factory()->createOne())
        ->test(ArtistSetup::class)
        ->set('selectedArtist', [
            'id' => 'test-artist-id',
            'name' => 'Test Artist',
            'cover' => 'https://example.test/artist.png',
        ])
        ->set('selectedTracks', collect([
            albumTrack('song-a1-id', 'Song A1', 'album-a-id', 'Album A', 'album'),
            albumTrack('song-a2-id', 'Song A2 (Remix)', 'album-a-id', 'Album A', 'album'),
            albumTrack('song-b1-id', 'Song B1', 'album-b-id', 'Album B', 'single'),
            albumTrack('song-b2-id', 'Song B2', 'album-b-id', 'Album B', 'single'),
        ]))
        ->set('form.name', 'Test Album List')
        ->set('form.is_public', true);
}

function albumTrack(string $id, string $name, string $albumId, string $albumName, string $albumType): array
{
    return [
        'id' => $id,
        'name' => $name,
        'uuid' => str()->uuid()->toString(),
        'cover' => "https://example.test/{$albumId}.png",
        'album_name' => $albumName,
        'album_id' => $albumId,
        'album_type' => $albumType,
        'featured_artist' => false,
    ];
}
