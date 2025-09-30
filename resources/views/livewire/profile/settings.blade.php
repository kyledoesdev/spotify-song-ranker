<div>
    <div class="mt-2 bg-white shadow-lg p-4 md:p-6 rounded-xl">
        <div class="mb-4">
            <h5 class="font-bold mb-2">Settings</h5>
            <p class="text-xs">Customize your experience and manage your account</p>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Email Preferences Section -->
            <div class="bg-white rounded-xl p-6 shadow-md transition-all duration-300">
                <div class="flex items-center mb-6">
                    <h5 class="font-semibold">Email Preferences</h5>
                </div>
                
                <div class="space-y-6">
                    <div class="group">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Ranking Reminder Emails</label>
                        <div class="relative">
                            <select
                                class="w-full appearance-none bg-slate-50 border border-slate-200 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-purple-400 focus:border-transparent transition-all duration-200"
                                wire:change="updateSetting('recieve_reminder_emails', $event.target.value === 'true')"
                            >
                                <option value="true" {{ auth()->user()->preferences->recieve_reminder_emails ? 'selected' : '' }}>Yes, keep me reminded</option>
                                <option value="false" {{ !auth()->user()->preferences->recieve_reminder_emails ? 'selected' : '' }}>No, I'll remember myself</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                                <i class="fa fa-chevron-down text-slate-400 text-sm"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="group">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Newsletter Emails</label>
                        <div class="relative">
                            <select
                                class="w-full appearance-none bg-slate-50 border border-slate-200 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-purple-400 focus:border-transparent transition-all duration-200 group-hover:border-slate-300"
                                wire:change="updateSetting('recieve_newsletter_emails', $event.target.value === 'true')"
                            >
                                <option value="true" {{ auth()->user()->preferences->recieve_newsletter_emails ? 'selected' : '' }}>Yes, send me updates</option>
                                <option value="false" {{ !auth()->user()->preferences->recieve_newsletter_emails ? 'selected' : '' }}>No newsletters please</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                                <i class="fa fa-chevron-down text-slate-400 text-sm"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Account Actions Section -->
            <div class="bg-white rounded-xl p-6 shadow-md border border-slate-100 hover:shadow-lg transition-all duration-300">
                <div class="flex items-center mb-6">
                    <h5 class="font-semibold">Account Actions</h5>
                </div>
                
                <div class="space-y-4">
                    <div class="group">
                        <button
                            class="btn-primary w-full text-sm py-4 px-6 flex items-center justify-center space-x-2"
                            onclick="window.confirm({
                                title: 'Begin downloading your data?',
                                message: 'Would you like to download all of your rankings? The export will be queued and emailed to {{ auth()->user()->email }} (your spotify email address) when completed. Please make sure you have access to this email address and the inbox is not full or you won\'t be able to access the rankings.',
                                confirmText: 'Go',
                                componentId: '{{ $this->getId() }}',
                                action: 'download'
                            });"
                        >
                            <i class="fa fa-download"></i>
                            <span>Export Your Rankings</span>
                        </button>
                        <p class="text-xs text-slate-500 mt-2 text-center">Download all your data as a backup</p>
                    </div>
                    
                    <div class="pt-4 border-t border-slate-100">
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                            <div class="flex items-center mb-2">
                                <i class="fa fa-exclamation-triangle text-red-500 mr-2"></i>
                                <span class="text-sm font-medium text-red-800">Danger Zone</span>
                            </div>
                            <p class="text-xs text-red-600">This action cannot be undone. Your account and all rankings will be deleted.</p>
                        </div>
                        
                        <button
                            class="btn-danger w-full text-sm py-4 px-6 flex items-center justify-center space-x-2"
                            onclick="window.confirm({
                                title: 'Delete your account?',
                                message: 'Are you sure you want to delete your account? By deleting your account, we will email you one last email with your ranking records & then delete them & your account. Are you sure you want to go?',
                                confirmText: 'It\'s time for me to go.',
                                entityId: {{ auth()->id() }},
                                componentId: '{{ $this->getId() }}',
                                action: 'destroy'
                            })"
                        >
                            <i class="fa fa-trash-alt"></i>
                            <span>Delete Account</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer with additional info -->
        <div class="mt-8 pt-6 border-t border-slate-200">
            
        </div>
    </div>
</div>