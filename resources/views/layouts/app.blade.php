<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Files Collector</title>

    <!-- Favicon -->
{{--<link rel="shortcut icon" href="{{ asset('/images/icons/favicon.png') }}">--}}

<!-- Styles -->
    <link href="{{ asset('/css/app.css') }}" rel="stylesheet">


</head>
<body id="app-layout">

@include('layouts.partials.nav')

@yield('content')

<!-- JavaScripts -->
<script type="text/javascript" src="{{ asset('/js/app/vendor.js') }}"></script>
<script type="text/javascript" src="{{ asset('/js/app/dependencies.js') }}"></script>
<script type="text/javascript" src="{{ asset('/js/app/pages.js') }}"></script>
<script type="text/javascript" src="{{ asset('/js/app/root.js') }}"></script>
</body>
</html>
