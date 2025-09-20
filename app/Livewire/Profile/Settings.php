<?php

namespace App\Livewire\Profile;

use App\Jobs\DeleteUserJob;
use App\Models\Ranking;
use App\Notifications\DownloadDataNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class Settings extends Component
{
    public function render()
    {
        return view('livewire.profile.settings');
    }

    public function updateSetting(string $name, mixed $value)
    {
        auth()->user()->preferences()->update([
            $name => $value,
        ]);

        $this->js("
            window.flash({
                title: 'Settings Updated!',
                message: 'Your settings have been saved.',
                icon: 'success'
            });
        ");
    }

    public function destroy($userId)
    {
        // Security check
        abort_unless(auth()->check() && $userId == auth()->id(), 403);

        $user = auth()->user();

        // Log the account deletion
        Log::channel('discord_user_updates')->emergency("{$user->email} is deleting their account... ID: {$userId}");

        // Log them out
        Auth::logout();

        /* kill session */
        Session::invalidate();
        Session::regenerateToken();

        // Queue deletion
        DeleteUserJob::dispatch($user);

        // Flash message and redirect
        session()->flash('success', "We're sorry to see you go {$user->name}. Be Well.");

        $this->redirect('/');
    }

    public function download()
    {
        $rankings = Ranking::query()
            ->where('user_id', auth()->id())
            ->with('songs', 'artist')
            ->get();

        $user = auth()->user();

        dispatch(fn () => Notification::send($user, new DownloadDataNotification($rankings)));

        $this->js("
            window.flash({
                title: 'Download Started!',
                message: 'Your data download has started and will be emailed to you when it is complete.',
                icon: 'success'
            });
        ");
    }
}
