<?php

use App\Filament\Resources\Playlists\Pages\ViewPlaylist;
use App\Filament\Resources\Playlists\RelationManagers\RankingsRelationManager;
use App\Models\Playlist;
use Livewire\Livewire;

describe('playlist rankings', function () {
    test('lists the rankings built from the playlist', function () {
        $playlist = Playlist::factory()->createOne();

        $ranking = publicCompletedRanking([
            'artist_id' => null,
            'playlist_id' => $playlist->getKey(),
        ]);

        $otherRanking = publicCompletedRanking();

        Livewire::actingAs(kyle())
            ->test(RankingsRelationManager::class, [
                'ownerRecord' => $playlist,
                'pageClass' => ViewPlaylist::class,
            ])
            ->assertOk()
            ->assertCanSeeTableRecords([$ranking])
            ->assertCanNotSeeTableRecords([$otherRanking]);
    });
});
