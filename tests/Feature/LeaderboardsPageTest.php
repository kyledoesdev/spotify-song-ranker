<?php

use App\Livewire\Leaderboards;
use App\Models\Artist;
use App\Models\Playlist;
use App\Models\Ranking;
use App\Models\Show;
use App\Models\Song;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

describe('page access', function () {
    test('guests can view the leaderboards page', function () {
        get(route('leaderboards'))
            ->assertOk()
            ->assertSee('Top Ranked Artists')
            ->assertSee('Top Creators')
            ->assertSee('Rankings with Most Songs');
    });

    test('authenticated users can view the leaderboards page', function () {
        actingAs(User::factory()->createOne())
            ->get(route('leaderboards'))
            ->assertOk()
            ->assertSee('Top Ranked Artists')
            ->assertSee('Top Creators')
            ->assertSee('Rankings with Most Songs');
    });

    test('leaderboard entries are visible on the page', function () {
        $artist = Artist::factory()->create(['artist_name' => 'Page Artist']);
        $user = User::factory()->createOne(['name' => 'Page Creator']);

        publicCompletedRanking([
            'artist_id' => $artist->getKey(),
            'user_id' => $user->getKey(),
            'name' => 'Page Ranking',
        ]);

        get(route('leaderboards'))
            ->assertOk()
            ->assertSee('Page Artist')
            ->assertSee('Page Creator')
            ->assertSee('Page Ranking');
    });

    test('guests see the leaderboards link in the navigation', function () {
        get(route('explore'))
            ->assertOk()
            ->assertSee('Leaderboards');
    });
});

describe('top ranked artists', function () {
    test('artists are ordered by public completed ranking count', function () {
        $first = Artist::factory()->create(['artist_name' => 'First Artist']);
        $second = Artist::factory()->create(['artist_name' => 'Second Artist']);

        publicCompletedRanking(['artist_id' => $first->getKey()]);
        publicCompletedRanking(['artist_id' => $first->getKey()]);
        publicCompletedRanking(['artist_id' => $second->getKey()]);

        Livewire::test(Leaderboards::class)
            ->assertViewHas('topArtists', fn ($entries) => $entries->pluck('name')->all() === ['First Artist', 'Second Artist']
                && $entries->first()['count'] == 2);
    });

    test('artists with only private or uncompleted rankings are not on the leaderboard', function () {
        $private = Artist::factory()->create(['artist_name' => 'Private Artist']);
        $unfinished = Artist::factory()->create(['artist_name' => 'Unfinished Artist']);

        Ranking::factory()->create([
            'artist_id' => $private->getKey(),
            'is_public' => false,
            'is_ranked' => true,
            'completed_at' => now(),
        ]);

        Ranking::factory()->create([
            'artist_id' => $unfinished->getKey(),
            'is_public' => true,
            'is_ranked' => false,
            'completed_at' => null,
        ]);

        Livewire::test(Leaderboards::class)
            ->assertViewHas('topArtists', fn ($entries) => $entries->isEmpty());
    });
});

describe('top creators', function () {
    test('creators are ordered by public completed ranking count and private-only creators are excluded', function () {
        $prolific = User::factory()->createOne(['name' => 'Prolific Creator']);
        $casual = User::factory()->createOne(['name' => 'Casual Creator']);
        $private = User::factory()->createOne(['name' => 'Private Creator']);

        publicCompletedRanking(['user_id' => $prolific->getKey()]);
        publicCompletedRanking(['user_id' => $prolific->getKey()]);
        publicCompletedRanking(['user_id' => $casual->getKey()]);

        Ranking::factory()->create([
            'user_id' => $private->getKey(),
            'is_public' => false,
            'is_ranked' => true,
            'completed_at' => now(),
        ]);

        Livewire::test(Leaderboards::class)
            ->assertViewHas('topCreators', fn ($entries) => $entries->pluck('name')->all() === ['Prolific Creator', 'Casual Creator']);
    });

    test('sections are capped at ten entries with ties broken by name', function () {
        foreach (range('a', 'k') as $letter) {
            $user = User::factory()->createOne(['name' => "creator-{$letter}"]);

            publicCompletedRanking(['user_id' => $user->getKey()]);
        }

        Livewire::test(Leaderboards::class)
            ->assertViewHas('topCreators', fn ($entries) => $entries->count() === 10
                && $entries->first()['name'] === 'creator-a'
                && ! $entries->contains('name', 'creator-k'));
    });
});

describe('rankings with most songs', function () {
    test('rankings are ordered by song count and credit their creator', function () {
        $creator = User::factory()->createOne(['name' => 'List Maker']);

        $bigger = publicCompletedRanking([
            'user_id' => $creator->getKey(),
            'name' => 'Bigger Ranking',
        ]);

        publicCompletedRanking([
            'artist_id' => null,
            'playlist_id' => Playlist::factory(),
            'name' => 'Smaller Ranking',
        ]);

        foreach (range(11, 15) as $rank) {
            Song::factory()->create([
                'ranking_id' => $bigger->getKey(),
                'rank' => $rank,
            ]);
        }

        Livewire::test(Leaderboards::class)
            ->assertViewHas('biggestRankings', fn ($entries) => $entries->pluck('name')->all() === ['Bigger Ranking', 'Smaller Ranking']
                && $entries->first()['count'] == 15
                && $entries->first()['subtitle'] === 'List Maker'
                && filled($entries->last()['image']));
    });

    test('show rankings are included', function () {
        $show = Show::create([
            'show_id' => str()->random(16),
            'publisher' => 'Podcast Publisher',
            'name' => 'A Podcast Show',
            'description' => 'A show about things.',
            'cover' => 'https://example.com/cover.jpg',
            'episode_count' => 100,
        ]);

        publicCompletedRanking([
            'artist_id' => null,
            'show_id' => $show->getKey(),
            'name' => 'Show Ranking',
        ]);

        publicCompletedRanking(['name' => 'Music Ranking']);

        Livewire::test(Leaderboards::class)
            ->assertViewHas('biggestRankings', fn ($entries) => $entries->pluck('name')->all() === ['Music Ranking', 'Show Ranking']);
    });

    test('rankings with more than 500 songs are excluded', function () {
        $artist = Artist::factory()->create();

        $within = publicCompletedRanking(['name' => 'Within Limit Ranking']);
        $beyond = publicCompletedRanking(['name' => 'Beyond Limit Ranking']);

        Song::factory()->count(490)->create([
            'ranking_id' => $within->getKey(),
            'artist_id' => $artist->getKey(),
        ]);

        Song::factory()->count(491)->create([
            'ranking_id' => $beyond->getKey(),
            'artist_id' => $artist->getKey(),
        ]);

        Livewire::test(Leaderboards::class)
            ->assertViewHas('biggestRankings', fn ($entries) => $entries->pluck('name')->all() === ['Within Limit Ranking']
                && $entries->first()['count'] == 500);
    });
});
