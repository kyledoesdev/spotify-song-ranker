<?php

namespace App\Livewire;

use App\Models\Ranking;
use App\Models\RankingSortingState;
use App\Models\Song;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class SongRankProcess extends Component
{
    public Ranking $ranking;
    public RankingSortingState $sortingState;
    
    public $currentSong1;
    public $currentSong2;
    public bool $showEmbeds = true;
    public bool $isComplete = false;
    public bool $isProcessing = false;
    public $progressPercentage = 0;
    
    protected $listeners = ['toggleEmbeds'];

    public function mount()
    {
        if ($this->ranking->is_ranked) {
            $this->isComplete = true;
            return;
        }

        $this->initializeSorting();
    }

    public function initializeSorting()
    {
        if (!$this->sortingState->sorting_state) {
            $this->startNewSorting();
        }
        
        $this->updateProgress();
        $this->continueSort();
    }

    protected function startNewSorting()
    {
        // Get just the IDs in random order
        $songIds = $this->ranking->songs->shuffle()->pluck('id')->toArray();
        
        // Initialize with just IDs - no full song data
        $state = [
            'stack' => [
                [
                    'type' => 'sort',
                    'ids' => $songIds,  // Just IDs, not full objects
                    'start' => 0,
                    'end' => count($songIds) - 1,
                    'depth' => 0
                ]
            ],
            'current_merge' => null,
            'completed_segments' => [] // Will store arrays of IDs only
        ];
        
        $this->sortingState->update([
            'sorting_state' => $state,
            'aprox_comparisons' => $this->calculateTotalComparisons(count($songIds)),
            'completed_comparisons' => 0
        ]);
    }

    protected function getSongData($songId)
    {
        // Just fetch the song data when needed - no caching
        $song = Song::find($songId);
        return [
            'id' => $song->id,
            'title' => $song->title,
            'cover' => $song->cover,
            'spotify_song_id' => $song->spotify_song_id
        ];
    }

    protected function continueSort()
    {
        $this->sortingState->refresh();
        $state = $this->sortingState->sorting_state;
        
        if ($state['current_merge']) {
            $this->processMerge();
            return;
        }
        
        while (!empty($state['stack'])) {
            $task = array_pop($state['stack']);
            
            if ($task['type'] === 'sort') {
                $this->handleSortTask($task, $state);
            } elseif ($task['type'] === 'merge') {
                $state['current_merge'] = $task;
                $this->sortingState->update(['sorting_state' => $state]);
                $this->processMerge();
                return;
            }
        }
        
        if (empty($state['stack']) && !$state['current_merge']) {
            $finalKey = $this->findCompletedSegment($state, 0, count($this->ranking->songs) - 1);
            if ($finalKey !== null && isset($state['completed_segments'][$finalKey])) {
                $this->completeSorting($state['completed_segments'][$finalKey]);
            }
        }
    }

    protected function handleSortTask($task, &$state)
    {
        $start = $task['start'];
        $end = $task['end'];
        $ids = $task['ids'];
        
        if ($start >= $end) {
            $key = $start . '-' . $end;
            $state['completed_segments'][$key] = array_slice($ids, $start, $end - $start + 1);
            $this->sortingState->update(['sorting_state' => $state]);
            return;
        }
        
        $mid = intval(($start + $end) / 2);
        
        array_push($state['stack'], [
            'type' => 'merge',
            'ids' => $ids,
            'start' => $start,
            'mid' => $mid,
            'end' => $end,
            'left_key' => $start . '-' . $mid,
            'right_key' => ($mid + 1) . '-' . $end,
            'depth' => $task['depth'] + 1
        ]);
        
        array_push($state['stack'], [
            'type' => 'sort',
            'ids' => $ids,
            'start' => $mid + 1,
            'end' => $end,
            'depth' => $task['depth'] + 1
        ]);
        
        array_push($state['stack'], [
            'type' => 'sort',
            'ids' => $ids,
            'start' => $start,
            'end' => $mid,
            'depth' => $task['depth'] + 1
        ]);
        
        $this->sortingState->update(['sorting_state' => $state]);
    }

    protected function processMerge()
    {
        $this->sortingState->refresh();
        $state = $this->sortingState->sorting_state;
        $merge = $state['current_merge'];
        
        if (!$merge) {
            $this->continueSort();
            return;
        }
        
        if (!isset($merge['left_ids']) || !isset($merge['right_ids'])) {
            $leftKey = $merge['left_key'];
            $rightKey = $merge['right_key'];
            
            $left = $state['completed_segments'][$leftKey] ?? [];
            $right = $state['completed_segments'][$rightKey] ?? [];
            
            $merge['left_ids'] = array_values($left);
            $merge['right_ids'] = array_values($right);
            $merge['result_ids'] = [];
            
            $state['current_merge'] = $merge;
            $this->sortingState->update(['sorting_state' => $state]);
            $this->sortingState->refresh();
            $state = $this->sortingState->sorting_state;
            $merge = $state['current_merge'];
        }
        
        if (!empty($merge['left_ids']) && !empty($merge['right_ids'])) {
            // Fetch both songs in a single query
            $songIds = [$merge['left_ids'][0], $merge['right_ids'][0]];
            $songs = Song::whereIn('id', $songIds)
                ->get()
                ->keyBy('id');
            
            $this->currentSong1 = [
                'id' => $songs[$merge['left_ids'][0]]->id,
                'title' => $songs[$merge['left_ids'][0]]->title,
                'cover' => $songs[$merge['left_ids'][0]]->cover,
                'spotify_song_id' => $songs[$merge['left_ids'][0]]->spotify_song_id
            ];
            
            $this->currentSong2 = [
                'id' => $songs[$merge['right_ids'][0]]->id,
                'title' => $songs[$merge['right_ids'][0]]->title,
                'cover' => $songs[$merge['right_ids'][0]]->cover,
                'spotify_song_id' => $songs[$merge['right_ids'][0]]->spotify_song_id
            ];
        } else {
            $result = array_merge($merge['result_ids'], $merge['left_ids'], $merge['right_ids']);
            $key = $merge['start'] . '-' . $merge['end'];
            $state['completed_segments'][$key] = $result;
            $state['current_merge'] = null;
            
            $this->sortingState->update(['sorting_state' => $state]);
            $this->continueSort();
        }
    }

    public function chooseSong($songId)
    {
        if ($this->isProcessing) {
            return;
        }

        $this->isProcessing = true;

        try {
            $this->sortingState->refresh();
            $state = $this->sortingState->sorting_state;
            $merge = $state['current_merge'];
            
            if (!$merge) {
                $this->isProcessing = false;
                return;
            }
            
            $songId = (string) $songId;
            $song1Id = (string) $this->currentSong1['id'];
            $song2Id = (string) $this->currentSong2['id'];

            if ($songId !== $song1Id && $songId !== $song2Id) {
                $this->isProcessing = false;
                return;
            }

            // Work with IDs only
            if ($songId === $song1Id) {
                $merge['result_ids'][] = array_shift($merge['left_ids']);
            } else {
                $merge['result_ids'][] = array_shift($merge['right_ids']);
            }
            
            $state['current_merge'] = $merge;
            
            DB::transaction(function () use ($state) {
                $this->sortingState->increment('completed_comparisons');
                $this->sortingState->update(['sorting_state' => $state]);
            });
            
            $this->updateProgress();
            $this->processMerge();

        } catch (\Exception $e) {
            \Log::error('Error in chooseSong', ['error' => $e->getMessage()]);
        } finally {
            $this->isProcessing = false;
        }
    }

    protected function findCompletedSegment($state, $start, $end)
    {
        $key = $start . '-' . $end;
        return isset($state['completed_segments'][$key]) ? $key : null;
    }

    protected function calculateTotalComparisons($n)
    {
        if ($n <= 1) return 0;
        return intval($n * log($n, 2));
    }

    protected function updateProgress()
    {
        if ($this->sortingState->aprox_comparisons > 0) {
            $this->progressPercentage = min(100, intval(($this->sortingState->completed_comparisons / $this->sortingState->aprox_comparisons) * 100));
        }
    }

    protected function completeSorting($finalSongIds)
    {
        DB::transaction(function () use ($finalSongIds) {
            $this->ranking->update([
                'is_ranked' => true,
                'completed_at' => now(),
            ]);

            $this->sortingState->update([
                'sorting_state' => null,
            ]);

            // Update ranks using just the IDs
            foreach ($finalSongIds as $index => $songId) {
                Song::where('id', $songId)->update([
                    'rank' => $index + 1,
                    'updated_at' => now()
                ]);
            }
        });

        $this->isComplete = true;
        $this->ranking->refresh();
        $this->sortingState->refresh();
        
        Log::channel('discord')->info($this->ranking->user->name . ' completed a ranking: ' . $this->ranking->name);
    }

    public function toggleEmbeds()
    {
        $this->showEmbeds = !$this->showEmbeds;
    }

    public function render()
    {
        return view('livewire.song-rank-process');
    }
}