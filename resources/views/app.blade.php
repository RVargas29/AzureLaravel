<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="{{ URL::asset('css/app.css') }}">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>IICA Play</title>
    </head>
    <body>
        <div id="app"></div>
        <script src="{{mix('js/app.js')}}"></script>
    </body>
</html>
