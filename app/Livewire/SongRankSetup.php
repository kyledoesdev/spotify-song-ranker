<?php

namespace App\Livewire;

use App\Http\Controllers\SpotifyAPIController;
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
    public string $searchTerm = '';
    public string $randomArtist = '';
    public string $rankingName = '';
    public bool $isPublic = true;
    
    public ?Collection $searchedArtists = null;
    public ?Collection $selectedArtistTracks = null;
    public array $selectedArtist = [];
    
    public function mount()
    {
        $this->randomArtist = Artist::inRandomOrder()->first()->artist_name;
    }

    public function render()
    {
        return view('livewire.song-rank-setup');
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

        $this->searchedArtists = (new SpotifyAPIController)->search($this->searchTerm);

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

        $tracks = (new SpotifyAPIController)->artistSongs($artistId);

        if (count($tracks) <= 1) {
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
        $message = "Are you ready to begin? After starting the ranking process, you WILL NOT be able to remove or edit the songs in the ranking.";
        
        if ($songCount >= 50) {
            $extraWarning = "Your ranking has {$songCount} songs, it may take > ~30 minutes to complete the ranking.";
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
        $ranking = DB::transaction(function () {
            /* update or create the artist */
            $artist = Artist::updateOrCreate([
                'artist_id' => $this->selectedArtist['id'],
            ], [
                'artist_name' => $this->selectedArtist['name'],
                'artist_img' => $this->selectedArtist['cover'],
            ]);

            /* create a new ranking */
            $ranking = Ranking::create([
                'user_id' => auth()->id(),
                'artist_id' => $artist->getKey(),
                'name' => Str::limit($this->rankingName ?? $artist->artist_name.' List', 30),
                'is_public' => $this->isPublic ?? false,
            ]);

            /* create the relation to the ranking's sorted state */
            $ranking->sortingState()->create();

            $songs = [];
            foreach ($this->selectedArtistTracks as $song) {
                array_push($songs, [
                    'ranking_id' => $ranking->getKey(),
                    'spotify_song_id' => $song['id'],
                    'uuid' => Str::uuid(),
                    'title' => $song['name'] ?? 'track deleted from spotify servers',
                    'cover' => $song['cover'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            /* batch insert the song records */
            Song::insert($songs);

            return $ranking;
        });

        Log::channel('discord')->info(auth()->user()->name . ' started ranking: ' . $ranking->name);

        $this->redirect(route('rank.show', ['id' => $ranking->getKey()]));
    }

    public function resetSetup()
    {
        $this->reset([
            'searchTerm',
            'rankingName',
            'isPublic',
            'searchedArtists',
            'selectedArtistTracks',
            'selectedArtist'
        ]);
    }
}