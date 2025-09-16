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
        <button
            class="btn-primary"
            onclick="window.confirm({
                title: 'Begin downloading your data?',
                message: 'Would you like to download all of your rankings? The export will be queued and emailed to {{ auth()->user()->email }} (your spotify email address) when completed. Please make sure you have access to this email address and the inbox is not full or you won\'t be able to access the rankings.',
                confirmText: 'Go',
                componentId: '{{ $this->getId() }}',
                action: 'download'
            });"
        >
            Export your data
        </button>
        <button
            class="btn-danger"
            onclick="window.confirm({
                title: 'Delete your account?',
                message: 'Are you sure you want to delete your account? By deleting your account, we will email you one last email with your ranking records & then delete them & your account. Are you sure you want to go?',
                confirmText: 'It\'s time for me to go.',
                entityId: {{ auth()->id() }},
                componentId: '{{ $this->getId() }}',
                action: 'destroy'
            })"
        >
            Delete your account
        </button>
    </div>
</div>