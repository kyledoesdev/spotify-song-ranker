@extends('layouts.app')

@section('content')
    <editranking 
        :ranking="{{ $ranking }}"
    />
@endsection