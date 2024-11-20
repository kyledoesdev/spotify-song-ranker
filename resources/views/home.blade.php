@extends('layouts.app')

@section('content')
    <div>
        <div class="mb-4">
            @include('layouts.nav')
        </div>
        <div class="border border-zinc-800 bg-white rounded-lg">
            <spotifysearch></spotifysearch>
        </div>
    </div>
@endsection