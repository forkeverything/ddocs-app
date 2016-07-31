<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Files Collector</title>

    {{--<link rel="shortcut icon" href="{{ asset('/images/icons/favicon.png') }}">--}}
    <link href="{{ asset('/css/landing.css') }}" rel="stylesheet">
</head>

<body>

@include('layouts.partials.nav')

<div id="landing" class="container">

    <!-- landing stuff -->

</div>
<script type="text/javascript" src="{{ asset('/js/landing/vendor.js') }}"></script>
<script type="text/javascript" src="{{ asset('/js/landing/dependencies.js') }}"></script>
<script type="text/javascript" src="{{ asset('/js/landing/main.js') }}"></script>
</body>
</html>
