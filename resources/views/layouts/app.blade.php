<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="stripe-key" content="{{ env('STRIPE_KEY') }}"/>

    <title>Files Collector</title>

    <!-- Favicon -->
    {{--<link rel="shortcut icon" href="{{ asset('/images/icons/favicon.png') }}">--}}

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
