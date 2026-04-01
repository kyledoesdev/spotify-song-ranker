@props(['content'])

<section class="py-16 px-4">
    <div class="max-w-6xl mx-auto">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900">{{ $content->firstWhere('slug', 'rank-types-title')->text }}</h2>
            <p class="mt-3 text-lg text-gray-800/60">{{ $content->firstWhere('slug', 'rank-types-subtitle')->text }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-gray-900/30 backdrop-blur-sm rounded-2xl p-8 border border-white/10 shadow-xl flex gap-6 items-start hover:bg-gray-900/40 transition-all">
                <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-purple-500/20 flex items-center justify-center">
                    <i class="fa-solid fa-microphone-lines text-xl text-purple-300"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-white mb-1">{{ $content->firstWhere('slug', 'rank-types-artists-title')->text }}</h3>
                    <p class="text-gray-300/80">{{ $content->firstWhere('slug', 'rank-types-artists-text')->text }}</p>
                </div>
            </div>

            <div class="bg-gray-900/30 backdrop-blur-sm rounded-2xl p-8 border border-white/10 shadow-xl flex gap-6 items-start hover:bg-gray-900/40 transition-all">
                <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-green-500/20 flex items-center justify-center">
                    <i class="fa-solid fa-list-ol text-xl text-green-300"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-white mb-1">{{ $content->firstWhere('slug', 'rank-types-playlists-title')->text }}</h3>
                    <p class="text-gray-300/80">{{ $content->firstWhere('slug', 'rank-types-playlists-text')->text }}</p>
                </div>
            </div>

            <div class="bg-gray-900/30 backdrop-blur-sm rounded-2xl p-8 border border-white/10 shadow-xl flex gap-6 items-start hover:bg-gray-900/40 transition-all">
                <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-blue-500/20 flex items-center justify-center">
                    <i class="fa-solid fa-book-open text-xl text-blue-300"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-white mb-1">{{ $content->firstWhere('slug', 'rank-types-audiobooks-title')->text }}</h3>
                    <p class="text-gray-300/80">{{ $content->firstWhere('slug', 'rank-types-audiobooks-text')->text }}</p>
                </div>
            </div>

            <div class="bg-gray-900/30 backdrop-blur-sm rounded-2xl p-8 border border-white/10 shadow-xl flex gap-6 items-start hover:bg-gray-900/40 transition-all">
                <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-amber-500/20 flex items-center justify-center">
                    <i class="fa-solid fa-podcast text-xl text-amber-300"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-white mb-1">{{ $content->firstWhere('slug', 'rank-types-podcasts-title')->text }}</h3>
                    <p class="text-gray-300/80">{{ $content->firstWhere('slug', 'rank-types-podcasts-text')->text }}</p>
                </div>
            </div>
        </div>
    </div>
</section>
