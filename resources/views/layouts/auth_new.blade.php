
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>@yield('title', config('constants.EVENT_NAME') . ' ' . config('constants.EVENT_YEAR'))</title> <link rel="icon" href="{{config('constants.event_logo')}}" type="image/vnd.microsoft.icon" />
    <!--     Fonts and icons     -->
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />
    <!-- Nucleo Icons -->
    <link href="/asset/css/nucleo-icons.css" rel="stylesheet" />
    <link href="/asset/css/nucleo-svg.css" rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <!-- Material Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
    <!-- CSS Files -->
    <link id="pagestyle" href="/asset/css/material-dashboard.min.css?v=3.1.0" rel="stylesheet" />
    <!-- Anti-flicker snippet (recommended)  -->
    <style>
        .async-hide {
            opacity: 0 !important
        }
    </style>
    <!-- End Google Tag Manager -->
</head>

<body class="bg-gray-200"><!-- Extra details for Live View on GitHub Pages --><!-- Google Tag Manager (noscript) -->
<!-- End Google Tag Manager (noscript) -->
<!-- Navbar -->
<nav class="navbar navbar-expand-lg position-absolute top-0 z-index-3 w-100 shadow-none my-3 navbar-transparent mt-4">
    <div class="container">
        <a class="navbar-brand ms-2 d-flex flex-column align-items-center" href="/">
            <img src="{{ config('constants.event_logo') }}" alt="Event Logo" class="mb-1" style="max-height: 60px;">
            <span style="color: #000;">{{config('constants.EVENT_NAME')}} {{config('constants.EVENT_YEAR')}}</span>
        </a>
    </div>
</nav>
<!-- End Navbar -->
<main class="main-content mt-0">
    <div class="page-header align-items-start min-height-300 m-3 border-radius-xl" style="background-image: url('https://bengalurutechsummit.com/login_banner.jpg');">
        <span class="mask bg-gradient-dark opacity-6"></span>
    </div>
@yield('content')




</main>

<footer class="footer py-3 w-100 mt-3">
    <div class="container">
        <div class="row align-items-center text-center">
            <div class="col-12 col-md-4 text-md-start d-flex justify-content-center justify-content-md-start align-items-center">
                <p class="mb-0 text-wrap text-sm text-white">© Copyright <span id="currentYear"></span> {{config('constants.EVENT_NAME')}}. All Rights Reserved.</p>
            </div>

            <!-- Black Vertical Separator -->
            <div class="separator d-none d-md-block"></div>

            <div class="col-12 col-md-3 text-center d-flex justify-content-center align-items-center">
                <a href="{{config('constants.APP_URL')}}/terms-conditions" class="nav-link text-white">Terms & Conditions</a>
            </div>

            <!-- Black Vertical Separator -->
            <div class="separator d-none d-md-block"></div>

            <div class="col-12 col-md-4 text-md-end d-flex justify-content-center justify-content-md-end align-items-center">
                <p class="mb-0 text-wrap text-sm text-white">Powered by MM Activ Sci-Tech Communications PVT. LTD.</p>
            </div>
        </div>
    </div>
</footer>

<script>
    document.getElementById("currentYear").textContent = new Date().getFullYear();
</script>

<style>
    .footer {
        background-color: #3f504e; /* Dark background for a strong footer band */
        padding: 20px 0;
        border-top: 3px solid #ffffff20; /* Light border for a sleek look */
        box-shadow: 0px -2px 5px rgba(0, 0, 0, 0.2); /* Soft shadow effect */
    }

    .separator {
        width: 2px !important;  /* Forces exact width */
        height: 25px !important; /* Forces exact height */
        background-color: #FFFFFF;
        margin: 0 10px !important; /* Ensures no extra spacing */
        padding: 0 !important; /* Removes any internal padding */
        display: inline-block; /* Prevents extra spacing issues */
    }

    .text-sm {
        font-size: 14px;
    }

    .nav-link {
        color: #ffffff !important;
        font-weight: 500;
    }

    .nav-link:hover {
        text-decoration: underline;
    }
</style>






<!--   Core JS Files   -->
<script src="/asset/js/core/popper.min.js"></script>
<script src="/asset/js/core/bootstrap.min.js"></script>
<script src="/asset/js/plugins/perfect-scrollbar.min.js"></script>
<script src="/asset/js/plugins/smooth-scrollbar.min.js"></script>
<!-- Kanban scripts -->
<script src="/asset/js/plugins/dragula/dragula.min.js"></script>
<script src="/asset/js/plugins/jkanban/jkanban.min.js"></script>
<script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
        var options = {
            damping: '0.5'
        }
        Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
</script>
<!-- Github buttons -->
<script async defer src="https://buttons.github.io/buttons.js"></script>
<!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
<script src="/asset/js/material-dashboard.min.js?v=3.1.0"></script>
</body>

</html>
