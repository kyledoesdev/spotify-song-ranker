<div class="mt-4">
    <x-profile-card :user="$user" />

    <div class="{{ $this->rankings->count() > 1 ? 'grid grid-cols-1 md:grid-cols-2 gap-4 overflow-x-auto pt-2' : 'grid grid-cols-1 gap-4 overflow-x-auto pt-2' }}">
        @forelse ($this->rankings as $ranking)
            <livewire:ranking.card
                :key="'ranking-card-'.$ranking->getKey()"
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