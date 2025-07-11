<?php

namespace App\Livewire;

use App\Models\Ranking;
use App\Models\Song;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class SongRankProcess extends Component
{
    public Ranking $ranking;
    public $currentSong1;
    public $currentSong2;
    public bool $showEmbeds = true;
    public bool $isComplete = false;
    public bool $isProcessing = false;
    public $progressPercentage = 0;
    public int $rankingId;
    
    protected $listeners = ['toggleEmbeds'];

    public function mount()
    {
        $this->ranking = Ranking::query()
            ->with(['user', 'songs', 'artist'])
            ->findOrFail($this->rankingId);
        
        if (! $this->ranking->is_public && $this->ranking->user_id != auth()->id()) {
            abort(404);
        }
                
        if (! $this->ranking->is_ranked && $this->ranking->user_id != auth()->id()) {
           abort(403, 'This ranking is not complete. You can not view it.');
        }

        if ($this->ranking->is_ranked) {
            $this->isComplete = true;
            return;
        }

        $this->initializeSorting();
    }

    public function initializeSorting()
    {
        if (!$this->ranking->sorting_state) {
            $this->startNewSorting();
        }
        
        $this->updateProgress();
        $this->continueSort();
    }

    protected function startNewSorting()
    {
        $songs = $this->ranking->songs->shuffle()->values()->map(function ($song) {
            return [
                'id' => $song->id,
                'title' => $song->title,
                'cover' => $song->cover,
                'spotify_song_id' => $song->spotify_song_id
            ];
        })->toArray();
        
        // Initialize with a stack-based approach for merge sort
        $state = [
            'stack' => [
                [
                    'type' => 'sort',
                    'array' => $songs,
                    'start' => 0,
                    'end' => count($songs) - 1,
                    'depth' => 0
                ]
            ],
            'current_merge' => null,
            'completed_segments' => [] // Store completed sorted segments
        ];
        
        $this->ranking->update([
            'sorting_state' => $state,
            'total_comparisons' => $this->calculateTotalComparisons(count($songs)),
            'completed_comparisons' => 0
        ]);
    }

    protected function continueSort()
    {
        $this->ranking->refresh();
        $state = $this->ranking->sorting_state;
        
        // If we have a current merge in progress, show the comparison
        if ($state['current_merge']) {
            $this->processMerge();
            return;
        }
        
        // Process the stack
        while (!empty($state['stack'])) {
            $task = array_pop($state['stack']);
            
            if ($task['type'] === 'sort') {
                $this->handleSortTask($task, $state);
            } elseif ($task['type'] === 'merge') {
                // Start a new merge
                $state['current_merge'] = $task;
                $this->ranking->update(['sorting_state' => $state]);
                $this->processMerge();
                return;
            }
        }
        
        // If stack is empty and no current merge, we're done
        if (empty($state['stack']) && !$state['current_merge']) {
            // Find the final sorted array
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
        $array = $task['array'];
        
        // Base case: single element or empty
        if ($start >= $end) {
            $key = $start . '-' . $end;
            $state['completed_segments'][$key] = array_slice($array, $start, $end - $start + 1);
            $this->ranking->update(['sorting_state' => $state]);
            return;
        }
        
        $mid = intval(($start + $end) / 2);
        
        // Push merge task (will be processed after both halves are sorted)
        array_push($state['stack'], [
            'type' => 'merge',
            'array' => $array,
            'start' => $start,
            'mid' => $mid,
            'end' => $end,
            'left_key' => $start . '-' . $mid,
            'right_key' => ($mid + 1) . '-' . $end,
            'depth' => $task['depth'] + 1
        ]);
        
        // Push right sort task
        array_push($state['stack'], [
            'type' => 'sort',
            'array' => $array,
            'start' => $mid + 1,
            'end' => $end,
            'depth' => $task['depth'] + 1
        ]);
        
        // Push left sort task (will be processed first)
        array_push($state['stack'], [
            'type' => 'sort',
            'array' => $array,
            'start' => $start,
            'end' => $mid,
            'depth' => $task['depth'] + 1
        ]);
        
        $this->ranking->update(['sorting_state' => $state]);
    }

    protected function processMerge()
    {
        $this->ranking->refresh();
        $state = $this->ranking->sorting_state;
        $merge = $state['current_merge'];
        
        if (!$merge) {
            $this->continueSort();
            return;
        }
        
        // Initialize merge if needed
        if (!isset($merge['left']) || !isset($merge['right'])) {
            $leftKey = $merge['left_key'];
            $rightKey = $merge['right_key'];
            
            // Get the sorted segments
            $left = $state['completed_segments'][$leftKey] ?? [];
            $right = $state['completed_segments'][$rightKey] ?? [];
            
            $merge['left'] = array_values($left);
            $merge['right'] = array_values($right);
            $merge['result'] = [];
            
            $state['current_merge'] = $merge;
            $this->ranking->update(['sorting_state' => $state]);
            $this->ranking->refresh();
        }
        
        // Check if we need to compare
        if (!empty($merge['left']) && !empty($merge['right'])) {
            $this->currentSong1 = $merge['left'][0];
            $this->currentSong2 = $merge['right'][0];
        } else {
            // Merge is complete
            $result = array_merge($merge['result'], $merge['left'], $merge['right']);
            $key = $merge['start'] . '-' . $merge['end'];
            $state['completed_segments'][$key] = $result;
            $state['current_merge'] = null;
            
            $this->ranking->update(['sorting_state' => $state]);
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
            $this->ranking->refresh();
            $state = $this->ranking->sorting_state;
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

            // Process the choice
            if ($songId === $song1Id) {
                $merge['result'][] = array_shift($merge['left']);
            } else {
                $merge['result'][] = array_shift($merge['right']);
            }
            
            $state['current_merge'] = $merge;
            
            DB::transaction(function () use ($state) {
                $this->ranking->increment('completed_comparisons');
                $this->ranking->update(['sorting_state' => $state]);
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
        if ($this->ranking->total_comparisons > 0) {
            $this->progressPercentage = min(100, intval(($this->ranking->completed_comparisons / $this->ranking->total_comparisons) * 100));
        }
    }

    protected function completeSorting($finalResult)
    {
        DB::transaction(function () use ($finalResult) {
            $this->ranking->update([
                'is_ranked' => true,
                'completed_at' => now(),
                'sorting_state' => null
            ]);

            foreach ($finalResult as $index => $song) {
                Song::where('id', $song['id'])->update([
                    'rank' => $index + 1,
                    'updated_at' => now()
                ]);
            }
        });

        $this->isComplete = true;
        $this->ranking->refresh();
        
        if (auth()->check()) {
            \Log::channel('discord')->info(auth()->user()->name . ' completed a ranking: ' . $this->ranking->name);
        }
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