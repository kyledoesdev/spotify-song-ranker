<x-guest-layout>
    <div class="mb-2">
        @include('layouts.nav', ['title' => 'Explore Rankings'])
    </div>
    <div class="border-zinc-800 rounded-lg mt-4 mb-4">
        <explorer />
    </div>
</x-guest-layout>