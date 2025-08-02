<?php

namespace App\Livewire\SongRank;

use App\Actions\Spotify\GetArtistSongs;
use App\Actions\Spotify\SearchArtists;
use App\Actions\StoreRanking;
use App\Livewire\Forms\RankingForm;
use App\Models\Artist;
use App\Models\Ranking;
use App\Models\Song;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class SongRankSetup extends Component
{
    public RankingForm $form;

    public ?Collection $searchedArtists = null;
    public ?Collection $selectedArtistTracks = null;

    public string $searchTerm = '';
    public string $randomArtist = '';
    public array $selectedArtist = [];
    
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
        if ($this->searchTerm == '') {
            $this->js("
                window.flash({
                    title: 'Please enter a valid search term.',
                    icon: 'error',
                });
            ");

            return;
        }

        $this->searchedArtists = (new SearchArtists)->handle(auth()->user(), $this->searchTerm);

        if ($this->searchedArtists->isEmpty() || is_null($this->searchedArtists)) {
            $this->js("
                window.flash({
                    title: 'No Artists found.',
                    message: 'Could not find artists for search term: {$this->searchTerm}',
                    icon: 'error',
                });
            ");
        }
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
        $this->selectedArtistTracks = collect($this->selectedArtistTracks)->reject(function($song) use ($key) {
            return str_contains(strtolower($song['name']), strtolower($key));
        })->values();
    }

    #[On('song-removed')]
    public function updateSelectedArtistTracks($id)
    {
        $currentTracks = collect($this->selectedArtistTracks)->toArray();
        
        $filteredTracks = array_values(array_filter($currentTracks, function($song) use ($id) {
            return $song['id'] !== $id;
        }));
        
        $this->selectedArtistTracks = collect($filteredTracks);
    }

    public function confirmBeginRanking()
    {
        $songCount = count($this->selectedArtistTracks);
        $message = "Are you sure you are ready to begin? After starting the ranking process, you WILL NOT be able to remove or edit the songs in the ranking.";
        
        if ($songCount >= 50) {
            $extraWarning = "Your ranking has {$songCount} songs, it may take > ~30 minutes to complete the ranking. (You can always start it now and pick back up where you left off later).";
            $message = $message . ' ' . $extraWarning;
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
        $ranking = (new StoreRanking)->handle(auth()->user(), [
            'artist_id' => $this->selectedArtist['id'],
            'artist_name' => $this->selectedArtist['name'],
            'artist_img' => $this->selectedArtist['cover'],
            'ranking_name' => $this->form->name,
            'is_public' => (bool) $this->form->is_public,
            'tracks' => $this->selectedArtistTracks
        ]);

        Log::channel('discord')->info(auth()->user()->name . ' started ranking: ' . $ranking->name);

        $this->redirect(route('rank.show', ['id' => $ranking->getKey()]));
    }

    public function resetSetup()
    {
        $this->reset([
            'searchTerm',
            'form.name',
            'form.is_public',
            'searchedArtists',
            'selectedArtistTracks',
            'selectedArtist'
        ]);
    }
}