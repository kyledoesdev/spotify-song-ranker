@extends('layouts.app')

@section('content')
    @include('layouts.nav')
    
    <livewire:song-rank-process
        :ranking="$ranking"
        :sortingState="$sortingState"
    />
@endsection