<?php

namespace App\Livewire\Profile;

use App\Exports\SongExport;
use App\Models\Ranking;
use App\Models\Song;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class Profile extends Component
{
    public User $user;

    public function mount(string $id)
    {
        $this->user = User::where('spotify_id', $id)->firstOrFail();

        session()->put(['profile_name' => $this->user->name]);
    }

    public function render()
    {
        return view('livewire.profile.profile', [
            'name' => Str::endsWith($this->user->name, 's') ? Str::finish($this->user->name, "'") : $this->user->name."'s",
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

        Log::channel('discord_ranking_updates')->info(auth()->user()->name.' deleted ranking: '.$ranking->name);

        $this->js("
            window.flash({
                title: 'Ranking Deleted!',
            });
        ");
    }

    public function download(int $rankingId): BinaryFileResponse
    {
        $ranking = Ranking::findOrFail($rankingId);

        abort_unless(auth()->check() && $ranking->user_id == auth()->id(), 403);

        return Excel::download(new SongExport($ranking->songs, $ranking->name), $ranking->name . '.csv', \Maatwebsite\Excel\Excel::CSV);
    }
}
