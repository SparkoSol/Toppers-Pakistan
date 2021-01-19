<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Apna Pos</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script type="text/javascript" src="js/jquery.printPage.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/mystyleSheet.css') }}" rel="stylesheet">
</head>

<body>
    <div>
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand">
                    <div class="d-flex justify-content-center">
                        <img style="margin-right:15px" width="50" height="50" src="/images/ApnaPos.png" alt="">
                        <h3 style="padding-top:15px">Apna Pos</h3>
                    </div>
                </a>
            </div>
        </nav>
    </div>
    <div class="container" style="margin-top:50px">
            <div class="">
                <img src="/images/error.png"  class="mx-auto d-block" alt="">
                <div class="text-center" style="padding:50px">
                    <h3>Problem Occured</h3>
                </div>
            </div>
    </div>
</body>
