<?php

namespace App\Livewire\Ranking;

use App\Actions\Rankings\DestroyRanking;
use App\Exports\SongExport;
use App\Models\Ranking;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class Card extends Component
{
    public Ranking $ranking;

    public function render()
    {
        return view('livewire.ranking.card', ['ranking' => $this->ranking]);
    }

    public function destroy()
    {
        abort_unless(auth()->check() && $this->ranking->user_id == auth()->id(), 403);

        (new DestroyRanking)->handle(auth()->user(), $this->ranking);

        $this->dispatch('rankings-updated');

        $this->js("
            window.flash({
                title: 'Ranking Deleted!',
            });
        ");
    }

    public function download(): BinaryFileResponse
    {
        abort_unless(auth()->check() && $this->ranking->user_id == auth()->id(), 403);

        /* yeah, this isn't what you should do here but ima do it anyway */
        $this->skipRender();

        return Excel::download(new SongExport($this->ranking->songs, $this->ranking->name), safeName($this->ranking->name).'.csv', \Maatwebsite\Excel\Excel::CSV);
    }
}
