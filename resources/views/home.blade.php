@extends('layouts.app')

@section('content')
    <div class="card" style="min-height: 600px">
        <div class="card-header">
            <div class="row">
                <div class="col">
                    <h2 class="mt-2">Welcome, {{ auth()->user()->name }}</h2>
                </div>
                <div class="col d-flex justify-content-end mt-2">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="btn btn-secondary" type="submit" title="Logout ?">
                            <i class="fa-solid fa-person-running"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="card-body">
            <home :lists="{{ $lists }}"></home>
        </div>
    </div>
@endsection