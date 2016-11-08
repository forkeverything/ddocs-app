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
    <sidebar></sidebar>
    <div id="center-pane">
        <navbar></navbar>
        <router-view></router-view>
    </div>
</div>

<!-- Scripts -->
<script src="/js/app.js"></script>

</body>
</html>
