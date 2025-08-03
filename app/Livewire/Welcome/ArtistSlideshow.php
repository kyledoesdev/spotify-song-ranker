<?php

namespace App\Livewire\Welcome;

use App\Models\ApplicationDashboard;
use App\Models\Artist;
use Illuminate\Support\Collection;
use Livewire\Component;

class ArtistSlideshow extends Component
{
    public Collection $artists;

    public function mount()
    {
        $this->artists = Artist::query()->topArtists()->get();
    }

    public function render()
    {
        return view('livewire.welcome.artist-slideshow', [
            'speed' => ApplicationDashboard::first()->slideshow_speed
        ]);
    }
}
