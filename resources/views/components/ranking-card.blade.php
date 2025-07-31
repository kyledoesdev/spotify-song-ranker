
<div 
    class="bg-white shadow-md rounded-lg cursor-pointer hover:shadow-lg transition-shadow relative p-2"
    wire:key="{{ $ranking->getKey() }}"
>
    <div class="m-2" style="min-width: 35vw;" onclick="window.location.href='{{ route('rank.show', ['id' => $ranking->getKey() ]) }}'">
        <div class="flex space-x-6">
            <div>
                <img 
                    src="{{ $ranking->artist->artist_img }}"
                    class="rounded-lg border border-zinc-800 mb-2"
                    width="120" 
                    height="120" 
                    alt="{{ $ranking->user->name }}"
                >
                <div class="relative z-10">
                    <x-spotify-logo :artist="$ranking->artist->artist_id" />
                </div>
            </div>
            <div>
                <h5 class="mb-1" title="{{ $ranking->name }}">
                    <span class="text-base md:text-lg">{{ Str::limit($ranking->name, 30) }}</span>
                </h5>
                
                {{-- Artist Name --}}
                <div class="flex">
                    <div class="mr-2">
                        <i class="fa fa-solid fa-music text-xs md:text-base"></i>
                    </div>
                    <div class="p-0">
                        <span class="text-xs md:text-base">{{ $ranking->artist->artist_name }}</span>
                    </div>
                </div>
                
                {{-- Top Song --}}
                <div class="flex">
                    <div class="mr-1">
                        <i class="fa fa-regular fa-star text-xs md:text-base"></i>
                    </div>
                    <div class="p-0">
                        @if($ranking->is_ranked)
                            <span class="text-xs md:text-base">{{ Str::limit($ranking->songs[0]->title ?? '', 25) }}</span>
                        @else
                            N/A
                        @endif
                    </div>
                </div>
                
                {{-- Song Count --}}
                <div class="flex">
                    <div class="mr-1">
                        <i class="fa fa-solid fa-hashtag text-xs md:text-base"></i>
                    </div>
                    <div class="p-0">
                        <span class="text-xs md:text-base">{{ $ranking->songs_count }} songs ranked.</span>
                    </div>
                </div>
                
                {{-- Ranking Creator (only on explore.index) --}}
                @if(request()->routeIs('explore.index'))
                    <div class="flex">
                        <div class="mr-1">
                            <i class="fa fa-regular fa-user text-xs md:text-base"></i>
                        </div>
                        <div class="p-0 relative z-10">
                            <a href="/profile/{{ $ranking->user->spotify_id }}" class="inline-block">
                                <span class="text-xs md:text-base">{{ $ranking->user->name }} 
                                    <i class="fa fa-arrow-up-right-from-square mx-1 text-blue-500 text-xs md:text-base"></i>
                                </span>
                            </a>
                        </div>
                    </div>
                @endif
                
                {{-- Ranking Completed At --}}
                <div class="flex">
                    <div class="mr-2">
                        <i class="fa fa-regular fa-clock text-xs md:text-base"></i>
                    </div>
                    <div class="p-0" title="{{ $ranking->formatted_completed_at }}">
                        <span class="text-xs md:text-base">{{ $ranking->completed_at }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (auth()->id() === $ranking->user_id && get_route() === 'profile')
        <div class="absolute top-2 right-2 flex space-x-1">
            <a 
                class="text-gray-500 hover:text-green-600 transition-colors p-1 text-sm"
                href="{{ route('rank.edit', ['id' => $ranking->getKey() ]) }}"
                title="Edit"
                onclick="event.stopPropagation()"
            >
                <i class="fa fa-pencil"></i>
            </a>
            
            <button 
                class="text-gray-500 hover:text-red-600 transition-colors p-1 text-sm"
                wire:click="confirmDestroy({{ $ranking->getKey() }})"
                title="Delete"
                onclick="event.stopPropagation()"
            >
                <i class="fa fa-trash"></i>
            </button>
        </div>
    @endif
</div>