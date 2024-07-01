@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header">
            @include('layouts.nav', ['title' => $title])
        </div>
        <div class="card-body">
            <explorer />
        </div>
    </div>
@endsection