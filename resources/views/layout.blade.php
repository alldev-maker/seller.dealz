<?php $user = auth()->user(); $user->makeHidden('urls'); ?>
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <title>@yield('title', settings('site.title'))</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('/css/admin.css') }}">
    @yield('stylesheet')
</head>
<body>
<div id="application">
    @component('elements.sidebar') @endcomponent
    <main role="main" id="content" class="">
        @component('elements.mainbar') @endcomponent
        <div class="working-area">
            @yield('content')
        </div>
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