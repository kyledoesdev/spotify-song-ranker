<section class="py-20 px-4">
    <div class="max-w-3xl mx-auto text-center">
        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Ready to Rank?</h2>
        <p class="text-lg text-gray-800/60 mb-8">Join the community and create your first ranking in minutes.</p>
        {{-- <p class="text-sm text-gray-800/60 mb-8">
            <i class="fa-solid fa-gift mr-1"></i>
            100% free, forever.
            <a href="{{ route('faq') }}" class="underline font-medium hover:text-gray-900 transition-colors">Find out why &rarr;</a>
        </p> --}}
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ auth()->check() ? route('dashboard') : route('spotify.login') }}"
               class="btn-animated inline-flex items-center justify-center gap-2 px-10 py-4 text-lg font-bold text-gray-900 rounded-xl shadow-lg hover:shadow-xl transition-shadow cursor-pointer">
                <i class="fa-solid fa-play"></i>
                @auth
                    Go to Dashboard
                @else
                    Get Started Free
                @endauth
            </a>
            <a href="{{ route('explore') }}"
               class="inline-flex items-center justify-center gap-2 px-10 py-4 text-lg font-semibold rounded-xl bg-white/20 backdrop-blur-sm border border-white/30 text-gray-900 hover:bg-white/30 transition-all cursor-pointer">
                <i class="fa-solid fa-compass"></i>
                Browse Rankings
            </a>
        </div>
    </div>
</section>
