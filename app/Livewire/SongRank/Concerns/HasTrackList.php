<?php

namespace App\Livewire\SongRank\Concerns;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Livewire\Attributes\On;

trait HasTrackList
{
    public ?Collection $selectedTracks = null;

    public array $removedTrackUuids = [];

    /**
     * The tracks the ranking will actually hold once removals are taken out.
     */
    public function tracksToRank(): Collection
    {
        return $this->withoutRemovedTracks($this->selectedTracks);
    }

    #[On('track-removed')]
    public function removeTrack(string $uuid): void
    {
        $this->removedTrackUuids[] = $uuid;
    }

    public function removeTracksMatching(string $term): void
    {
        $uuids = $this->allTracks()
            ->filter(fn (array $track) => Str::contains($track['name'], $term, ignoreCase: true))
            ->pluck('uuid')
            ->toArray();

        $this->removedTrackUuids = array_merge($this->removedTrackUuids, $uuids);

        $this->dispatch('tracks-batch-removed', uuids: $uuids);
    }

    /**
     * Every track this component renders, across all of its lists.
     */
    protected function allTracks(): Collection
    {
        return collect($this->selectedTracks);
    }

    protected function withoutRemovedTracks(?Collection $tracks): Collection
    {
        return collect($tracks)
            ->reject(fn (array $track) => in_array($track['uuid'], $this->removedTrackUuids))
            ->values();
    }

    protected function resetTrackList(): void
    {
        $this->selectedTracks = null;
        $this->removedTrackUuids = [];
    }
}
