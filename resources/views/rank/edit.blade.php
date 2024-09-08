@extends('layouts.app')

@section('content')
    @include('layouts.nav')

    <div class="mb-4">
        <editranking :ranking="{{ $ranking }}" />
    </div>
@endsection