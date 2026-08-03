<?php

use App\Actions\CompleteSongRankProcess;
use App\Livewire\Explorer;
use App\Models\Artist;
use App\Models\Playlist;
use App\Models\Ranking;
use App\Models\RankingSortingState;
use App\Models\Show;
use Livewire\Livewire;

use function Pest\Laravel\get;

describe('ranking visibility', function () {
    test('public completed rankings are visible', function () {
        $ranking = publicCompletedRanking();

        get(route('explore'))
            ->assertOk()
            ->assertSee($ranking->name);
    });

    test('public uncompleted rankings are hidden', function () {
        $ranking = Ranking::factory()->create([
            'is_public' => true,
            'is_ranked' => false,
            'completed_at' => null,
        ]);

        get(route('explore'))
            ->assertOk()
            ->assertDontSee($ranking->name);
    });

    test('private completed rankings are hidden', function () {
        $ranking = Ranking::factory()->create([
            'is_public' => false,
            'is_ranked' => true,
            'completed_at' => now(),
        ]);

        get(route('explore'))
            ->assertOk()
            ->assertDontSee($ranking->name);
    });

    test('private uncompleted rankings are hidden', function () {
        $ranking = Ranking::factory()->create([
            'is_public' => false,
            'is_ranked' => false,
            'completed_at' => null,
        ]);

        get(route('explore'))
            ->assertOk()
            ->assertDontSee($ranking->name);
    });
});

describe('explorable rankings counter', function () {
    test('shows a live count of explorable rankings', function () {
        publicCompletedRanking();
        publicCompletedRanking();

        Ranking::factory()->create([
            'is_public' => false,
            'is_ranked' => true,
            'completed_at' => now(),
        ]);

        Ranking::factory()->create([
            'is_public' => true,
            'is_ranked' => false,
            'completed_at' => null,
        ]);

        get(route('explore'))
            ->assertOk()
            ->assertSee('2 completed rankings to explore');
    });

    test('completing a ranking busts the counter cache', function () {
        cache()->put('explore:total-rankings', 99);

        $ranking = Ranking::factory()->create([
            'is_public' => true,
            'is_ranked' => false,
            'completed_at' => null,
        ]);

        RankingSortingState::create(['ranking_id' => $ranking->getKey()]);

        (new CompleteSongRankProcess)->handle($ranking, [
            'finalSongIds' => $ranking->songs()->pluck('id')->all(),
        ]);

        expect(cache()->has('explore:total-rankings'))->toBeFalse();
    });
});

describe('searching', function () {
    test('can search rankings by artist name', function () {
        $match = Artist::factory()->create(['artist_name' => 'Matching Artist']);
        $other = Artist::factory()->create(['artist_name' => 'Other Artist']);

        $matchingRanking = publicCompletedRanking([
            'artist_id' => $match->getKey(),
            'name' => 'Matching Ranking',
        ]);

        $otherRanking = publicCompletedRanking([
            'artist_id' => $other->getKey(),
            'name' => 'Other Ranking',
        ]);

        Livewire::test(Explorer::class)
            ->set('search', 'Matching Artist')
            ->call('performSearch')
            ->assertSee($matchingRanking->name)
            ->assertDontSee($otherRanking->name);
    });

    test('can search rankings by playlist name', function () {
        $playlist = Playlist::factory()->create(['name' => 'Chill Vibes Playlist']);

        $matchingRanking = publicCompletedRanking([
            'artist_id' => null,
            'playlist_id' => $playlist->getKey(),
            'name' => 'Matching Playlist Ranking',
        ]);

        $otherRanking = publicCompletedRanking(['name' => 'Other Ranking']);

        Livewire::test(Explorer::class)
            ->set('search', 'Chill Vibes')
            ->call('performSearch')
            ->assertSee($matchingRanking->name)
            ->assertDontSee($otherRanking->name);
    });

    test('can search rankings by show name', function () {
        $show = Show::create([
            'show_id' => str()->random(16),
            'publisher' => 'Podcast Publisher',
            'name' => 'True Crime Weekly',
            'description' => 'A weekly true crime show.',
            'cover' => 'https://example.com/cover.jpg',
            'episode_count' => 100,
        ]);

        $matchingRanking = publicCompletedRanking([
            'artist_id' => null,
            'show_id' => $show->getKey(),
            'name' => 'Matching Show Ranking',
        ]);

        $otherRanking = publicCompletedRanking(['name' => 'Other Ranking']);

        Livewire::test(Explorer::class)
            ->set('search', 'True Crime Weekly')
            ->call('performSearch')
            ->assertSee($matchingRanking->name)
            ->assertDontSee($otherRanking->name);
    });
});
