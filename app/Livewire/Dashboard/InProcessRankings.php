<?php

namespace App\Livewire\Dashboard;

use App\Models\Ranking;
use Livewire\Component;

class InProcessRankings extends Component
{
    public function render()
    {
        return view('livewire.dashboard.in-process-rankings', [
            'rankings' => Ranking::query()
                ->where('user_id', auth()->id())
                ->where('is_ranked', false)
                ->with(['artist', 'user'])
                ->with('songs', fn ($q) => $q->where('rank', 1))
                ->withCount('songs')
                ->get(),
        ]);
    }
}
