<?php

namespace App\Livewire;

use App\Models\Ranking;
use App\Models\Song;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Profile extends Component
{
    public User $user;

    public function mount(string $id)
    {
        $this->user = User::where('spotify_id', $id)->firstOrFail();
    }

    public function render()
    {
        return view('livewire.profile', [
            'name' => get_formatted_name($this->user->name),
        ]);
    }

    #[Computed]
    public function rankings()
    {
        return Ranking::query()
            ->forProfilePage($this->user)
            ->get();
    }

    public function confirmDestroy($rankingId)
    {
        $this->js("
            window.confirm({
                title: 'Delete Ranking?',
                message: 'Are you sure you want to delete this ranking?',
                confirmText: 'Delete',
                entityId: {$rankingId},
                componentId: '{$this->getId()}',
            });
        ");
    }

    public function destroy(int $rankingId)
    {
        abort_unless(auth()->check() && Ranking::findOrFail($rankingId)->user_id == auth()->id(), 403);

        Ranking::findOrFail($rankingId)->delete();

        Song::where('ranking_id', $rankingId)->delete();

        $this->js("
            window.flash({
                title: 'Ranking Deleted!',
            });
        ");
    }
}
