@extends('layouts.app')

@section('content')
    <div>
        @include('layouts.nav', ['title' => title(false) . ' Feed.'])
    </div>
    <div class="bg-white rounded-lg mt-4">
        <explorer />
    </div>
@endsection