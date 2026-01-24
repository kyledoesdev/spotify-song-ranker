<?php

namespace App\Livewire\SongRank\ProgressTabs;

use App\Models\Ranking;
use App\Models\RankingSortingState;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class CurrentOrder extends Component
{
    public Ranking $ranking;

    public Collection $songs;

    public RankingSortingState $sortingState;

    public bool $showAdvancedStats = false;

    public function getData(): array
    {
        $state = $this->sortingState->fresh()->sorting_state;
        $currentMerge = $state['current_merge'] ?? null;
        $completedSegments = $state['completed_segments'] ?? [];
        $songsById = $this->songs->keyBy('id');

        $largestSegment = collect($completedSegments)
            ->sortByDesc(fn ($ids) => count($ids))
            ->first();

        $duel = null;
        if ($currentMerge && !empty($currentMerge['left_ids']) && !empty($currentMerge['right_ids'])) {
            $duel = [
                'left' => $songsById->get($currentMerge['left_ids'][0]),
                'right' => $songsById->get($currentMerge['right_ids'][0]),
            ];
        }

        return [
            'duel' => $duel,
            'current_merge' => $currentMerge ? $this->buildCurrentMerge($currentMerge, $songsById) : null,
            'best_order' => $largestSegment ? $this->buildBestOrder($largestSegment, $songsById) : null,
        ];
    }

    protected function buildCurrentMerge(array $merge, Collection $songsById): array
    {
        return [
            'ranked_songs' => collect($merge['result_ids'] ?? [])
                ->map(fn ($songId) => $songsById->get($songId))
                ->filter()
                ->values(),
            'left_songs' => collect($merge['left_ids'] ?? [])
                ->map(fn ($songId) => $songsById->get($songId))
                ->filter()
                ->values(),
            'right_songs' => collect($merge['right_ids'] ?? [])
                ->map(fn ($songId) => $songsById->get($songId))
                ->filter()
                ->values(),
            'left_count' => count($merge['left_ids'] ?? []),
            'right_count' => count($merge['right_ids'] ?? []),
        ];
    }

    protected function buildBestOrder(array $songIds, Collection $songsById): array
    {
        return [
            'track_count' => count($songIds),
            'songs' => collect($songIds)
                ->map(fn ($songId) => $songsById->get($songId))
                ->filter()
                ->values(),
        ];
    }

    public function render()
    {
        return view('livewire.song-rank.progress-tabs.current-order', $this->getData());
    }
}