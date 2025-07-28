<x-app-layout>
    <div>
        @include('layouts.nav', ['title' => 'Settings & Preferences'])
    </div>
    <div>
        <settings :preferences="{{ auth()->user()->preferences }}" />
    </div>
</x-app-layout>