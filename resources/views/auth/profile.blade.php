@extends('layout')

@section('content')

    <br>

    <div class="text-center">
        <img
            src="{{ asset('Images/customer.jpg')}}"
            style=" border-radius: 50%; width: 200px; height: 200px;">
    </div>
    <br>
    <div class="card border-dark mb-3">
        <div class="card-header">Name</div>
        <div class="card-body">
            {{Auth::user()->name}}
        </div>
    </div>

    <div class="card border-dark mb-3">
        <div class="card-header">Email</div>
        <div class="card-body">
            {{Auth::user()->email}}
        </div>
    </div>

    <div class="card border-dark mb-3">
        <div class="card-header">Location</div>
        <div class="card-body">
            {{Auth::user()->location}}
        </div>
    </div>

    <div class="card border-dark mb-3">
        <div class="card-header">Phone</div>
        <div class="card-body">
            {{Auth::user()->phone}}
        </div>
    </div>

    <br>
    <div class="d-grid gap-2 d-md-flex justify-content-md-end">

        <a class="btn btn-success me-md-2" href="{{route('logout')}}">Log Out </a>

    </div>
    <br>

@endsection
