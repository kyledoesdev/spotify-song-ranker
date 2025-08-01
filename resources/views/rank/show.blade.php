<x-app-layout>
    @if ($ranking->is_ranked)
        <livewire:song-ranking
            :ranking="$ranking"
        />
    @else
        <livewire:song-rank-process
            :ranking="$ranking"
            :sortingState="$sortingState"
        />
    @endif
</x-app-layout>