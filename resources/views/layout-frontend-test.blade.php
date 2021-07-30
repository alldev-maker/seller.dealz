<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <title>@yield('title')</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="@yield('stylesheet')">
</head>
<body class="frontend h-100">
<div id="application" class="wrapper h-100">
    <main role="main" id="content" class="h-100">
        @yield('content')
    </main>
</div>
<?php
$settings = new stdClass();
$settings->timezone = settings('site.timezone');
$settings->showVideo = settings('debug.show.video');
$settings->showDots = settings('debug.show.dots');
$settings->highlight = settings('debug.highlight.read');
?>
<script>window.qm = { }; window.qm.settings =  @json($settings)</script>
@yield('objects')
<script src="{{ asset('/js/frontend-test.js') }}"></script>
@yield('javascript')
</body>
</html>