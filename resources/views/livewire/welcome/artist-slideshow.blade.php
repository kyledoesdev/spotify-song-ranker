<div class="relative w-full mx-auto py-4 space-y-3"
     x-data="{
        mobileSize: 112,
        desktopSize: 152,
        gap: 12,
        pixelsPerSecond: 50,
        get itemWidth() { return (window.innerWidth >= 768 ? this.desktopSize : this.mobileSize) + this.gap },
        count: {{ count($this->artists) }},
        get totalWidth() { return this.count * this.itemWidth },
        get duration() { return this.totalWidth / this.pixelsPerSecond }
     }"
     x-resize="
        $el.style.setProperty('--slideshow-width', (totalWidth * 2) + 'px');
        $el.style.setProperty('--slideshow-duration', duration + 's');
     "
     x-init="
        $el.style.setProperty('--slideshow-width', (totalWidth * 2) + 'px');
        $el.style.setProperty('--slideshow-duration', duration + 's');
     "
>
    {{-- Top row: slides left --}}
    <div class="relative overflow-hidden">
        <div class="flex gap-3"
             style="animation: artistSlideLeft var(--slideshow-duration) linear infinite; width: var(--slideshow-width); will-change: transform;">
            @foreach($this->artists as $artist)
                <div class="flex-shrink-0 w-[100px] md:w-[140px]">
                    <img
                        src="{{ $artist['artist_img'] }}"
                        alt="{{ $artist['artist_name'] }}"
                        class="w-[100px] h-[100px] md:w-[140px] md:h-[140px] object-cover rounded-xl shadow-md"
                    >
                </div>
            @endforeach
            @foreach($this->artists as $artist)
                <div class="flex-shrink-0 w-[100px] md:w-[140px]">
                    <img
                        src="{{ $artist['artist_img'] }}"
                        alt="{{ $artist['artist_name'] }}"
                        class="w-[100px] h-[100px] md:w-[140px] md:h-[140px] object-cover rounded-xl shadow-md"
                    >
                </div>
            @endforeach
        </div>
        <div class="absolute inset-y-0 left-0 w-16 bg-gradient-to-r from-[rgba(207,120,224,0.8)] to-transparent pointer-events-none"></div>
        <div class="absolute inset-y-0 right-0 w-16 bg-gradient-to-l from-[rgba(100,249,179,0.8)] to-transparent pointer-events-none"></div>
    </div>

    {{-- Bottom row: slides right (reversed collection) --}}
    <div class="relative overflow-hidden">
        <div class="flex gap-3"
             style="animation: artistSlideRight var(--slideshow-duration) linear infinite; width: var(--slideshow-width); will-change: transform;">
            @foreach($this->artists->reverse() as $artist)
                <div class="flex-shrink-0 w-[100px] md:w-[140px]">
                    <img
                        src="{{ $artist['artist_img'] }}"
                        alt="{{ $artist['artist_name'] }}"
                        class="w-[100px] h-[100px] md:w-[140px] md:h-[140px] object-cover rounded-xl shadow-md"
                    >
                </div>
            @endforeach
            @foreach($this->artists->reverse() as $artist)
                <div class="flex-shrink-0 w-[100px] md:w-[140px]">
                    <img
                        src="{{ $artist['artist_img'] }}"
                        alt="{{ $artist['artist_name'] }}"
                        class="w-[100px] h-[100px] md:w-[140px] md:h-[140px] object-cover rounded-xl shadow-md"
                    >
                </div>
            @endforeach
        </div>
        <div class="absolute inset-y-0 left-0 w-16 bg-gradient-to-r from-[rgba(207,120,224,0.8)] to-transparent pointer-events-none"></div>
        <div class="absolute inset-y-0 right-0 w-16 bg-gradient-to-l from-[rgba(100,249,179,0.8)] to-transparent pointer-events-none"></div>
    </div>
</div>
