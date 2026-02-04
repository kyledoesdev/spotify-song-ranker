<?php

namespace App\Livewire;

use App\Models\Artist;
use App\Models\Ranking;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Explorer extends Component
{
    public ?string $search = null;

    public ?string $artist = null;

    public ?string $playlist = null;

    public bool $isSideBarOpen = false;

    public bool $isPlaylistSideBarOpen = false;

    public int $perPage = 6;

    public function render()
    {
        return view('livewire.explorer', [
            'artists' => cache()->remember('explore:top-artists', 300, fn () =>
                Artist::query()->topArtists()->limit(10)->get()
            ),
        ]);
    }

    #[Computed]
    public function rankings()
    {
        return Ranking::query()
            ->forExplorePage([
                'search' => $this->search,
                'artist' => $this->artist,
                'playlist' => $this->playlist,
            ])
            ->limit($this->perPage)
            ->get();
    }

    #[Computed]
    public function hasMorePages(): bool
    {
        return $this->rankings->count() === $this->perPage;
    }

    #[Computed]
    public function isFiltered(): bool
    {
        return filled($this->search) || filled($this->artist) || filled($this->playlist);
    }

    public function loadMore()
    {
        $this->perPage += 6;
    }

    public function toggleSidebar()
    {
        $this->isSideBarOpen = ! $this->isSideBarOpen;
    }

    public function performSearch()
    {
        $this->perPage = 6;
    }

    public function resetSearch()
    {
        $this->search = null;
        $this->artist = null;
        $this->playlist = null;
        $this->perPage = 6;
    }

    public function filterByArtist(string $artistId)
    {
        $this->artist = $artistId;
        $this->playlist = null;
        $this->search = null;
        $this->perPage = 6;
    }

    public function filterByPlaylist(string $playlistId)
    {
        $this->playlist = $playlistId;
        $this->artist = null;
        $this->search = null;
        $this->perPage = 6;
    }
}