@php
    $config = [
        'appName' => config('app.name'),
        'locale' => ($locale = app()->getLocale()),
        'locales' => config('app.locales'),
        'githubAuth' => config('services.github.client_id'),
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{ config('app.name') }}</title>
    <link rel="icon" href='{{ asset('images/' . config('config.favicon')) }}'>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Urbanist:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ mix('/dist/css/app.css') }}">

    {{-- <script src="/bower_components/webcomponentsjs/webcomponents-lite.min.js"></script>
    <link rel="import" href="/bower_components/paper-progress/paper-progress.html">
    <link rel="import" href="/bower_components/paper-slider/paper-slider.html">
    <link rel="import" href="/bower_components/paper-button/paper-button.html">
    <link rel="import" href="/bower_components/paper-card/paper-card.html">
    <link rel="import" href="/bower_components/paper-dialog/paper-dialog.html">
    <link rel="import" href="/bower_components/paper-input/paper-input.html">
    <link rel="import" href="/bower_components/paper-input/paper-input-container.html">
    <link rel="import" href="/bower_components/paper-input/paper-input-error.html">
    <link rel="import" href="/bower_components/paper-input/paper-input-char-counter.html">
    <link rel="import" href="/bower_components/paper-input/paper-textarea.html"> 
    <link rel="import" href="/bower_components/paper-styles/color.html">
    <link rel="stylesheet" href="/bower_components/paper-styles/demo.css">
    <link rel="import" href="../polymer/polymer.html"> --}}

</head>

<body class="hold-transition layout-footer-fixed">
    <div id="app"></div>

    {{-- Global configuration object --}}
    <script>
        window.config = @json($config);
    </script>

    {{-- Load the application scripts --}}

    <script src="{{ mix('/dist/js/app.js') }}"></script>

</body>

</html>
