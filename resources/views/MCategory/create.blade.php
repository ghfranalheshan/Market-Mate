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

        <form action="{{route('MCategory.store')}}" method="post">
            @csrf
            @method('POST')

            <div class="mb-3">
                <h4> Category Name </h4>
                <br>
                <input type="text" name="name" class="form-control">
            </div>

            <button class="btn btn-success me-md-2" type="submit"> Save</button>

        </form>

    </div>

@endsection
