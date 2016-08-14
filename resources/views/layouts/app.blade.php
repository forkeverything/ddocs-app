<!DOCTYPE html>
<html lang="en">
<head>

    @include('layouts.partials.html-head')

    <!-- Styles -->
    <link href="{{ asset('/css/all.css') }}" rel="stylesheet">

    <!-- External Scripts -->
    <script type="text/javascript" src="https://js.stripe.com/v2/"></script>
</head>
<body id="app-layout">

@include('layouts.partials.nav')

@yield('content')

<!-- Local Scripts -->
<script type="text/javascript" src="{{ asset('/js/app/vendor.js') }}"></script>
<script type="text/javascript" src="{{ asset('/js/app/dependencies.js') }}"></script>
<script type="text/javascript" src="{{ asset('/js/app/pages.js') }}"></script>
<script type="text/javascript" src="{{ asset('/js/app/root.js') }}"></script>
</body>
</html>
