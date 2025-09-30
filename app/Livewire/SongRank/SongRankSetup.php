<?php

namespace App\Livewire\SongRank;

use App\Actions\Spotify\GetArtistSongs;
use App\Actions\Spotify\GetPlaylistTracks;
use App\Actions\Spotify\SearchArtists;
use App\Actions\StoreRanking;
use App\Enums\RankingType;
use App\Livewire\Forms\RankingForm;
use App\Models\Artist;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
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
        if ($this->artistSearchTerm == '') {
            $this->js("
                window.flash({
                    title: 'Please enter a valid search term.',
                    icon: 'error',
                });
            ");

            return;
        }

        $this->searchedArtists = (new SearchArtists)->handle(auth()->user(), $this->artistSearchTerm);

        $this->type = RankingType::ARTIST;

        if (is_null($this->searchedArtists) || ($this->searchedArtists && $this->searchedArtists->isEmpty())) {
            $this->js("
                window.flash({
                    title: 'No Artists found.',
                    message: 'Could not find artists for search term: {$this->artistSearchTerm}',
                    icon: 'error',
                });
            ");
        }
    }

    public function searchPlaylist()
    {
        /* ensure playlist url is valid */
        if (! $this->isSpotifyPlaylistUrl()) {
            $this->js("
                window.flash({
                    title: 'No playlist found.',
                    message: 'We could not find a playlist for that URL. Please ensure you entered a valid spotify playlist URL and the playlist is public.',
                    icon: 'error',
                });
            ");

            $this->playlistURL = '';

            return;
        }

        $this->type = RankingType::PLAYLIST;

        $playlistData = (new GetPlaylistTracks)->search(auth()->user(), $this->playlistURL);

        /* ensure playlist has tracks */
        if (is_null($playlistData)) {
            $this->js("
                window.flash({
                    title: 'Could not find playlist.',
                    message: 'Something went wrong trying to find this playlist: {$this->playlistURL}.',
                    icon: 'error',
                });
            ");

            $this->playlistURL = '';

            return;
        }

        $this->selectedPlaylist = $playlistData->except('tracks')->toArray();
        $this->selectedPlaylistTracks = $playlistData->only('tracks')->first();
    }

    public function loadArtistSongs(string $artistId)
    {
        $this->selectedArtist = collect($this->searchedArtists)->firstWhere('id', $artistId);

        $this->searchedArtists = null;

        $tracks = (new GetArtistSongs)->handle(auth()->user(), $artistId);

        if (count($tracks) <= 1 || is_null($tracks)) {
            $this->js("
                window.flash({
                    title: 'Not enough tracks to rank.',
                    message: 'The artist: {$this->selectedArtist['name']} does not have enough tracks to rank.',
                    icon: 'error',
                });
            ");

            $this->resetSetup();

            return;
        }

        $this->selectedArtistTracks = $tracks;
    }

    public function filterSongs(string $key)
    {
        $property = $this->type === RankingType::ARTIST ? 'selectedArtistTracks' : 'selectedPlaylistTracks';

        $this->$property = collect($this->$property)
            ->reject(fn ($song) => str_contains(strtolower($song['name']), strtolower($key)))
            ->values();
    }

    #[On('song-removed')]
    public function updateSelectedArtistTracks(string $id)
    {
        $property = $this->type === RankingType::ARTIST ? 'selectedArtistTracks' : 'selectedPlaylistTracks';

        $this->$property = collect($this->$property)
            ->reject(fn ($song) => $song['id'] === $id)
            ->values();
    }

    public function confirmBeginRanking()
    {
        $songCount = $this->type === RankingType::ARTIST
            ? count($this->selectedArtistTracks)
            : count($this->selectedPlaylistTracks);

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
        $ranking = (new StoreRanking)->handle(auth()->user(), $this->type, $this->type === RankingType::PLAYLIST ? [
            'playlist' => $this->selectedPlaylist,
            'ranking_name' => $this->form->name,
            'is_public' => (bool) $this->form->is_public,
            'tracks' => $this->selectedPlaylistTracks,
        ] : [
            'artist' => $this->selectedArtist,
            'ranking_name' => $this->form->name,
            'is_public' => (bool) $this->form->is_public,
            'tracks' => $this->selectedArtistTracks,
        ]);

        Log::channel('discord_ranking_updates')->info(auth()->user()->name.' started ranking: '.$ranking->name."({$this->type->value} type)");

        $this->redirect(route('ranking', ['id' => $ranking->getKey()]));
    }

    public function resetSetup()
    {
        $this->reset([
            'artistSearchTerm',
            'playlistURL',
            'form.name',
            'form.is_public',
            'searchedArtists',
            'selectedArtistTracks',
            'selectedArtist',
            'selectedPlaylist',
            'selectedPlaylistTracks',
        ]);
    }

    private function isSpotifyPlaylistUrl(): bool
    {
        return
            $this->playlistURL !== '' &&
            Str::isUrl($this->playlistURL) &&
            Str::startsWith($this->playlistURL, 'https://open.spotify.com/playlist');
    }
}
