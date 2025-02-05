@extends('layouts.app')

@section('content')
    @include('layouts.nav')

    <profile 
        :user="{{ $user }}" 
        :rankings="{{ $rankings }} "
        name="{{ $name }}"
    />
@endsection