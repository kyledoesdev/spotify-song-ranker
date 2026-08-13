<?php

use App\Filament\Resources\Rankings\Pages\ViewRanking;
use App\Models\Ranking;
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
        $ranking = Ranking::factory()->playlist()->createOne();

        viewRanking($ranking)
            ->assertSee('Playlist Details')
            ->assertDontSee('Artist Details')
            ->assertDontSee('Show Details');
    });

    test('only shows show details for a show ranking', function () {
        $ranking = Ranking::factory()->show()->createOne();

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
