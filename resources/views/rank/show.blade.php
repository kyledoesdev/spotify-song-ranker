@extends('layouts.app')

@section('content')
    @include('layouts.nav')
    
    <livewire:song-rank-process :rankingId="$rankingId" />
@endsection