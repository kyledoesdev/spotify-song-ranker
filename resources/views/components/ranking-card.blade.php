@props(['ranking'])

<div class="bg-white shadow-md rounded-lg cursor-pointer hover:shadow-lg transition-shadow relative p-2">
    <div class="m-2" style="min-width: 35vw;" onclick="window.location.href='{{ route('ranking', ['id' => $ranking->getKey() ]) }}'">
        <div class="flex space-x-6">
            <div>
                <img 
                    src="{{ $ranking->isPlaylistType() ? $ranking->playlist->cover : $ranking->artist->artist_img }}"
                    class="rounded-lg border border-zinc-800 mb-2"
                    width="120" 
                    height="120" 
                    alt="{{ $ranking->user->name }}"
                >
                <div class="relative z-10">
                    @if ($ranking->isPlaylistType())
                        <x-spotify-logo :playlist="$ranking->playlist->playlist_id" />
                    @else
                        <x-spotify-logo :artist="$ranking->artist->artist_id" />    
                    @endif
                </div>
            </div>
            <div class="space-y-1">
                <h5 class="mb-2" title="{{ $ranking->name }}">
                    <span class="text-base md:text-lg font-bold k-line">{{ Str::limit($ranking->name, 30) }}</span>
                </h5>
                
                <div class="flex">
                    <div class="mr-2">
                        <i class="fa fa-solid fa-music text-xs md:text-base"></i>
                    </div>
                    <div class="p-0">
                        <span class="text-xs md:text-base">
                            {{ $ranking->isPlaylistType() ? $ranking->playlist->name : $ranking->artist->artist_name}}
                        </span>
                    </div>
                </div> 
                
                {{-- Top Song --}}
                <div class="flex">
                    <div class="mr-1">
                        <i class="fa fa-regular fa-star text-xs md:text-base"></i>
                    </div>
                    <div class="p-0">
                        @if ($ranking->is_ranked)
                            <span class="text-xs md:text-base">{{ Str::limit($ranking->songs->first()?->title, 25) }}</span>
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
                        <span class="text-xs md:text-base">{{ $ranking->songs_count }} {{ $ranking->has_podcast_episode == 1 ? 'records' : 'songs' }} {{ $ranking->is_ranked ? ' ranked.' : ' in process.' }}</span>
                    </div>
                </div>
                
                @if (Route::currentRouteName() != 'profile')
                    <div class="flex">
                        <div class="mr-1">
                            <i class="fa fa-regular fa-user text-xs md:text-base"></i>
                        </div>
                        <div class="p-0 relative z-10">
                            <a href="{{ route('profile', ['id' => $ranking->user->spotify_id]) }}" class="inline-block">
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
</div>