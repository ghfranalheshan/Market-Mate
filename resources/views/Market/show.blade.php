@extends('layout')

@section('content')
    <div class="text-center">
        <img src="{{'http://127.0.0.1:8000/'.$market->photo}}" style=" border-radius: 50%; width: 200px; height: 200px;">

    </div>
    <br>
    <div class="card border-dark mb-3">
        <div class="card-header">Market Name</div>
        <div class="card-body">
            {{$market->market_name}}
        </div>
    </div>

    <div class="card border-dark mb-3">
        <div class="card-header">Work Time</div>
        <div class="card-body">
            {{$market->FullTime}}
        </div>
    </div>

    <div class="card border-dark mb-3">
        <div class="card-header">Market Type</div>
        <div class="card-body">
            {{$market->marketType}}
        </div>
    </div>

    <div class="card border-dark mb-3">
        <div class="card-header">Product Category</div>
        <div class="card-body">
            {{$category[0]}}
        </div>
    </div>

    <div class="card border-dark mb-3">
        <div class="card-header">Location</div>
        <div class="card-body">
            {{$market->location}}
        </div>
    </div>

    <div class="card border-dark mb-3">
        <div class="card-header">Phone</div>
        <div class="card-body">
            {{$market->phone}}
        </div>
    </div>


@endsection
