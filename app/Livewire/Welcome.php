<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\Ranking;
use App\Models\Artist;
use Livewire\Component;

class Welcome extends Component
{
    public $users;
    public $rankings;
    public $artists;
    public $artistcount;

    public function mount()
    {
        $this->users = User::count();
        $this->rankings = Ranking::where('is_ranked', true)->where('is_public', true)->count();
        $this->artists = Artist::inRandomOrder()->limit(20)->get(['artist_name', 'artist_img']);
        $this->artistcount = Artist::count();
    }

    public function render()
    {
        return view('livewire.welcome');
    }
}