<x-guest-layout>
    @include('layouts.nav')
    
    <livewire:song-rank-process
        :ranking="$ranking"
        :sortingState="$sortingState"
    />
</x-guest-layout>