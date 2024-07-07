@php
    $small = true;
@endphp

@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header">
            @include('layouts.nav', ['title' => title(false) . ' Feed.'])
        </div>
        <div class="card-body">
            <explorer />
        </div>
    </div>
@endsection