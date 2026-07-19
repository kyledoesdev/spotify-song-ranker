<?php

namespace App\Livewire\SongRank;

use App\Actions\Rankings\StoreArtistRanking;
use App\Actions\Rankings\StorePlaylistRanking;
use App\Actions\Rankings\StoreShowRanking;
use App\Actions\Spotify\GetArtistSongs;
use App\Actions\Spotify\GetPlaylistTracks;
use App\Actions\Spotify\GetShowEpisodes;
use App\Actions\Spotify\SearchArtists;
use App\Enums\RankingType;
use App\Livewire\Forms\RankingForm;
use App\Models\Artist;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;

class SongRankSetup extends Component
{
    public RankingForm $form;

    public ?Collection $searchedArtists = null;

    public ?Collection $selectedTracks = null;

    public RankingType $type = RankingType::ARTIST;

    public string $searchTerm = '';

    public string $randomArtist = '';

    public ?array $selectedArtist = [];

    public array $selectedPlaylist = [];

    public array $selectedShow = [];

    public array $removedTrackUuids = [];

    public function mount()
    {
        $this->randomArtist = Artist::inRandomOrder()->first()->artist_name;
    }

    public function render()
    {
        return view('livewire.song-rank.song-rank-setup');
    }

    public function search()
    {
        match ($this->type) {
            RankingType::ARTIST => $this->searchArtist(),
            RankingType::PLAYLIST => $this->searchPlaylist(),
            RankingType::SHOW => $this->searchShow(),
        };
    }

    public function searchArtist()
    {
        $this->selectedArtist = null;
        $this->selectedTracks = null;
        $this->removedTrackUuids = [];

        if ($this->searchTerm == '') {
            $this->invalidSearchTerm();

            return;
        }

        $this->searchedArtists = (new SearchArtists)->handle(Auth::user(), $this->searchTerm);

        if (is_null($this->searchedArtists) || ($this->searchedArtists && $this->searchedArtists->isEmpty())) {
            $this->noArtistFound();
        }
    }

    public function searchPlaylist()
    {
        $this->selectedTracks = null;
        $this->selectedPlaylist = [];
        $this->removedTrackUuids = [];

        if (! $this->isSpotifyPlaylistUrl()) {
            $this->invalidPlaylistURL();

            return;
        }

        $playlistData = (new GetPlaylistTracks)->search(Auth::user(), $this->searchTerm);

        if (is_null($playlistData)) {
            $this->playlistNotFound();

            return;
        }

        $this->selectedPlaylist = $playlistData->except('tracks')->toArray();
        $this->selectedTracks = $playlistData->only('tracks')->first();

        if (! empty($playlistData['deleted_tracks'])) {
            $names = collect($playlistData['deleted_tracks'])
                ->map(fn ($name) => "• {$name}")
                ->implode(PHP_EOL);

            $this->acknowledgeDeletedTracks($names);
        }
    }

    public function searchShow()
    {
        $this->selectedTracks = null;
        $this->selectedShow = [];
        $this->removedTrackUuids = [];

        if (! $this->isSpotifyShowUrl()) {
            $this->invalidShowURL();

            return;
        }

        $showData = (new GetShowEpisodes)->search(Auth::user(), $this->searchTerm);

        if (is_null($showData)) {
            $this->showNotFound();

            return;
        }

        $this->selectedShow = $showData->except('tracks')->toArray();
        $this->selectedTracks = $showData->only('tracks')->first();
    }

    public function loadArtistSongs(string $artistId)
    {
        $this->removedTrackUuids = [];

        $this->selectedArtist = collect($this->searchedArtists)->firstWhere('id', $artistId);

        $this->searchedArtists = null;

        $tracks = (new GetArtistSongs)->handle(Auth::user(), $artistId);

        if (count($tracks) < 2 || is_null($tracks)) {
            $this->notEnoughTracks();

            return;
        }

        $this->selectedTracks = $tracks;
    }

    public function getFilteredTracks()
    {
        return collect($this->selectedTracks)
            ->reject(fn ($song) => in_array($song['uuid'], $this->removedTrackUuids))
            ->values();
    }

    public function filterSongs(string $key)
    {
        $filteredUuids = collect($this->selectedTracks)
            ->filter(fn ($song) => str_contains(strtolower($song['name']), strtolower($key)))
            ->pluck('uuid')
            ->toArray();

        $this->removedTrackUuids = array_merge($this->removedTrackUuids, $filteredUuids);

        $this->dispatch('tracks-batch-removed', uuids: $filteredUuids);
    }

    #[On('song-removed')]
    public function updateSelectedArtistTracks(string $uuid)
    {
        $this->removedTrackUuids[] = $uuid;
    }

