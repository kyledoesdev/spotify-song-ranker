@extends('layouts.app')

@section('content')
    @php
        $artists = App\Models\Artist::query()->topArtists()->get();
        $artistCount = round($artists->count() / 25) * 25;
        $users = round(App\Models\User::count() / 50) * 50;
        $rankings = round(App\Models\Ranking::where('is_ranked', true)->count() / 25) * 25;
    @endphp

    <div>
        <welcome 
            :artists="{{ $artists }}"
            :artistcount="{{ $artistCount }}"
            :users="{{ $users }}"
            :rankings="{{ $rankings }}"
        />
    </div>
@endsection
