<?php

namespace App\Livewire;

use App\Jobs\DeleteUserJob;
use App\Models\Ranking;
use App\Models\User;
use App\Notifications\DownloadDataNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Livewire\Component;

class Settings extends Component
{
    public function render()
    {
        return view('livewire.settings');
    }

    public function updateEmailPreference($receiveEmails)
    {        
        auth()->user()->preferences()->update([
            'recieve_reminder_emails' => $receiveEmails
        ]);

        $this->js("
            window.flash({
                title: 'Preferences Updated!',
                message: 'Your email preferences have been saved.',
                icon: 'success'
            });
        ");
    }

    public function confirmDestroy()
    {
        $this->js("
            window.confirm({
                title: 'Delete your account?',
                message: 'Are you sure you want to delete your account? By deleting your account, we will email you one last email with your ranking records & then delete them & your account. Are you sure you want to go?',
                confirmText: 'It's time for me to go.',
                entityId: " . auth()->id() . ",
                action: 'destroy'
            });
        ");
    }

    public function destroy($userId)
    {
        // Security check
        abort_unless(auth()->check() && $userId == auth()->id(), 403);

        $user = auth()->user();

        // Log the account deletion
        Log::channel('discord')->emergency("{$user->email} is deleting their account... ID: {$userId}");

        // Log them out
        Auth::logout();

        // Queue deletion
        DeleteUserJob::dispatch($user);

        // Flash message and redirect
        session()->flash('success', "We're sorry to see you go {$user->name}. Be Well.");

        $this->redirect('/');
    }

    public function confirmDownload()
    {
        $this->js("
            window.confirm({
                title: 'Begin downloading your data?',
                message: 'Would you like to download all of your rankings? The export will be queued and emailed to you when completed.',
                confirmText: 'Go',
                action: 'download'
            });
        ");
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