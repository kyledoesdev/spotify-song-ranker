<x-app-layout>
    @include('layouts.nav')

    <div class="mb-4">
        <editranking :ranking="{{ $ranking }}" />
    </div>
</x-app-layout>