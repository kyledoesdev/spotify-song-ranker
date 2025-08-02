<?php

namespace App\Livewire;

use App\Models\Artist;
use App\Models\Ranking;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Explorer extends Component
{
    use WithPagination;

    public string $search = '';
    public string $artist = '';

    public bool $isSideBarOpen = false;

    public function render()
    {
        return view('livewire.explorer', [
            'artists' => Artist::query()->topArtists()->get()
        ]);
    }

    #[Computed]
    public function rankings()
    {
        return Ranking::query()
            ->forExplorePage($this->search, $this->artist)
            ->paginate(5);
    }

    public function toggleSidebar()
    {
        $this->isSideBarOpen = ! $this->isSideBarOpen;
    }

    public function performSearch()
    {
        $this->resetPage();
    }

    public function resetSearch()
    {
        $this->search = '';
        $this->artist = '';
        $this->resetPage();
    }

    public function filterByArtist($artistId)
    {
        $this->artist = $artistId;
        $this->performSearch();
    }
}