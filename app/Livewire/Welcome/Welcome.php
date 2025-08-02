<?php

namespace App\Livewire\Welcome;

use App\Models\Artist;
use App\Models\Ranking;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class Welcome extends Component
{
    public function render()
    {
        return view('livewire.welcome.welcome', [
            'users' => User::count(),
            'rankings' => Ranking::query()
                ->where('is_ranked', true)
                ->where('is_public', true)
                ->count(),
            'artists' => Artist::query()
                ->inRandomOrder()
                ->get(),
            'artistsCount' => Ranking::query()
                ->where('is_ranked', true)
                ->where('is_public', true)
                ->whereHas('artist')
                ->distinct('artist_id')
                ->count('artist_id')
        ]);
    }
}