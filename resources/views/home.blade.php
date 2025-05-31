@extends('layouts.app')

@section('content')
    <div>
        <div class="mb-4">
            @include('layouts.nav')
        </div>
        <div class="bg-white shadow-md rounded-lg">
            @php $randomArtist = App\Models\Artist::pluck('artist_name')->shuffle()->first(); @endphp

            <spotifysearch :artistplaceholder='"{{ $randomArtist }}"'></spotifysearch>
        </div>
    </div>
@endsection