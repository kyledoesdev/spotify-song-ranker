<?php

namespace App\Livewire\Welcome;

use App\Models\Ranking;
use App\Models\User;
use Livewire\Component;

class Welcome extends Component
{
    public function render()
    {
        return view('livewire.welcome.welcome', [
            'users' => round(User::count() / 50) * 50,
            'rankings' => round(Ranking::query()
                ->where('is_ranked', true)
                ->where('is_public', true)
                ->count() / 25) * 25,
            'artistsCount' => round(Ranking::query()
                ->where('is_ranked', true)
                ->where('is_public', true)
                ->whereHas('artist')
                ->distinct('artist_id')
                ->count('artist_id') / 25) * 25,
        ]);
    }
}
