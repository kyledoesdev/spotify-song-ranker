@extends('layouts.app')

@section('content')
    <div>
        @include('layouts.nav', ['title' => 'Settings & Preferences'])
    </div>
    <div>
        <settings :preferences="{{ auth()->user()->preferences }}" />
    </div>
@endsection