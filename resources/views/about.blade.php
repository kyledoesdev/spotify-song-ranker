@extends('layouts.app')

@section('content')
    @include('layouts.nav', ['title' => 'About Song Rank'])

    <div class="mt-4">
        <about />
    </div>
@endsection