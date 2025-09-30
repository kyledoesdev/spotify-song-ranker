<?php

namespace App\Livewire;

use App\Models\Artist;
use App\Models\Playlist;
use App\Models\Ranking;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Explorer extends Component
{
    use WithPagination;

    public ?string $search = null;

    public ?string $artist = null;

    public ?string $playlist = null;

    public bool $isSideBarOpen = false;

    public bool $isPlaylistSideBarOpen = false;

    public function render()
    {
        return view('livewire.explorer', [
            'artists' => Artist::query()->topArtists()->limit(10)->get(),
            'playlists' => Playlist::query()->topPlaylists()->limit(10)->get(),
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
            ->paginate(5);
    }

    public function toggleSidebar()
    {
        $this->isSideBarOpen = ! $this->isSideBarOpen;
    }

    public function togglePlaylistSidebar()
    {
        $this->isPlaylistSideBarOpen = ! $this->isPlaylistSideBarOpen;
    }

    public function performSearch()
    {
        $this->resetPage();
    }

    public function resetSearch()
    {
        $this->search = null;
        $this->artist = null;
        $this->playlist = null;

        $this->resetPage();
    }

    public function filterByArtist(string $artistId)
    {
        $this->artist = $artistId;

        $this->playlist = null;
        $this->search = null;

        $this->performSearch();
    }

    public function filterByPlaylist(string $playlistId)
    {
        $this->playlist = $playlistId;

        $this->artist = null;
        $this->search = null;

        $this->performSearch();
    }
}
