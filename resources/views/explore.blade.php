@extends('layouts.app')

@section('content')
    <div class="card" style="min-height: 600px">
        <div class="card-header">
            @include('layouts.nav', ['welcome' => false, 'title' => $title])
        </div>
        <div class="card-body">
            <explorer />
        </div>
    </div>
@endsection