<?php

namespace App\Livewire\Ranking;

use App\Livewire\Forms\RankingForm;
use App\Models\Ranking;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class EditRanking extends Component
{
    public ?Ranking $ranking;

    public RankingForm $form;

    public function mount($id)
    {
        $this->ranking = Ranking::query()
            ->with('songs')
            ->find($id);

        if (is_null($this->ranking)) {
            $email = auth()->check() ? auth()->user()->email : request()->ip();

            Log::channel('discord_other_updates')->info("Ranking not found: Id Given: {$id} :: User Email: {$email}");

            abort(404);
        }

        if ($this->ranking->user_id != auth()->id()) {
            abort(403, 'You are not allowed to edit this ranking.');
        }

        $this->form->fill([
            'name' => $this->ranking->name,
            'is_public' => $this->ranking->is_public ? '1' : '0',
            'comments_enabled' => $this->ranking->comments_enabled ? '1' : '0',
            'comments_replies_enabled' => $this->ranking->comments_replies_enabled ? '1' : '0',
        ]);
    }

    public function render()
    {
        return view('livewire.ranking.edit-ranking');
    }

    public function update()
    {
        $this->form->validate();

        $this->ranking->update([
            'name' => $this->form->name,
            'is_public' => $this->form->is_public === '1' || $this->form->is_public === true,
            'comments_enabled' => $this->form->comments_enabled === '1' || $this->form->comments_enabled === true,
            'comments_replies_enabled' => $this->form->comments_replies_enabled === '1' || $this->form->comments_replies_enabled === true,
        ]);

        $this->js("window.flash({
            title: 'Ranking Updated!',
        })");
    }
}
