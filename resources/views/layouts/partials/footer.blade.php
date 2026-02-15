<!-- Footer -->
<footer class="bg-gray-800 py-4">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 text-center lg:text-left">
            <!-- Footer Text -->
            <div class="flex items-center justify-center lg:justify-start">
                <span class="text-sm text-white mx-1">&copy; {{ now()->format('Y') }} songrank.dev by
                    <a class="font-bold text-blue-400 mt-1 ml-1" href="https://kyledoes.dev">Kyle</a>
                </span>
            </div>

            <!-- Links -->
            <div class="flex justify-center text-white items-center lg:flex-row space-x-4">
                <a href="{{ route('about') }}" class="hover:text-gray-400">About</a>
                <a href="{{ route('faq') }}" class="hover:text-gray-400">FAQ</a>
                {{-- <a href="{{ route('terms') }}" class="hover:text-gray-400">Terms</a> --}}
            </div>

            <!-- Social Media -->
            <div class="flex items-center text-white justify-center lg:justify-end space-x-4">
                <a target="_blank" href="https://discord.gg/zXe9kqyFEJ" class="text-gray-400 hover:text-white">Discord</a>
                <a target="_blank" href="https://github.com/kyledoesdev" class="text-gray-400 hover:text-white">Github</a>
                <a target="_blank" href="https://bsky.app/profile/kyledoes.dev" class="text-gray-400 hover:text-white">Bluesky</a>
                <a target="_blank" href="https://ko-fi.com/spacelampsix" class="text-gray-400 hover:text-white">Donate</a>
            </div>
        </div>
    </div>
</footer>