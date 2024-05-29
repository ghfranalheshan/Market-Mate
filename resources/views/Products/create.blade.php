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

        <form action="{{route('Product.store')}}" method="post" enctype="multipart/form-data">
            @csrf
            @method('POST')

            <div class="mb-3">

                <label for="exampleFormControlInput1" class="form-label">Name</label>
                <input type="text" name="name" class="form-control">

                <label for="exampleFormControlInput1" class="form-label">Description</label>
                <input type="text" name="description" class="form-control">

                <label for="exampleFormControlInput1" class="form-label">photo</label>
                <input type="file" name="photo" class="form-control">
            </div>

            <button class="btn btn-success me-md-2" type="submit"> Save</button>

        </form>

    </div>

@endsection
