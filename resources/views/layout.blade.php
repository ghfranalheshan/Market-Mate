<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css"
          integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>
<nav class="navbar navbar-dark bg-dark" style="height: 90px; padding-top: 0px;">
    <span class="navbar-brand mb-0 h1"> Admin Dashboard </span>


    <ul class="navbar-nav ms-auto">
        <!-- Authentication Links -->
        @guest
            @if (Route::has('login'))
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                </li>
            @endif

            @if (Route::has('register'))
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                </li>
            @endif
    </ul>

    @else
        <li class="nav-item dropdown">
            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                {{ Auth::user()->name }}
            </a>

            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                <a class="dropdown-item" href="{{ route('logout') }}"
                   onclick="event.preventDefault();
            document.getElementById('logout-form').submit();">
                    {{ __('Logout') }}
                </a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </li>

    @endguest
</nav>

@guest
    <div class="text-center">
        <img src="{{ asset('Images/customer.jpg') }}" class="card-img-top" style="width: 20%; height: 20%;">

        {{--        <img src="{{'http://127.0.0.1:8000/'.Auth::user()->photo}}" class="rounded">--}}
    </div>

    <div class="d-flex justify-content-center">
        <span class="navbar-brand mb-0 h1">MarketMate</span>
    </div>

@else


    <div class="container-fluid" style="display: flex; flex-direction: row;margin: 0px ; padding: 0px" >
        <nav class="col-md-2 d-none d-md-block bg-light sidebar" style="margin: 0px ; padding: 0px ; width: 25% ;min-height: 100vh" >
            <div class="sidebar-sticky" style="background-color: #343a40 ; width: 210px; height: 100%; position: relative;" >
                <ul class="nav flex-column" >
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('profile')}}" style="color: #edf2f7">
                            My Profile
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{route('Home')}}" style="color: #edf2f7">
                            Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('MCategory.index')}}" style="color: #edf2f7">
                            Categories
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('index')}}" style="color: #edf2f7">
                            Markets
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('Product.index')}}" style="color: #edf2f7">
                            Products
                        </a>
                    </li>

                </ul>
            </div>
        </nav>

        <div class="container" style="flex: 1 ; margin-right: 20px;" >
            @yield('content')

        </div>

    </div>

@endguest

<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
        crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js"
        integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49"
        crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"
        integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy"
        crossorigin="anonymous"></script>
</body>

</html>

