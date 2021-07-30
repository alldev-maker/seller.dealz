<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <title><?php echo settings('site.title'); ?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('/css/frontend.css') }}">
</head>
<body class="guest">
@yield('content')
</div>

<script src="{{ asset('/js/guest.js') }}"></script>
@yield('javascript')

</body>
</html>