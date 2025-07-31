<div class="grid grid-cols-1 md:grid-cols-2 mt-4 bg-white shadow-md p-4 rounded-lg">
    <div>
        <h5 class="md:text-xl mb-4">Email Preferences</h5>
        <label>Ranking Reminder Emails</label>
        <select
            class="border bg-zinc-100 rounded-lg p-1 ml-2"
            wire:change="updateEmailPreference($event.target.value === 'true')"
        >
            <option value="true" {{ auth()->user()->preferences->recieve_reminder_emails ? 'selected' : '' }}>Yes</option>
            <option value="false" {{ !auth()->user()->preferences->recieve_reminder_emails ? 'selected' : '' }}>No</option>
        </select>
    </div>
    <div>
        <h5 class="md:text-xl">Other Settings</h5>
        <button class="btn-primary" wire:click="confirmDownload">Export your data</button>
        <button class="btn-danger" wire:click="confirmDestroy">Delete your account</button>
    </div>
</div>