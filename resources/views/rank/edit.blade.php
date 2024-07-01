@extends('layouts.app')

@section('content')
    <div class="container">
        <editranking  :ranking="{{ $ranking }}" />
    </div>
@endsection