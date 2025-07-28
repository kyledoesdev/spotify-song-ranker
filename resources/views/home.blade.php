<x-app-layout>
    <div>
        <div class="mb-4">
            @include('layouts.nav')
        </div>

        @php
            $inProgressRankings = App\Models\Ranking::query()
                ->where('user_id', auth()->id())
                ->where('is_ranked', false)
                ->with(['artist', 'user'])
                ->with('songs', fn ($q) => $q->where('rank', 1))
                ->withCount('songs')
                ->get();

            $randomArtist = App\Models\Artist::pluck('artist_name')->shuffle()->first();
        @endphp

        <dashboard 
            :inprogressrankings="{{ $inProgressRankings }}"
            :artistplaceholder='"{{ $randomArtist }}"'
        >
        </dashboard>
    </div>
</x-app-layout>