<div>
    @if ($this->ranking->shouldShowSupportPopup())
        <div x-data x-init="
            setTimeout(() => {
                window.flashSupport({
                    title: 'Enjoying Song Rank?',
                    message: 'Please consider supporting songrank to keep it free for everyone!'
                });
            }, 3000)
        "></div>
    @endif

    @if ($ranking->is_ranked)
        <div class="pl-4 pr-4 bg-white shadow-lg rounded-lg mt-4">
            <div class="flex justify-between items-center">
                <div>
                    <h5 class="text-base sm:text-lg md:text-xl font-medium">{{ $ranking->name }}</h5>
                </div>
                <div class="flex">
                    @if (prev_route() == 'explore')
                        <a onclick="history.back()" class="btn-secondary p-1 sm:p-2 m-1 sm:m-2">
                            <i class="fa fa-solid fa-arrow-left text-sm sm:text-base"></i>
                        </a>
                    @endif

                    @auth
                        <a href="{{ route('dashboard') }}" class="btn-primary p-1 sm:p-2 m-1 sm:m-2">
                            <i class="fa fa-solid fa-house text-sm sm:text-base"></i>
                        </a>
                    @endauth                    
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
                                        <x-song-ranked-item
                                            :ranking="$ranking"
                                            :song="$song"
                                        />
                                    </li>
                                </div>
                            </div>
                        @endforeach
                    </ol>
                </div>
            </div>

            @if ($ranking->comments_enabled)
                <div class="mx-4 pb-8">
                    @if ($ranking->comments_replies_enabled)
                        <livewire:comments 
                            :model="$ranking"
                            newest-first
                            no-comments-text="No comments yet - you could be the first!"
                        />
                    @else
                        <livewire:comments 
                            :model="$ranking"
                            newest-first
                            no-replies
                            no-comments-text="No comments yet - you could be the first!"
                        />
                    @endif
                </div>
            @endif
        </div>
    @else
        <livewire:song-rank.song-rank-process
            :ranking="$ranking"
            :sortingState="$sortingState"
        />
    @endif
</div>
