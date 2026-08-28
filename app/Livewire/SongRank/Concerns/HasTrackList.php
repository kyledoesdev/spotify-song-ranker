<?php

namespace App\Livewire\SongRank\Concerns;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Livewire\Attributes\On;

trait HasTrackList
{
    public Collection $selectedTracks;

    public Collection $featuredTracks;

    public Collection $removedTrackUuids;

    public function initializeHasTrackList(): void
    {
        $this->selectedTracks ??= collect();
        $this->featuredTracks ??= collect();
        $this->removedTrackUuids ??= collect();
    }

    // -- Querying tracks --

    protected function allTracks(): Collection
    {
        return collect($this->selectedTracks)->concat($this->featuredTracks);
    }

    public function rankableTracks(): Collection
    {
        return $this->allTracks()
            ->reject(fn (array $track) => $this->removedTrackUuids->contains($track['uuid']))
            ->values();
    }

    public function hasFeaturedTracks(): bool
    {
        return $this->featuredTracks->isNotEmpty();
    }

    public function removedTracks(): Collection
    {
        return $this->allTracks()
            ->filter(fn (array $track) => $this->removedTrackUuids->contains($track['uuid']))
            ->values();
    }

    // -- Removing tracks --

    #[On('track-removed')]
    public function removeTrack(string $uuid): void
    {
        $this->removedTrackUuids->push($uuid);
    }

    public function removeTracksMatching(string $term): void
    {
        $uuids = $this->allTracks()
            ->filter(fn (array $track) => Str::contains($track['name'], $term, ignoreCase: true))
            ->pluck('uuid');

        $this->removedTrackUuids = $this->removedTrackUuids->merge($uuids)->values();

        $this->dispatch('tracks-batch-removed', uuids: $uuids->values()->all());
    }

    // -- Restoring tracks --

    public function restoreTrack(string $uuid): void
    {
        $this->removedTrackUuids = $this->removedTrackUuids->reject(fn (string $id) => $id === $uuid)->values();

        $this->dispatch('tracks-batch-restored', uuids: [$uuid]);
    }

    // -- Resetting --

    protected function resetTrackList(): void
    {
        $this->selectedTracks = collect();
        $this->featuredTracks = collect();
        $this->removedTrackUuids = collect();
    }
}
