<?php

namespace App\Livewire\SongRank\Concerns;

use App\Livewire\Forms\RankingForm;
use App\Models\Ranking;

trait HasRankingForm
{
    public RankingForm $form;

    public function confirmBeginRanking(): void
    {
        $songCount = $this->tracksToRank()->count();

        if ($songCount > Ranking::MAX_SONGS) {
            $this->tooManyTracks($songCount, Ranking::MAX_SONGS);

            return;
        }

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

    public function updatedFormCommentsEnabled($value): void
    {
        if (! $value || $value === '0') {
            $this->form->comments_replies_enabled = '0';
        }
    }

    /**
     * The shared attribute bag every Store*Ranking action expects.
     */
    protected function rankingAttributes(): array
    {
        return [
            'ranking_name' => $this->form->name,
            'is_public' => (bool) $this->form->is_public,
            'comments_enabled' => (bool) $this->form->comments_enabled,
            'comments_replies_enabled' => (bool) $this->form->comments_replies_enabled,
            'tracks' => $this->tracksToRank(),
        ];
    }

    protected function resetRankingForm(): void
    {
        $this->reset([
            'form.name',
            'form.is_public',
            'form.comments_enabled',
            'form.comments_replies_enabled',
        ]);
    }
}
