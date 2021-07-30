<?php $user = auth()->user(); $user->makeHidden('urls'); ?>
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <title>@yield('title', settings('site.title'))</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('/css/admin.css') }}">
    <link rel="stylesheet" href="@yield('stylesheet')">
</head>
<body class="preview h-100">
<div id="application" class="wrapper h-100">
    <main role="main" id="content" class="h-100">
        @yield('content')
    </main>
</div>
<script src="{{ asset('/js/app.js') }}"></script>
<script>
    Settings = @json(settings('site.*'));
    window.quizmaster.settings = @json(settings('site.*'));
    window.quizmaster.user = @json($user);
</script>
@yield('javascript')
</body>
</html>