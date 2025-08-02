<?php

namespace App\Livewire;

use App\Models\Ranking as RankingModel;
use Livewire\Component;

class Ranking extends Component
{
    public RankingModel $ranking;

    public function mount(int $id)
    {
        $this->ranking = RankingModel::query()
            ->with(['user', 'songs', 'artist', 'sortingState'])
            ->findOrFail($id);

        if (! $this->ranking->isPublic()) {
            abort(404);
        }

        if (! $this->ranking->is_ranked && $this->ranking->user_id != auth()->id()) {
            abort(403, 'This ranking is not complete. You can not view it.');
        }
    }

    public function render()
    {
        return view('livewire.ranking', [
            'ranking' => $this->ranking,
            'sortingState' => $this->ranking->sortingState,
        ]);
    }
}
