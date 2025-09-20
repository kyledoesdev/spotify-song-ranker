<div>
    <div class="mt-4 bg-white shadow-md p-4 sm:p-6 rounded-lg">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-6">Settings</h1>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="space-y-3">
                <h5 class="text-lg sm:text-xl font-medium mb-4">Email Preferences</h5>
                <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                    <label class="text-sm sm:text-base">Ranking Reminder Emails</label>
                    <select
                        class="border bg-zinc-100 rounded-lg px-2 py-1 text-sm sm:ml-2"
                        wire:change="updateSetting('recieve_reminder_emails', $event.target.value === 'true')"
                    >
                        <option value="true" {{ auth()->user()->preferences->recieve_reminder_emails ? 'selected' : '' }}>Yes</option>
                        <option value="false" {{ !auth()->user()->preferences->recieve_reminder_emails ? 'selected' : '' }}>No</option>
                    </select>
                </div>
                <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                    <label class="text-sm sm:text-base">Newsletter Emails</label>
                    <select
                        class="border bg-zinc-100 rounded-lg px-2 py-1 text-sm sm:ml-2"
                        wire:change="updateSetting('recieve_newsletter_emails', $event.target.value === 'true')"
                    >
                        <option value="true" {{ auth()->user()->preferences->recieve_newsletter_emails ? 'selected' : '' }}>Yes</option>
                        <option value="false" {{ !auth()->user()->preferences->recieve_newsletter_emails ? 'selected' : '' }}>No</option>
                    </select>
                </div>
            </div>
            
            <div>
                <h5 class="text-lg sm:text-xl font-medium">Other Settings</h5>
                <div class="space-y-3">
                    <button
                        class="btn-primary w-full sm:w-auto text-sm py-2 px-4"
                        onclick="window.confirm({
                            title: 'Begin downloading your data?',
                            message: 'Would you like to download all of your rankings? The export will be queued and emailed to {{ auth()->user()->email }} (your spotify email address) when completed. Please make sure you have access to this email address and the inbox is not full or you won\'t be able to access the rankings.',
                            confirmText: 'Go',
                            componentId: '{{ $this->getId() }}',
                            action: 'download'
                        });"
                    >
                        Export your rankings
                    </button>
                    <button
                        class="btn-danger w-full sm:w-auto text-sm py-2 px-4"
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
        </div>
    </div>
</div>