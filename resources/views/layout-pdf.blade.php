<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <title>@yield('title')</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('/css/admin.css') }}">
    @yield('stylesheet')
</head>
<body class="pdf">
<div id="application" class="wrapper">
    <main role="main" id="content">
        @yield('content')
    </main>
</div>
</body>
</html>