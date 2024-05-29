@extends('layout')

@section('content')

    <div class="container p-5 d-flex flex-column align-items-center justify-content-center">
        <div class="mb-3">
            <br>
            <img src="{{'http://127.0.0.1:8000/'.$photo}}" style="max-width: 100%; max-height: 100%; width: 300px; height: 300px;">
        </div>
        <div class="mb-3">
            <h3 style="padding: 5px;width:400px ;color: black" >Name : {{$product->name}} </h3>
        </div>
        <div class="mb-3">
            <P style="  border: 2px solid black;padding: 5px;width:400px" > {{$product->description}} </p>
        </div>
    </div>

@endsection
