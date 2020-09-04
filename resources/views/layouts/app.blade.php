<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Toppers Pakistan</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script type="text/javascript" src="js/jquery.printPage.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
<v-app>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/home') }}">
                    <div class="d-flex justify-content-center">
                        <img width="50" height="50" src="/images/ToppersPakistanLogo.png" alt="">
                    </div>
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    {{-- @if(@Auth::user()->type == "Main Admin")
                                    <a class="dropdown-item" href="{{ url('restaurant') }}">
                                        {{ __('Restaurants') }}
                                    </a>
                                    @endif --}}
                                    <a class="dropdown-item" href="{{ url('/home') }}">
                                        {{ __('Home/Orders') }}
                                    </a>
                                    {{-- <a class="dropdown-item" href="{{ url('/shop') }}">
                                        {{ __('Shop') }}
                                    </a> --}}
                                    @if(@Auth::user()->type == "Main Admin")
                                    <a class="dropdown-item" href="{{ url('branch') }}">
                                        {{ __('Restaurant Branches') }}
                                    </a>
                                    @endif
                                    @if(@Auth::user()->type == "Main Admin")
                                    <a class="dropdown-item" href="{{ url('product') }}">
                                        {{ __('Products') }}
                                    </a>
                                    @endif
                                    @if(@Auth::user()->type == "Main Admin")
                                    <a class="dropdown-item" href="{{ url('customer') }}">
                                        {{ __('Customers') }}
                                    </a>
                                    @endif
                                    @if(@Auth::user()->type == "Main Admin")
                                    <a class="dropdown-item" href="{{ url('category') }}">
                                        {{ __('Category') }}
                                    </a>
                                    @endif
                                    @if(@Auth::user()->type == "Main Admin")
                                    <a class="dropdown-item" href="{{ url('subCategory') }}">
                                        {{ __('Sub Category') }}
                                    </a>
                                    @endif
                                    @if(@Auth::user()->type == "Main Admin")
                                    <a class="dropdown-item" href="{{ url('unit') }}">
                                        {{ __('Unit') }}
                                    </a>
                                    @endif
                                    @if(@Auth::user()->type == "Main Admin")
                                    <a class="dropdown-item" href="{{ url('carosel') }}">
                                        {{ __('Ads Image') }}
                                    </a>
                                    @endif
                                    @if(@Auth::user()->type == "Main Admin")
                                    <a class="dropdown-item" href="{{ url('register-admin') }}">
                                        {{ __('Register') }}
                                    </a>
                                    @endif
                                    @if(@Auth::user()->type == "Sub Admin")
                                    <a class="dropdown-item" href="{{ url('add-customer-info') }}">
                                        {{ __('Punch Order') }}
                                    </a>
                                    @endif
                                    @if(@Auth::user()->type == "Main Admin")
                                    <a class="dropdown-item" href="{{ url('notification') }}">
                                        {{ __('Notification') }}
                                    </a>
                                    @endif
                                    <a class="dropdown-item" href="{{ url('report') }}">
                                        {{ __('Reports') }}
                                    </a>
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
                    </ul>
                </div>
            </div>
        </nav>

        <example-component />

        <main class="py-4">
            @yield('content')
        </main>
    </div>
</v-app>
</body>
</html>
