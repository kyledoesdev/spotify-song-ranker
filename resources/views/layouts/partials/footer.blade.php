<!-- Footer -->
<footer class="bg-gray-800 text-white py-4">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 text-center lg:text-left">
            <!-- Footer Text -->
            <div class="flex items-center justify-center lg:justify-start">
                <p class="text-sm">&copy; {{ now()->format('Y') }} Song-Rank.com by Kyle</p>
            </div>

            <!-- Links -->
            <div class="flex justify-center flex-col items-center lg:flex-row lg:space-x-4">
                <a href="{{ route('about') }}" class="hover:text-gray-400">About</a>
                <a target="_blank" href="https://github.com/kyledoesdev/spotify-song-ranker" class="hover:text-gray-400">Source</a>
                <a target="_blank" href="https://github.com/kyledoesdev/spotify-song-ranker/blob/master/contributing.md" class="hover:text-gray-400">Contributing</a>
                <a target="_blank" href="#" class="hover:text-gray-400">Support</a>
            </div>

            <!-- Social Media -->
            <div class="flex items-center justify-center lg:justify-end space-x-4">
                <a target="_blank" href="https://discord.gg/zXe9kqyFEJ" class="text-gray-400 hover:text-white">Discord</a>
                <a target="_blank" href="https://github.com/kyledoesdev" class="text-gray-400 hover:text-white">Github</a>
                <a target="_blank" href="https://x.com/kyledoesdev" class="text-gray-400 hover:text-white">X</a>
            </div>
        </div>
    </div>
</footer>

<input type="hidden" id="authid" value="{{ auth()->id() }}" />
<input type="hidden" id="authname" value="{{ auth()->user()->name }}" />
<input type="hidden" id="routename" value="{{ get_route() }}" />