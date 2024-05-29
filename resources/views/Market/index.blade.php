@extends('layout')

@section('content')

<br>
<br>
    <table class="table">
        <thead class="table-dark">
        <tr>
            <th scope="col">Market name</th>
            <th scope="col">Name</th>
            <th scope="col">Market Type</th>
            <th scope="col">Work Time</th>

        </tr>
        </thead>
        <tbody>
        @foreach($markets as $item)
            <tr>
                <td>{{$item->market_name}}</td>
                <td>{{$item->name}}</td>
                <td>{{$item->marketType}}</td>
                <td>{{$item->FullTime}}</td>

                <td>
                    <a class="btn btn-success me-md-2" href="{{route('show',$item->id)}}">Show </a>
                </td>


            </tr>
       @endforeach


        </tbody>
    </table>

@endsection
