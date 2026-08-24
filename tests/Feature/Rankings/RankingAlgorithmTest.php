<?php

use App\Livewire\SongRank\SongRankProcess;
use App\Models\Artist;
use App\Models\RankingSortingState;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    Artist::factory()->create();
    User::factory()->createOne();
});

describe('ranking algorithm', function () {
    test('ranks songs in the expected order based on user selections', function () {
        $user = User::factory()->createOne();
        $expectedSongTitles = expectedSongTitles(5);
        $ranking = algorithmRanking($user, 'Algorithm Test Ranking', $expectedSongTitles);

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
        $user = User::factory()->createOne();
        $expectedSongTitles = expectedSongTitles(2);
        $ranking = algorithmRanking($user, 'Two Song Ranking', $expectedSongTitles);

        $sortingState = RankingSortingState::create([
            'ranking_id' => $ranking->getKey(),
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
        $user = User::factory()->createOne();
        $expectedSongTitles = expectedSongTitles(10);
        $ranking = algorithmRanking($user, 'Ten Song Ranking', $expectedSongTitles);

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
        $user = User::factory()->createOne();
        $expectedSongTitles = expectedSongTitles(5);
        $ranking = algorithmRanking($user, 'Resumable Ranking', $expectedSongTitles);

        $sortingState = RankingSortingState::create([
            'ranking_id' => $ranking->getKey(),
            'sorting_state' => null,
            'aprox_comparisons' => 0,
            'completed_comparisons' => 0,
        ]);

        $firstSession = Livewire::actingAs($user)
            ->test(SongRankProcess::class, [
                'ranking' => $ranking,
                'sortingState' => $sortingState,
            ]);

        simulateRankingComparisons($firstSession, maxComparisons: 3);

        $sortingState->refresh();

        expect($sortingState->completed_comparisons)->toBeGreaterThan(0);
        expect($ranking->fresh()->is_ranked)->toBeFalse();

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
});
