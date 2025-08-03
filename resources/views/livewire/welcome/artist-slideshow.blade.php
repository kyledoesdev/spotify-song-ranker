<div class="overflow-hidden relative w-full h-auto mx-auto p-4">    
    <div class="w-full overflow-hidden">
        <div class="flex" 
             style="animation: artistSlideLeft 60s linear infinite; width: {{ count($this->artists) * 144 }}px; will-change: transform;">
            @foreach($this->artists as $index => $artist)
                <div class="flex-shrink-0 px-2" style="width: 144px;">
                    <img 
                        src="{{ $artist['artist_img'] }}" 
                        alt="{{ $artist['artist_name'] }}"
                        class="w-32 h-32 object-cover rounded-lg transform rotate-3 transition-transform duration-300 shadow-lg"
                    >
                </div>
            @endforeach
        </div>
    </div>
</div>