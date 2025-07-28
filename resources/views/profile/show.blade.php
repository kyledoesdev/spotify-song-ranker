<x-guest-layout>
    @include('layouts.nav')

    <profile 
        :user="{{ $user }}" 
        :rankings="{{ $rankings }} "
        name="{{ $name }}"
    />
</x-guest-layout>