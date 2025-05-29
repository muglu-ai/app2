<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <meta charset="UTF-8">
    <meta name="description" content="Semi-Con registration" />
    <meta name="keywords" content="admin,dashboard" />
    <meta name="author" content="MKS" />
    <title>@yield('title', 'SEEMICON 2025')</title>

    <!-- Styles -->
    <link type="text/css" rel="stylesheet" href="assets/plugins/materialize/css/materialize.min.css"/>
    <link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="assets/plugins/metrojs/MetroJs.min.css" rel="stylesheet">
    <link href="assets/plugins/weather-icons-master/css/weather-icons.min.css" rel="stylesheet">


    <!-- Theme Styles -->
    <link href="assets/css/alpha.min.css" rel="stylesheet" type="text/css"/>
    <link href="assets/css/custom.css" rel="stylesheet" type="text/css"/>
{{--    <link rel="stylesheet" href="{{ asset('css/app.css') }}"> <!-- Add your CSS link -->--}}
</head>
<body>
@include('components.loader')
<div class="mn-content fixed-sidebar">
@include('components.header')
@include('components.navbar')

<div class="container">
    <div class="row">
        <div class="col-md-3">
            @include('components.sidebar')
        </div>
        <div class="col-md-9">
            @yield('content')
        </div>
    </div>
</div>
</div>

{{--@include('components.footer')--}}

<script src="{{ asset('js/app.js') }}"></script> <!-- Add your JS link -->
<script src="/assets/plugins/jquery/jquery-2.2.0.min.js"></script>
<script src="/assets/plugins/materialize/js/materialize.min.js"></script>
<script src="/assets/plugins/material-preloader/js/materialPreloader.min.js"></script>
<script src="/assets/plugins/jquery-blockui/jquery.blockui.js"></script>
<script src="/assets/plugins/waypoints/jquery.waypoints.min.js"></script>
<script src="/assets/plugins/counter-up-master/jquery.counterup.min.js"></script>
<script src="/assets/plugins/jquery-sparkline/jquery.sparkline.min.js"></script>
<script src="/assets/plugins/chart.js/chart.min.js"></script>
<script src="/assets/plugins/flot/jquery.flot.min.js"></script>
<script src="/assets/plugins/flot/jquery.flot.time.min.js"></script>
<script src="/assets/plugins/flot/jquery.flot.symbol.min.js"></script>
<script src="/assets/plugins/flot/jquery.flot.resize.min.js"></script>
<script src="/assets/plugins/flot/jquery.flot.tooltip.min.js"></script>
<script src="/assets/plugins/curvedlines/curvedLines.js"></script>
<script src="/assets/plugins/peity/jquery.peity.min.js"></script>
<script src="/assets/js/alpha.min.js"></script>
<script src="/assets/js/pages/dashboard.js"></script>
</body>
</html>
