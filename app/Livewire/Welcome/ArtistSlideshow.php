<?php

namespace App\Livewire\Welcome;

use App\Models\ApplicationDashboard;
use App\Models\Artist;
use Illuminate\Support\Collection;
use Livewire\Component;

class ArtistSlideshow extends Component
{
    public Collection $artists;

    public function mount(): void
    {
        $this->artists = cache()->remember(
            'welcome:top-artists',
            now()->addDay(),
            fn () => Artist::query()->topArtists(100)->get()
        ) ?? collect();
    }

    public function render()
    {
        return view('livewire.welcome.artist-slideshow', [
            'speed' => ApplicationDashboard::first()?->slideshow_speed,
        ]);
    }
}
