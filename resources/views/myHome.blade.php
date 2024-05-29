@extends('layout')

@section('content')

    <!-- Main Content -->


    <div class="container mt-3" style="padding: 10px">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <canvas id="productChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <canvas id="orderChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="container mt-3" style="padding: 10px">
        <div class="row">
            <div class="col-md-4 mx-auto">
                <div class="card">
                    <img src="{{ asset('Images/customer.jpg') }}" class="card-img-top" style="width: 100%; height: auto;">
                    <div class="card-body">
                        <h5 class="card-title">Total Customers</h5>
                        <p class="card-text">Current Customer number in the App</p>
                        <a class="btn btn-success"> {{ $total['customer']}} </a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mx-auto">
                <div class="card">
                    <img src="{{ asset('Images/revenue.jpg') }}" class="card-img-top" style="width: 100%; height: auto;">
                    <div class="card-body">
                        <h5 class="card-title">Total Markets</h5>
                        <p class="card-text">Current Markets number in the App</p>
                        <a class="btn btn-success"> {{  $total['market'] }} </a>
                    </div>
                </div>
            </div>
        </div>
    </div>




    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


    <script>
        document.addEventListener("DOMContentLoaded", function(event) {
            var ctx = document.getElementById('productChart').getContext('2d');
            var productChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['June', 'July', 'august'],
                    datasets: [{
                        label: 'Total Products',
                        data: {{ json_encode($productdata) }} ,
                        backgroundColor: [
                            'rgba(252,49,21,0.6)',
                            'rgba(22,234,14,0.6)',
                            'rgba(54, 162, 235, 0.6)',

                        ],
                        borderColor: 'rgb(5,9,43)',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function(event) {
            var ctx = document.getElementById('orderChart').getContext('2d');
            var productChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['June', 'July', 'august'],
                    datasets: [{
                        label: 'Total Orders',
                        data: {{ json_encode($orderdata) }} ,
                        backgroundColor: [
                            'rgba(252,49,21,0.6)',
                            'rgba(22,234,14,0.6)',
                            'rgba(54, 162, 235, 0.6)',

                        ],
                        borderColor: 'rgb(5,9,43)',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>
@endsection