    public function confirmBeginRanking()
    {
        $tracks = $this->getFilteredTracks();
        $songCount = count($tracks);

        $message = 'Are you sure you are ready to begin? After starting the ranking process, you WILL NOT be able to remove or edit the songs in the ranking.';

        if ($songCount >= 50) {
            $extraWarning = "Your ranking has {$songCount} songs, it may take > ~30 minutes to complete the ranking. (You can always start it now and pick back up where you left off later).";
            $message = $message.' '.$extraWarning;
        }

        $this->js("
            window.confirm({
                title: 'Begin Ranking?',
                message: '{$message}',
                confirmText: 'Let\\'s Go!',
                componentId: '{$this->getId()}',
                action: 'beginRanking'
            });
        ");
    }

    public function beginRanking()
    {
        $tracks = $this->getFilteredTracks();

        $attributes = [
            'ranking_name' => $this->form->name,
            'is_public' => (bool) $this->form->is_public,
            'comments_enabled' => (bool) $this->form->comments_enabled,
            'comments_replies_enabled' => (bool) $this->form->comments_replies_enabled,
            'tracks' => $tracks,
        ];

        $ranking = match ($this->type) {
            RankingType::ARTIST => (new StoreArtistRanking)->handle(Auth::user(), array_merge($attributes, ['artist' => $this->selectedArtist])),
            RankingType::PLAYLIST => (new StorePlaylistRanking)->handle(Auth::user(), array_merge($attributes, ['playlist' => $this->selectedPlaylist])),
            RankingType::SHOW => (new StoreShowRanking)->handle(Auth::user(), array_merge($attributes, ['show' => $this->selectedShow])),
        };

        $this->redirect(route('ranking', ['id' => $ranking->getKey()]));
    }

    public function resetSetup()
    {
        $this->reset([
            'searchTerm',
            'form.name',
            'form.is_public',
            'form.comments_enabled',
            'form.comments_replies_enabled',
            'searchedArtists',
            'selectedTracks',
            'selectedArtist',
            'selectedPlaylist',
            'selectedShow',
            'removedTrackUuids',
        ]);
    }

    public function isTypeLocked(): bool
    {
        return ! empty($this->selectedArtist)
            || ! empty($this->selectedPlaylist)
            || ! empty($this->selectedShow);
    }

    public function updatedType(RankingType $value): void
    {
        if ($this->isTypeLocked()) {
            $this->type = match (true) {
                ! empty($this->selectedArtist) => RankingType::ARTIST,
                ! empty($this->selectedPlaylist) => RankingType::PLAYLIST,
                default => RankingType::SHOW,
            };

            return;
        }

        $newType = $value;
        $this->resetSetup();
        $this->type = $newType;
    }

    public function updatedFormCommentsEnabled($value): void
    {
        if (! $value || $value === '0') {
            $this->form->comments_replies_enabled = '0';
        }
    }

    private function isSpotifyPlaylistUrl(): bool
    {
        return
            $this->searchTerm !== '' &&
            Str::isUrl($this->searchTerm) &&
            Str::startsWith($this->searchTerm, 'https://open.spotify.com/playlist');
    }

    private function isSpotifyShowUrl(): bool
    {
        return
            $this->searchTerm !== '' &&
            Str::isUrl($this->searchTerm) &&
            Str::startsWith($this->searchTerm, 'https://open.spotify.com/show');
    }

    // TODO - move to a repo / service / library

    private function invalidSearchTerm(): void
    {
        $this->js("
            window.flash({
                title: 'Please enter a valid search term.',
                icon: 'error',
            });
        ");
    }

    private function noArtistFound(): void
    {
        $this->js("
            window.flash({
                title: 'No Artists found.',
                message: 'Could not find artists for search term: {$this->searchTerm}',
                icon: 'error',
            });
        ");
    }

    private function invalidPlaylistURL(): void
    {
        $this->js("
            window.flash({
                title: 'No playlist found.',
                message: 'We could not find a playlist for that URL. Please ensure you entered a valid spotify playlist URL and the playlist is public.',
                icon: 'error',
            });
        ");

        $this->searchTerm = '';
    }

    private function playlistNotFound(): void
    {
        $this->js("
            window.flash({
                title: 'Could not find playlist.',
                message: 'Playlists can have a max of 500 songs. Something went wrong trying to find this playlist: {$this->searchTerm}.',
                icon: 'error',
            });
        ");

        $this->searchTerm = '';
    }

    private function invalidShowURL(): void
    {
        $this->js("
            window.flash({
                title: 'Invalid URL.',
                message: 'Please enter a valid Spotify show URL (e.g. https://open.spotify.com/show/...).',
                icon: 'error',
            });
        ");

        $this->searchTerm = '';
    }

    private function showNotFound(): void
    {
        $this->js("
            window.flash({
                title: 'Could not find show.',
                message: 'Shows can have a max of 500 episodes. Something went wrong trying to find this show: {$this->searchTerm}.',
                icon: 'error',
            });
        ");

        $this->searchTerm = '';
    }

    private function acknowledgeDeletedTracks(string $names): void
    {
        $this->js("
            window.flash({
                title: 'Some tracks could not be found',
                message: 'The following tracks were removed from Spotify and could not be added to this ranking. You can proceed with the other tracks. Sorry 🤷',
                submessage: `{$names}`,
                icon: 'error',
            });
        ");
    }

    private function notEnoughTracks(): void
    {
        $this->js("
            window.flash({
                title: 'Not enough tracks to rank.',
                message: 'The artist: {$this->selectedArtist['name']} does not have enough tracks to rank.',
                icon: 'error',
            });
        ");

        $this->resetSetup();
    }
}
