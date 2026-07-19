<?php

namespace App\Livewire;

use App\Models\Artist;
use App\Models\Ranking;
use App\Models\User;
use Illuminate\Support\Collection;
use Livewire\Component;

class Leaderboards extends Component
{
    public function render()
    {
        return view('livewire.leaderboards', [
            'topArtists' => $this->topArtists(),
            'topCreators' => $this->topCreators(),
            'biggestRankings' => $this->biggestRankings(),
        ]);
    }

    private function topArtists(): Collection
    {
        return cache()->remember('leaderboards:top-artists', now()->addHour(), fn () => Artist::query()
            ->topArtists()
            ->get()
            ->map(fn (Artist $artist) => [
                'image' => $artist->artist_img,
                'name' => $artist->artist_name,
                'count' => $artist->artist_rankings_count,
                'url' => null,
                'spotify_url' => $artist->spotifyUrl(),
            ]));
    }

    private function topCreators(): Collection
    {
        return cache()->remember('leaderboards:top-creators', now()->addHour(), fn () => User::query()
            ->topCreators()
            ->get()
            ->map(fn (User $user) => [
                'image' => $user->avatar,
                'name' => $user->name,
                'count' => $user->rankings_count,
                'url' => route('profile', ['id' => $user->spotify_id]),
                'spotify_url' => null,
            ]));
    }

    private function biggestRankings(): Collection
    {
        return cache()->remember('leaderboards:most-songs', now()->addHour(), fn () => Ranking::query()
            ->mostSongs()
            ->get()
            ->map(fn (Ranking $ranking) => [
                'image' => $ranking->source?->cover(),
                'name' => $ranking->name,
                'subtitle' => $ranking->user->name,
                'count' => $ranking->songs_count,
                'url' => route('ranking', ['id' => $ranking->getKey()]),
                'spotify_url' => $ranking->source?->spotifyUrl(),
            ]));
    }
}
