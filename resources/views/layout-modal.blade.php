<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <title><?php echo settings('site.title'); ?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('/css/admin.css') }}">
</head>
<body class="layout-modal">
<div id="application" class="wrapper">
    <main role="main" id="content" class="">
        <div class="working-area m-0">
            @yield('content')
        </div>
    </main>
</div>
<script src="{{ asset('/js/app.js') }}"></script>
<script>Settings = {!! json_encode(settings('site.*')) !!}</script>
@yield('javascript')
</body>
</html>