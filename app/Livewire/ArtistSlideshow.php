<?php

namespace App\Livewire;

use Illuminate\Support\Collection;
use Livewire\Component;

class ArtistSlideshow extends Component
{
    public Collection $artists;

    public function mount(Collection $artists)
    {
        $this->artists = $artists;
    }

    public function getDuplicatedArtistsProperty()
    {
        return collect($this->artists)
            ->merge($this->artists)
            ->merge($this->artists)
            ->merge($this->artists);
    }

    public function render()
    {
        return view('livewire.artist-slideshow');
    }
}