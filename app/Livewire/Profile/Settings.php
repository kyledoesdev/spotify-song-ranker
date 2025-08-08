<?php

namespace App\Livewire\Profile;

use App\Jobs\DeleteUserJob;
use App\Models\Ranking;
use App\Notifications\DownloadDataNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Livewire\Component;

class Settings extends Component
{
    public function render()
    {
        return view('livewire.profile.settings');
    }

    public function updateEmailPreference($receiveEmails)
    {
        auth()->user()->preferences()->update([
            'recieve_reminder_emails' => $receiveEmails,
        ]);

        $this->js("
            window.flash({
                title: 'Preferences Updated!',
                message: 'Your email preferences have been saved.',
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

        dispatch(fn () => Notification::send(auth()->user(), new DownloadDataNotification($rankings)));

        $this->js("
            window.flash({
                title: 'Download Started!',
                message: 'Your data download has started and will be emailed to you when it is complete.',
                icon: 'success'
            });
        ");
    }
}
