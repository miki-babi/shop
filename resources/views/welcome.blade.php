<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

</head>
<body>
    <div id="app">
        <welcome-component></welcome-component>
    </div>
    <script src="{{ mix('js/app.js') }}" defer></script>
</body>
</html>
