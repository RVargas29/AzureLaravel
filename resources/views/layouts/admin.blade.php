<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="{{ URL::asset('css/app.css') }}">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Laravel</title>
    </head>
    <body>
        <nav class="play-navbar">
            <a href="#" target="_blank">
                <img src="/img/logos/iica.svg" alt="IICAPlay" />
            </a>
            <ul class="menu">
                <li>
                    <a href="#home"><span class="glyphicon glyphicon-home"></span> Home</a>
                </li>
                <li>
                    <a href="{{ url(/videos") }}> Videos</a>
                </li>
                <li>
                    <a href="#search">Buscar</a>
                </li>
                <li>
                    <a href="#autentication" class="btn btn-iica-green"><span class="glyphicon glyphicon-lock"></span> Autenticación</a>
                </li>
            </ul>
        </nav>   
        <div class="container">
            @yield('content')                
        </div>     
        <script src="{{mix('js/app.js')}}"></script>
    </body>
</html>
