<?php

namespace App\Livewire\Profile;

use App\Models\Ranking;
use App\Models\Song;
use App\Models\User;
use Illuminate\Support\Facades\Log;
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
        return view('livewire.profile.profile', [
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

    public function destroy(int $rankingId)
    {
        $ranking = Ranking::findOrFail($rankingId);

        abort_unless(auth()->check() && $ranking->user_id == auth()->id(), 403);

        Ranking::findOrFail($rankingId)->delete();

        Song::where('ranking_id', $rankingId)->delete();

        Log::channel('discord')->info(auth()->user()->name . ' deleted ranking: ' . $ranking->name);

        $this->js("
            window.flash({
                title: 'Ranking Deleted!',
            });
        ");
    }
}
