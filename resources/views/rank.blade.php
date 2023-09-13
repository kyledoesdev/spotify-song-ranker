@extends('layouts.app')

@section('content')
    <div class="container">
        <rank-maker 
            :rankingid="{{ $ranking->id }}"
            :ranksongs="{{ $songs }}"
            :isranked="{{ $ranking->is_ranked }}"
            rankname="{{ $ranking->name }}"
        >
        </rank-maker>
    </div>
@endsection