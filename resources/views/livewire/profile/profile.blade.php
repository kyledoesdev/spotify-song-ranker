<div class="mt-4">
    <div class="bg-white rounded-lg shadow-md mb-4">
        <div class="flex items-center space-x-6 mx-2 py-1">
            <div class="flex-shrink-0">
                <div class="relative">
                    <img 
                        class="w-20 h-20 rounded-full object-cover border-4 border-dark ml-1" 
                        src="{{ $user->avatar }}" 
                        alt="{{ $user->name }}"
                    >
                </div>
            </div>
            
            <div class="flex-1 min-w-0">
                <div class="mb-1">
                    <h2 class="text-xl font-bold text-gray-800 mt-4">{{ $user->name }}</h2>
                </div>
                
                <div class="flex space-x-4 overflow-x-auto pb-2">
                    <div class="flex items-center space-x-2 py-2 px-3 bg-gray-50 rounded-lg flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-700">Joined</p>
                            <p class="text-xs text-gray-500">{{ $user->created_at->format('M Y') }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-2 py-2 px-3 bg-gray-50 rounded-lg flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-700">Rankings</p>
                            <p class="text-xs text-gray-500">{{ number_format($user->rankings->where('is_ranked', true)->count()) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="{{ $this->rankings->count() > 1 ? 'grid grid-cols-1 md:grid-cols-2 gap-4 overflow-x-auto pt-2' : 'grid grid-cols-1 gap-4 overflow-x-auto pt-2' }}">
        @forelse ($this->rankings as $ranking)
            <x-ranking-card
                :key="$ranking->getKey()"
                :ranking="$ranking"
            />
        @empty
            <div class="col-span-full text-center py-8 text-gray-500">
                <p class="text-lg">No rankings found.</p>
                <p class="text-sm mt-2">Create your first ranking to get started!</p>
            </div>
        @endforelse
    </div>
</div>