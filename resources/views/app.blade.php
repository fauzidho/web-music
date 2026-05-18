<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'WebMusic') }}</title>

        <!-- Google Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts and Styles -->
        @vite(['resources/js/app.js', 'resources/css/app.css'])
        @inertiaHead
        
        <style>
            body {
                font-family: 'Plus Jakarta Sans', 'Outfit', sans-serif;
                background-color: #0b0f19;
                color: #f3f4f6;
                margin: 0;
                overflow-x: hidden;
            }
        </style>
    </head>
    <body class="antialiased">
        @inertia
    </body>
</html>
