<div class="max-w-6xl mx-auto">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white/15 backdrop-blur-sm rounded-2xl p-6 border border-white/20 shadow-lg text-center">
            <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-purple-400/20 flex items-center justify-center">
                <i class="fa-solid fa-users text-xl text-purple-600"></i>
            </div>
            <p class="text-3xl md:text-4xl font-bold text-gray-900">{{ number_format($users) }}+</p>
            <p class="text-sm text-gray-800/60 mt-1 font-medium">Users Worldwide</p>
        </div>

        <div class="bg-white/15 backdrop-blur-sm rounded-2xl p-6 border border-white/20 shadow-lg text-center">
            <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-green-400/20 flex items-center justify-center">
                <i class="fa-solid fa-ranking-star text-xl text-green-600"></i>
            </div>
            <p class="text-3xl md:text-4xl font-bold text-gray-900">{{ number_format($rankings) }}+</p>
            <p class="text-sm text-gray-800/60 mt-1 font-medium">Rankings Created</p>
        </div>

        <div class="bg-white/15 backdrop-blur-sm rounded-2xl p-6 border border-white/20 shadow-lg text-center">
            <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-blue-400/20 flex items-center justify-center">
                <i class="fa-solid fa-guitar text-xl text-blue-600"></i>
            </div>
            <p class="text-3xl md:text-4xl font-bold text-gray-900">{{ number_format($artists) }}+</p>
            <p class="text-sm text-gray-800/60 mt-1 font-medium">Artists Ranked</p>
        </div>

        <div class="bg-white/15 backdrop-blur-sm rounded-2xl p-6 border border-white/20 shadow-lg text-center">
            <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-amber-400/20 flex items-center justify-center">
                <i class="fa-solid fa-headphones text-xl text-amber-600"></i>
            </div>
            <p class="text-3xl md:text-4xl font-bold text-gray-900">{{ number_format($playlists) }}+</p>
            <p class="text-sm text-gray-800/60 mt-1 font-medium">Playlists Ranked</p>
        </div>
    </div>
</div>
