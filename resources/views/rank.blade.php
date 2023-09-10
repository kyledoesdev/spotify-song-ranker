@extends('layouts.app')

@section('content')
    <div class="container-xl">
        <rank-maker 
            :rankingid="{{ $rankingId }}"
            :ranksongs="{{ $songs }}"
            :isranked="{{ $isRanked }}"
        >
        </rank-maker>
    </div>
@endsection