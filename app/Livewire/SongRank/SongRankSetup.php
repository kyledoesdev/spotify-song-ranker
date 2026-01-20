<?php

namespace App\Livewire\SongRank;

use App\Actions\Rankings\StoreArtistRanking;
use App\Actions\Rankings\StorePlaylistRanking;
use App\Actions\Spotify\GetArtistSongs;
use App\Actions\Spotify\GetPlaylistTracks;
use App\Actions\Spotify\SearchArtists;
use App\Enums\RankingType;
use App\Livewire\Forms\RankingForm;
use App\Models\Artist;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;

class SongRankSetup extends Component
{
    public RankingForm $form;

    public ?Collection $searchedArtists = null;

    public ?Collection $selectedArtistTracks = null;

    public ?Collection $selectedPlaylistTracks = null;

    public RankingType $type = RankingType::ARTIST;

    public string $artistSearchTerm = '';

    public string $playlistURL = '';

    public string $randomArtist = '';

    public ?array $selectedArtist = [];

    public array $selectedPlaylist = [];

    public array $removedTrackUuids = [];

    public function mount()
    {
        $this->randomArtist = Artist::inRandomOrder()->first()->artist_name;
    }

    public function render()
    {
        return view('livewire.song-rank.song-rank-setup');
    }

    public function searchArtist()
    {
        $this->selectedArtist = null;
        $this->selectedArtistTracks = null;
        $this->removedTrackUuids = [];

        if ($this->artistSearchTerm == '') {
            $this->invalidSearchTerm();

            return;
        }

        $this->searchedArtists = (new SearchArtists)->handle(auth()->user(), $this->artistSearchTerm);
        $this->type = RankingType::ARTIST;

        if (is_null($this->searchedArtists) || ($this->searchedArtists && $this->searchedArtists->isEmpty())) {
            $this->noArtistFound();
        }
    }

    public function searchPlaylist()
    {
        $this->selectedPlaylistTracks = null;
        $this->selectedPlaylist = [];
        $this->removedTrackUuids = [];

        /* ensure playlist url is valid */
        if (! $this->isSpotifyPlaylistUrl()) {
            $this->invalidPlaylistURL();

            return;
        }

        $this->type = RankingType::PLAYLIST;

        $playlistData = (new GetPlaylistTracks)->search(auth()->user(), $this->playlistURL);

        /* ensure playlist has tracks */
        if (is_null($playlistData)) {
            $this->playlistNotFound();

            return;
        }

        $this->selectedPlaylist = $playlistData->except('tracks')->toArray();
        $this->selectedPlaylistTracks = $playlistData->only('tracks')->first();

        if (! empty($playlistData['deleted_tracks'])) {
            $names = collect($playlistData['deleted_tracks'])
                ->map(fn ($name) => "• {$name}")
                ->implode(PHP_EOL);

            $this->acknowledgeDeletedTracks($names);
        }
    }

    public function loadArtistSongs(string $artistId)
    {
        $this->removedTrackUuids = [];

        $this->selectedArtist = collect($this->searchedArtists)->firstWhere('id', $artistId);

        $this->searchedArtists = null;

        $tracks = (new GetArtistSongs)->handle(auth()->user(), $artistId);

        if (count($tracks) < 2 || is_null($tracks)) {
            $this->notEnoughTracks();

            return;
        }

        $this->selectedArtistTracks = $tracks;
    }

    public function getFilteredTracks()
    {
        $property = $this->type === RankingType::ARTIST ? 'selectedArtistTracks' : 'selectedPlaylistTracks';

        return collect($this->$property)
            ->reject(fn ($song) => in_array($song['uuid'], $this->removedTrackUuids))
            ->values();
    }

    public function filterSongs(string $key)
    {
        $property = $this->type === RankingType::ARTIST ? 'selectedArtistTracks' : 'selectedPlaylistTracks';

        $filteredUuids = collect($this->$property)
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

        $ranking = $this->type === RankingType::PLAYLIST
            ? (new StorePlaylistRanking)->handle(auth()->user(), array_merge($attributes, ['playlist' => $this->selectedPlaylist]))
            : (new StoreArtistRanking)->handle(auth()->user(), array_merge($attributes, ['artist' => $this->selectedArtist]));

        $this->redirect(route('ranking', ['id' => $ranking->getKey()]));
    }

    public function resetSetup()
    {
        $this->reset([
            'artistSearchTerm',
            'playlistURL',
            'form.name',
            'form.is_public',
            'form.comments_enabled',
            'form.comments_replies_enabled',
            'searchedArtists',
            'selectedArtistTracks',
            'selectedArtist',
            'selectedPlaylist',
            'selectedPlaylistTracks',
            'removedTrackUuids',
        ]);
    }

    public function updatedFormCommentsEnabled($value): void
    {
        if (!$value || $value === '0') {
            $this->form->comments_replies_enabled = '0';
        }
    }

    private function isSpotifyPlaylistUrl(): bool
    {
        return
            $this->playlistURL !== '' &&
            Str::isUrl($this->playlistURL) &&
            Str::startsWith($this->playlistURL, 'https://open.spotify.com/playlist');
    }

    //TODO - move to a repo / service / library

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
                message: 'Could not find artists for search term: {$this->artistSearchTerm}',
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

        $this->playlistURL = '';
    }

    private function playlistNotFound(): void
    {
        $this->js("
            window.flash({
                title: 'Could not find playlist.',
                message: 'Playlists can have a max of 500 songs. Something went wrong trying to find this playlist: {$this->playlistURL}.',
                icon: 'error',
            });
        ");

        $this->playlistURL = '';
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
