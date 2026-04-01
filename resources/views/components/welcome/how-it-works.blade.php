@props(['content'])

<section class="py-16 px-4">
    <div class="max-w-6xl mx-auto">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900">{{ $content->firstWhere('slug', 'how-it-works-title')->text }}</h2>
            <p class="mt-3 text-lg text-gray-800/60">{{ $content->firstWhere('slug', 'how-it-works-subtitle')->text }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white/15 backdrop-blur-sm rounded-2xl p-8 border border-white/20 shadow-lg text-center group hover:bg-white/25 transition-all">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-purple-400/20 flex items-center justify-center">
                    <i class="fa-solid fa-magnifying-glass text-2xl text-purple-600"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $content->firstWhere('slug', 'how-it-works-step-1-title')->text }}</h3>
                <p class="text-gray-800/60">{{ $content->firstWhere('slug', 'how-it-works-step-1-text')->text }}</p>
            </div>

            <div class="bg-white/15 backdrop-blur-sm rounded-2xl p-8 border border-white/20 shadow-lg text-center group hover:bg-white/25 transition-all">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-green-400/20 flex items-center justify-center">
                    <i class="fa-solid fa-code-compare text-2xl text-green-600"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $content->firstWhere('slug', 'how-it-works-step-2-title')->text }}</h3>
                <p class="text-gray-800/60">{{ $content->firstWhere('slug', 'how-it-works-step-2-text')->text }}</p>
            </div>

            <div class="bg-white/15 backdrop-blur-sm rounded-2xl p-8 border border-white/20 shadow-lg text-center group hover:bg-white/25 transition-all">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-blue-400/20 flex items-center justify-center">
                    <i class="fa-solid fa-trophy text-2xl text-blue-600"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $content->firstWhere('slug', 'how-it-works-step-3-title')->text }}</h3>
                <p class="text-gray-800/60">{{ $content->firstWhere('slug', 'how-it-works-step-3-text')->text }}</p>
            </div>
        </div>
    </div>
</section>
