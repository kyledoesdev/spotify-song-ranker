<?php

namespace App\Livewire\Profile;

use App\Models\Ranking;
use App\Models\User;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

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
    #[On('rankings-updated')]
    public function rankings()
    {
        return Ranking::query()
            ->forProfilePage($this->user)
            ->get();
    }
}
