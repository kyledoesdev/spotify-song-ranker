@extends('layouts.app')

@section('content')
    <div class="card" style="min-height: 600px">
        <div class="card-header">
            @include('layouts.nav')
        </div>
        <div class="card-body">
            <settings :preferences="{{ auth()->user()->preferences }}" />
        </div>
    </div>
@endsection