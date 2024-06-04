@extends('layouts.app')

@section('content')
    <div class="card" style="min-height: 600px">
        <div class="card-header">
            <div class="row">
                <div class="col">
                    <h2 class="mt-2">Welcome, {{ auth()->user()->name }}</h2>
                </div>
                <div class="col d-flex justify-content-end mt-2">
                    
                </div>
            </div>
        </div>
        <div class="card-body">
            doot doot
        </div>
    </div>
@endsection