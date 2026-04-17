<?php

use App\Livewire\SongRank\SongRankProcess;
use App\Models\Artist;
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
