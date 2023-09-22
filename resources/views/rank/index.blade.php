@extends('layouts.app')

@section('content')
    <div class="container">
        <allrankingslist 
            :rankings="{{ $rankings }}" 
            :authid="{{ auth()->id() }}"
        />
    </div>
@endsection