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

        <form action="{{route('Product.update',[$product->id])}}" method="post" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="exampleFormControlInput1" class="form-label">Product Name </label>
                <input type="text" name="name" class="form-control" value="{{$product->name}}">
            </div>
            <div class="mb-3">
                <label for="exampleFormControlInput1" class="form-label">Product Description </label>
                <input type="text" name="description" class="form-control" value="{{$product->description}}">
            </div>
            <div class="mb-3">
                <label for="exampleFormControlInput1" class="form-label">Product Photo </label>
                <input type="file" name="photo" class="form-control" value="{{$product->photo}}">
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <button class="btn btn-success me-md-2" type="submit">Update</button>
            </div>
        </form>

    </div>

@endsection
