<!DOCTYPE html>
<html lang="en">
<head>


@include('layouts.partials.head')

<!-- Styles -->
    <link href="/css/vendor.css" rel="stylesheet">
    <link href="/css/app.css" rel="stylesheet">
</head>
<body>

<div id="app" v-cloak>
    <navbar></navbar>
    <router-view></router-view>
</div>

<!-- Scripts -->
<script src="/js/app.js"></script>

</body>
</html>
