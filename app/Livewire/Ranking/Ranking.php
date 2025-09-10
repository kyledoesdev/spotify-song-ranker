<?php

namespace App\Livewire\Ranking;

use App\Models\Ranking as RankingModel;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Ranking extends Component
{
    public ?RankingModel $ranking;

    public function mount($id)
    {
        $this->ranking = RankingModel::query()
            ->with(['user', 'songs', 'artist', 'sortingState'])
            ->find($id);

        if (is_null($this->ranking)) {
            $email = auth()->check() ? auth()->user()->email : request()->ip();
            
            Log::channel('discord_other_updates')->info("Ranking not found: Id Given: {$id} :: User Email: {$email}");

            abort(404);
        }

        if (! $this->ranking->is_public && $this->ranking->user_id != auth()->id()) {
            abort(404);
        }

        if (! $this->ranking->is_ranked && $this->ranking->user_id != auth()->id()) {
            abort(403, 'This ranking is not complete. You can not view it.');
        }

        session()->put(['ranking_name' => $this->ranking->name]);
    }

    public function render()
    {
        return view('livewire.ranking.ranking', [
            'ranking' => $this->ranking,
            'sortingState' => $this->ranking->sortingState,
        ]);
    }
}
