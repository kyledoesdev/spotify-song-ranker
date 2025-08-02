<div>
    @if ($ranking->is_ranked)
        <div class="pl-4 pr-4 bg-white shadow-lg rounded-lg mt-4">
            <div class="flex justify-between items-center">
                <div>
                    <h5 class="text-base sm:text-lg md:text-xl font-medium">{{ $ranking->name }}</h5>
                </div>
                <div class="flex">
                    <a href="{{ auth()->check() ? route('home') : route('explore.index') }}" class="btn-primary p-1 sm:p-2 m-1 sm:m-2">
                        <i class="fa fa-solid {{ auth()->check() ? 'fa-house' : 'fa-magnifying-glass' }} text-sm sm:text-base"></i>
                    </a>
                    <share />
                </div>
            </div>

            <hr>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8 px-2 md:px-4 overflow-x-hidden">
                <div class="md:col-span-2 w-full m-2" style="max-height: 600px; overflow-y: auto;">
                    <ol>
                        @foreach ($ranking->songs->sortBy('rank') as $song)
                            <div class="flex">
                                <div class="p-2 md:p-4 mt-4">{{ $song->rank }}.</div>
                                <div class="flex-1">
                                    <li>
                                        <x-song-ranked-item :song="$song" />
                                    </li>
                                </div>
                            </div>
                        @endforeach
                    </ol>
                </div>
            </div>
        </div>
    @else
        <livewire:song-rank-process
            :ranking="$ranking"
            :sortingState="$sortingState"
        />
    @endif
</div>
