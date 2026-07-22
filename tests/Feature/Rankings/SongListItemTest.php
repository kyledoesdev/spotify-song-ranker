<?php

use App\Enums\RankingType;
use App\Livewire\SongRank\SongListItem;
use App\Models\User;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;

describe('the artist credit line', function () {
    test('shows the primary artist on featured tracks in artist rankings', function () {
        songListItem(RankingType::ARTIST, [
            'featured_artist' => true,
            'primary_artist' => ['id' => 'rav-id', 'name' => 'Rav'],
        ])
            ->call('primaryArtistName')
            ->assertReturned('Rav')
            ->assertSee('Rav');
    });

    test('falls back to the artist a playlist track arrived with', function () {
        songListItem(RankingType::PLAYLIST, [
            'artist_id' => 'local-natives-id',
            'artist_name' => 'Local Natives',
        ])
            ->call('primaryArtistName')
            ->assertReturned('Local Natives')
            ->assertSee('Local Natives');
    });

    test('stays empty for owned artist tracks, whose artist is implied', function () {
        songListItem(RankingType::ARTIST)
            ->call('primaryArtistName')
            ->assertReturned(null);
    });
});

// -- Helpers --

function songListItem(RankingType $type, array $extra = []): Testable
{
    return Livewire::actingAs(User::factory()->createOne())
        ->test(SongListItem::class, [
            'type' => $type,
            'song' => array_merge([
                'id' => 'track-id',
                'name' => 'Track Name',
                'uuid' => str()->uuid()->toString(),
                'cover' => 'https://example.test/cover.png',
            ], $extra),
        ]);
}
