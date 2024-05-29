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

        <form action="{{route('MCategory.update',[$m_category->id])}}" method="post">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="exampleFormControlInput1" class="form-label">Category Name </label>
                <input type="text" name="name" class="form-control" value="{{$m_category->name}}">
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <button class="btn btn-success me-md-2" type="submit">Update</button>
            </div>
        </form>

    </div>

@endsection
