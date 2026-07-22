<?php

namespace App\Livewire\SongRank\Setup;

use App\Actions\Rankings\StoreArtistRanking;
use App\Actions\Spotify\GetArtistAppearsOnSongs;
use App\Actions\Spotify\GetArtistSongs;
use App\Actions\Spotify\SearchArtists;
use App\Enums\RankingType;
use App\Livewire\SongRank\Concerns\HasRankingForm;
use App\Livewire\SongRank\Concerns\HasSetupFlashErrors;
use App\Livewire\SongRank\Concerns\HasTrackList;
use App\Models\Artist;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ArtistSetup extends Component
{
    use HasRankingForm;
    use HasSetupFlashErrors;
    use HasTrackList;

    public ?Collection $searchedArtists = null;

    public ?Collection $featuredTracks = null;

    public string $searchTerm = '';

    public string $randomArtist = '';

    public ?array $selectedArtist = null;

    public bool $includeFeaturedTracks = false;

    public int $appearsOnCount = 0;

    public function mount(): void
    {
        $this->randomArtist = Artist::query()
            ->whereNotNull('artist_img')
            ->inRandomOrder()
            ->first()?->artist_name ?? '';
    }

    public function render()
    {
        return view('livewire.song-rank.setup.artist-setup');
    }

    public function rankingType(): RankingType
    {
        return RankingType::ARTIST;
    }

    public function search(): void
    {
        $this->selectedArtist = null;
        $this->resetTrackList();
        $this->resetFeaturedTracks();

        if ($this->searchTerm === '') {
            $this->invalidSearchTerm();

            return;
        }

        $this->searchedArtists = (new SearchArtists)->handle(Auth::user(), $this->searchTerm);

        if (is_null($this->searchedArtists) || $this->searchedArtists->isEmpty()) {
            $this->noArtistFound($this->searchTerm);
        }
    }

    public function loadArtistSongs(string $artistId): void
    {
        $this->resetTrackList();
        $this->resetFeaturedTracks();

        $this->selectedArtist = collect($this->searchedArtists)->firstWhere('id', $artistId);
        $this->searchedArtists = null;

        $tracks = (new GetArtistSongs)->handle(Auth::user(), $artistId);

        if (is_null($tracks) || $tracks->count() < 2) {
            $this->notEnoughTracks(data_get($this->selectedArtist, 'name', 'this artist'));
            $this->resetSetup();

            return;
        }

        $this->selectedTracks = $tracks;
        $this->appearsOnCount = (new GetArtistAppearsOnSongs)->count(Auth::user(), $artistId);
    }

    /**
     * Fetching an artist's appearances is expensive for prolific guests, so it
     * waits until the toggle first flips on rather than loading with the catalog.
     */
    public function updatedIncludeFeaturedTracks(bool $value): void
    {
        if ($value && is_null($this->featuredTracks)) {
            $this->featuredTracks = (new GetArtistAppearsOnSongs)->handle(
                Auth::user(),
                data_get($this->selectedArtist, 'id', ''),
                collect($this->selectedTracks),
            );

            if (is_null($this->featuredTracks)) {
                $this->includeFeaturedTracks = false;
                $this->featuredTracksUnavailable(data_get($this->selectedArtist, 'name', 'this artist'));
            }
        }

        $this->js('window.hideLoader()');
    }

    // -- Track lists --

    /**
     * Featured tracks only join the ranking while the toggle is on, so nothing leaks in when it is off.
     */
    public function tracksToRank(): Collection
    {
        return $this->withoutRemovedTracks($this->selectedTracks)
            ->concat($this->featuredTracksToRank())
            ->values();
    }

    public function featuredTracksToRank(): Collection
    {
        if (! $this->includeFeaturedTracks) {
            return collect();
        }

        return $this->withoutRemovedTracks($this->featuredTracks);
    }

    public function hasFeaturedTracks(): bool
    {
        return collect($this->featuredTracks)->isNotEmpty();
    }

    protected function allTracks(): Collection
    {
        return collect($this->selectedTracks)->concat($this->featuredTracks ?? []);
    }

    protected function resetFeaturedTracks(): void
    {
        $this->featuredTracks = null;
        $this->includeFeaturedTracks = false;
        $this->appearsOnCount = 0;
    }

    // -- Starting the ranking --

    public function beginRanking(): void
    {
        $ranking = (new StoreArtistRanking)->handle(Auth::user(), [
            ...$this->rankingAttributes(),
            'artist' => $this->selectedArtist,
        ]);

        $this->redirect(route('ranking', ['id' => $ranking->getKey()]));
    }

    public function resetSetup(): void
    {
        $this->reset([
            'searchTerm',
            'searchedArtists',
            'selectedArtist',
            'selectedTracks',
            'featuredTracks',
            'removedTrackUuids',
            'includeFeaturedTracks',
            'appearsOnCount',
        ]);

        $this->resetRankingForm();
    }
}
