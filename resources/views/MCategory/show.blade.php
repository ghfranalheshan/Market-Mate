@extends('layout')

@section('content')

    @if($errors->any())

        <div class="alert alert-danger" role="alert">
            <ul>
                @foreach($errors->all() as $item)
                    <li>{{$item}}</li>
                @endforeach
            </ul>

        </div>
    @endif


    <div class="container p-5">


        <div class="mb-3">
            <h4> Category Name </h4>
            <br>
            <P style=" border: 2px solid black; padding: 5px;width:400px" > {{$m_category->name}} </p>
        </div>


    </div>

@endsection
