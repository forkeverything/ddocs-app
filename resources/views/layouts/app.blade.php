<!DOCTYPE html>
<html lang="en">
<head>


    @include('layouts.partials.head')

    <!-- Styles -->
    <link href="/css/vendor.css" rel="stylesheet">
    <link href="/css/app.css" rel="stylesheet">
</head>
<body>

    @include('layouts.partials.nav')

    @yield('content')

    <!-- Scripts -->
    <script src="/js/app.js"></script>

</body>
</html>
