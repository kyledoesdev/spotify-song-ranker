<?php

use App\Filament\Resources\Artists\Pages\ViewArtist;
use App\Filament\Resources\Playlists\Pages\ViewPlaylist;
use App\Filament\Resources\Shows\Pages\ViewShow;
use App\Models\Artist;
use App\Models\Playlist;
use App\Models\Show;
use Livewire\Livewire;

describe('spotify links', function () {
    test('links an artist back to spotify', function () {
        $artist = Artist::factory()->createOne();

        Livewire::actingAs(kyle())
            ->test(ViewArtist::class, ['record' => $artist->getKey()])
            ->assertOk()
            ->assertSee($artist->spotifyUrl());
    });

    test('links a show back to spotify', function () {
        $show = Show::factory()->createOne();

        Livewire::actingAs(kyle())
            ->test(ViewShow::class, ['record' => $show->getKey()])
            ->assertOk()
            ->assertSee($show->spotifyUrl());
    });

    test('links a playlist back to spotify', function () {
        $playlist = Playlist::factory()->createOne();

        Livewire::actingAs(kyle())
            ->test(ViewPlaylist::class, ['record' => $playlist->getKey()])
            ->assertOk()
            ->assertSee($playlist->spotifyUrl());
    });
});
