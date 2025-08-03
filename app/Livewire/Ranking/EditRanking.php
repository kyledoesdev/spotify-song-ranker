<?php

namespace App\Livewire\Ranking;

use App\Livewire\Forms\RankingForm;
use App\Models\Ranking;
use Livewire\Component;

class EditRanking extends Component
{
    public Ranking $ranking;

    public RankingForm $form;

    public function mount(int $id)
    {
        $this->ranking = Ranking::query()
            ->with('songs')
            ->findOrFail($id);

        if ($this->ranking->user_id !== auth()->id()) {
            abort(403, 'You are not allowed to edit this ranking.');
        }

        $this->form->fill([
            'name' => $this->ranking->name,
            'is_public' => $this->ranking->is_public ? '1' : '0',
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
        ]);

        $this->js("window.flash({
            title: 'Ranking Updated!',
        })");
    }
}
