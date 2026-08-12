<?php

use App\Filament\Resources\Rankings\Pages\ViewRanking;
use App\Models\Playlist;
use App\Models\Ranking;
use App\Models\Show;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;

describe('source sections', function () {
    test('only shows artist details for an artist ranking', function () {
        $ranking = Ranking::factory()->createOne();

        viewRanking($ranking)
            ->assertSee('Artist Details')
            ->assertDontSee('Playlist Details')
            ->assertDontSee('Show Details');
    });

    test('only shows playlist details for a playlist ranking', function () {
        $ranking = Ranking::factory()->createOne([
            'artist_id' => null,
            'playlist_id' => Playlist::factory()->createOne()->getKey(),
        ]);

        viewRanking($ranking)
            ->assertSee('Playlist Details')
            ->assertDontSee('Artist Details')
            ->assertDontSee('Show Details');
    });

    test('only shows show details for a show ranking', function () {
        $show = Show::create([
            'show_id' => str()->random(16),
            'publisher' => 'Podcast Publisher',
            'name' => 'True Crime Weekly',
            'description' => 'A weekly true crime show.',
            'cover' => 'https://example.com/cover.jpg',
            'episode_count' => 100,
        ]);

        $ranking = Ranking::factory()->createOne([
            'artist_id' => null,
            'show_id' => $show->getKey(),
        ]);

        viewRanking($ranking)
            ->assertSee('Show Details')
            ->assertDontSee('Artist Details')
            ->assertDontSee('Playlist Details');
    });
});

function viewRanking(Ranking $ranking): Testable
{
    return Livewire::actingAs(kyle())
        ->test(ViewRanking::class, ['record' => $ranking->getKey()])
        ->assertOk();
}
