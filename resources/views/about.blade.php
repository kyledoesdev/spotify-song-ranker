@extends('layouts.app')

@section('content')
    <div class="card" style="min-height: 600px">
        <div class="card-header">
            @include('layouts.nav', ['title' => 'About Song Rank'])
        </div>
        <div class="card-body">
            <about />
        </div>
    </div>
@endsection