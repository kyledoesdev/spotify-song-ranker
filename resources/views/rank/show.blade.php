@extends('layouts.app')

@section('content')
    <div class="container">
        <rankmaker 
            rankingid="{{ $ranking->id }}"
            :ranksongs="{{ $songs }}"
            isranked="{{ $ranking->is_ranked ? 'yes' : 'no' }}" {{-- stupid fucking boolean hack i hate Javascript fuck havascript i hate you --}}
            rankname="{{ $ranking->name }}"
            creator="{{ $creator }}"
        >
        </rankmaker>
    </div>
@endsection