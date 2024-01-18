@extends('layouts.app')

@section('content')
    <div class="card" style="min-height: 600px">
        <div class="card-header">
            <div class="row">
                <div class="col">
                    <h2 class="mt-4">Welcome, {{ auth()->user()->name }}</h2>
                </div>
                <div class="col d-flex justify-content-end mt-2">
                    <div class="dropdown-center">
                        <img
                            height="64"
                            width="64"
                            class="rounded-pill border border-3 border-dark dropdown-toggle mb-2" 
                            type="button" data-bs-toggle="dropdown" 
                            aria-expanded="false" 
                            src="{{ auth()->user()->avatar }}" 
                            alt="User Actions"
                        />
                        <ul class="dropdown-menu">
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="{{ route('logout') }}">
                                    Logout
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <home :lists="{{ $lists }}"></home>
        </div>
    </div>
@endsection