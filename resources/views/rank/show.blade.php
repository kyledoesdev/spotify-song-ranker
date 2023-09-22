@extends('layouts.app')

@section('content')
    <div class="container">
        <rankmaker 
            :rankingid="{{ $ranking->id }}"
            :ranksongs="{{ $songs }}"
            :isranked="{{ $ranking->is_ranked }}"
            rankname="{{ $ranking->name }}"
        >
        </rankmaker>
    </div>
@endsection