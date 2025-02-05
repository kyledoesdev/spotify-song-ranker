@extends('layouts.app')

@section('content')
    <div class="mb-2">
        @include('layouts.nav', ['title' => 'Explore Rankings'])
    </div>
    <div class="border-zinc-800 rounded-lg mt-4 mb-4">
        <explorer />
    </div>
@endsection