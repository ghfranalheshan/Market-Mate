@extends('layout')

@section('content')

    <br>
    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
        <a class="btn btn-success me-md-2" href="{{route('Product.create')}}"> Create product </a>

    </div>

    <br>

    @if($message = \Illuminate\Support\Facades\Session::get('Success'))
        <div class="alert alert-success" role="alert">
            {{$message}}
        </div>
    @endif

    @if($message = \Illuminate\Support\Facades\Session::get('Failed'))
        <div class="alert alert-danger" role="alert">
            {{$message}}
        </div>
    @endif

    <table class="table">
        <thead class="table-dark">
        <tr>
            <th scope="col">ID</th>
            <th scope="col">Name</th>
            <th scope="col">Description</th>
            <th scope="col"></th>
            <th scope="col"></th>
            <th scope="col"></th>
        </tr>
        </thead>
        <tbody>
        @foreach($products as $item)
            <tr>
                <td>{{$item->id}}</td>
                <td>{{$item->name}}</td>
                <td>{{$item->description}}</td>
                </td>
                <td>
                    <a class="btn btn-success me-md-2" href="{{route('Product.show',$item->id)}}">Show </a>
                </td>
                <td>
                    <a class="btn btn-success me-md-2" href="{{route('Product.edit',$item->id)}}">Edit </a>
                </td>

                <td>
                    <form action="{{route('Product.destroy',$item->id)}}" method="post">
                        @csrf
                        @method('DELETE')

                        <button class="btn btn-outline-danger me-md-2" type="submit"> Delete </button>

                    </form>
            </tr>
        @endforeach


        </tbody>
    </table>

@endsection
