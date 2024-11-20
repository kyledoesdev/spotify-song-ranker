@extends('layouts.app')

@section('content')
    <div class="mb-2">
        @include('layouts.nav', ['title' => title(false) . ' Feed.'])
    </div>
    <div class="bg-white border border-zinc-800  rounded-lg mt-4">
        <explorer />
    </div>
@endsection