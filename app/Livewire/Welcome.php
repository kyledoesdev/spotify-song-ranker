<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\Ranking;
use App\Models\Artist;
use Livewire\Component;

class Welcome extends Component
{
    public function render()
    {
        return view('livewire.welcome', [
            'users' => User::count(),
            'rankings' => Ranking::where('is_ranked', true)->where('is_public', true)->count(),
            'artists' => Artist::inRandomOrder()->limit(20)->get(['artist_name', 'artist_img']),
            'artistsCount' => Artist::count(),
        ]);
    }
}