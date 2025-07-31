<div>
    <div>
        <div class="flex justify-center">
            <h5 class="text-4xl md:text-6xl lg:text-7xl md:mb-2">Song - Rank</h5>
        </div>
        <div class="flex justify-center">
            <h5 class="text-xs mt-1 md:text-lg">Rank you favorite artists' tracks.</h5>
        </div>
        <div class="flex justify-center mt-1">
            <span>Made with ❤️ by 
                <a class="text-blue-600" href="https://bsky.app/profile/kyledoes.dev">
                    Kyle
                </a>
            </span>
        </div>
    </div>
    
    <div>
        <div class="flex justify-center mt-8">
            <livewire:artist-slideshow 
                :artists="$artists" 
                :speed="2" 
                direction="left" 
            />
        </div>
    </div>
    
    <livewire:welcome-stats 
        :users="$users"
        :rankings="$rankings"
        :artistcount="$artistcount"
    />
    
    <div class="flex justify-center gap-4 mt-4">
        <a class="btn-secondary m-2 p-2" href="/explore">
            <span class="pb-2">Explore</span>
        </a>
        <a class="btn-primary m-2 p-2" href="{{ auth()->check() ? route('home') : route('spotify.login') }}">
            @if(auth()->check())
                <span>View Dashboard</span>
            @else
                <span>Login & Start</span>
            @endif
        </a>
    </div>     
</div>