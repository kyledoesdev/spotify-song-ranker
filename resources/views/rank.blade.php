@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <rank-maker :ranksongs="{{ $songs }}"></rank-maker>
    </div>
@endsection