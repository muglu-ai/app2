<style>
    @media (max-width: 767px) {
        .dropdown button .text-truncate {
            max-width: none;
        }
        .dropdown-menu {
            min-width: 100%;
        }

        .abc{
            margin-top:10px;
            margin-left: 40px;

        }
    }
    .navbar-brand span {
        display: block;
        margin: 0;
        padding: 0;
    }

    #navbarBlur {
        background-color: #fff;
    }

</style>

<nav class="navbar navbar-main navbar-expand-lg position-sticky mt-2 top-1 px-0 py-1 mx-3 shadow-none border-radius-lg z-index-sticky" id="navbarBlur" data-scroll="true">
    <div class="container-fluid py-1 px-2 d-flex flex-column">

        <!-- First Row: Logo, Buttons, Logout & Email -->
        <div class="d-flex justify-content-between align-items-center w-100">
            <div class="d-flex align-items-center">
                <!-- Logo -->
                <a class="navbar-brand ms-2" href="/">
                    <img src="{{config('constants.event_logo')}}" alt="Logo" width="160" class="img-fluid">

                </a>
                <style>
                   .btn-primary {
                        --bs-btn-color: #0a0a0a !important;
                        --bs-btn-bg: #e91e63 !important;
                        --bs-btn-border-color: #e91e63 !important;
                        --bs-btn-hover-color: #0a0a0a !important;
                        --bs-btn-hover-bg: rgb(236.3,63.75,122.4) !important;
                        --bs-btn-hover-border-color: rgb(235.2,52.5,114.6) !important;
                        --bs-btn-focus-shadow-rgb: 200,27,86 !important;
                        --bs-btn-active-color: #0a0a0a !important;
                        --bs-btn-active-bg: rgb(237.4,75,130.2) !important;
                        --bs-btn-active-border-color: rgb(235.2,52.5,114.6) !important;
                        --bs-btn-active-shadow: none !important;
                        --bs-btn-disabled-color: #0a0a0a !important;
                        --bs-btn-disabled-bg: #e91e63 !important;
                        --bs-btn-disabled-border-color: #e91e63 !important;
                    }
                    .bold-text {
                        font-weight: bold !important;
                        color: #ffffff !important;
                    }
                </style>

                <!-- Buttons for Onboarding & Sponsorship -->
                <a href="/semicon-2025/onboarding" class="btn btn-primary ms-3 me-2 d-none d-md-inline bold-text" >Onboarding</a>
                <a href="/semicon-2025/sponsorship" class="btn btn-secondary d-none d-md-inline bold-text">Sponsorship</a>
            </div>

            <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
                <div class="ms-md-auto pe-md-3 d-flex flex-column flex-lg-row align-items-center">
                    <!-- Profile Icon & Email -->
                    <div class="dropdown abc">
                        <!-- Clickable User Icon & Email -->
                        <button class="btn btn-light d-flex align-items-center" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle me-2"></i>
                            <!-- Email shown by default on medium and larger screens -->
                            <span class="text-dark text-truncate d-none d-md-block">{{ Auth::user()->name }}</span>
                        </button>

                        <!-- Dropdown Menu -->
                        <ul class="dropdown-menu dropdown-menu-end">
                            <!-- Display email inside the dropdown -->
                            <li>
                        <span class="dropdown-item disabled">
                            <i class="bi bi-person-circle me-2"></i>
                            {{ Auth::user()->email }}
                        </span>
                            </li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item d-flex align-items-center gap-2">
                                        <i class="fa-solid fa-right-from-bracket"></i>
                                        <span>Sign Out</span>
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Logout & Email -->
{{--            <div class="d-flex align-items-center d-none d-md-flex">--}}
{{--                <span class="me-3 text-dark"><i class="fa-solid fa-envelope"></i> {{ Auth::user()->email }}</span>--}}
{{--                <form method="POST" action="{{ route('logout') }}">--}}
{{--                    @csrf--}}
{{--                    <button type="submit" class="waves-effect waves-grey" style="display: inline-flex; align-items: center; gap: 5px; background: none; border: none; cursor: pointer;">--}}
{{--                        <i class="fa-solid fa-right-from-bracket"></i>--}}
{{--                        Sign&nbsp;Out--}}
{{--                    </button>--}}
{{--                </form>--}}
{{--            </div>--}}
        </div>


        <style>
            .nav-pills .nav-link {
                border: 1px solid #ccc; /* Light grey border */
                border-radius: 5px; /* Rounded corners */
                margin-right: 5px; /* Space between links */
                background-color: #f8f9fa; /* Light grey background */
                color: #333; /* Dark text color */
            }

            .nav-pills .nav-link.active {
                /*border-color: #007bff; !* Blue border for active link *!*/
                background-color: #007bff; /* Blue background for active link */
                color: #fff; /* White text color for active link */
            }

            .nav-pills .nav-link:hover {
                border-color: #0056b3; /* Darker blue border on hover */
                background-color: #e2e6ea; /* Slightly darker grey background on hover */
                color: #0056b3; /* Darker blue text color on hover */
            }
        </style>
        {{-- @php
            $eventExists = (object) ['event_name' => ''];
            $eventExists->event_name = "{{config('constants.EVENT_NAME')}} {{config('constants.EVENT_YEAR')}}";
        @endphp --}}
        <style>
            /* .bold-black {
                font-weight: bold !important;
                color: #000000 !important;
            } */
            </style>
        <!-- Second Row: Left-Aligned Navigation -->
        <div class="container-fluid mt-2">
            <nav class="nav nav-pills flex-column flex-md-row">
                <a class="nav-link active bold-black" href="{{ route('event.list') }}">
                    <i class="fa-solid fa-house"></i>
                </a>
                <a class="nav-link disabled bold-black" href="#" aria-disabled="true">{{ config('constants.EVENT_NAME') }}</a>
                <a class="nav-link bold-black" href="{{config('constants.SHORT_NAME')}}">Onboarding</a>
            </nav>
        </div>
    </div>
</nav>



{{--<nav class="navbar navbar-main navbar-expand-lg position-sticky mt-2 top-1 px-0 py-1 mx-3 shadow-none border-radius-lg z-index-sticky" id="navbarBlur" data-scroll="true">--}}
{{--    <div class="container-fluid py-1 px-2">--}}
{{--        <nav aria-label="breadcrumb" class="ps-2">--}}
{{--            <ol class="breadcrumb bg-transparent mb-0 p-0">--}}
{{--                <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Home</a></li>--}}
{{--                <li class="breadcrumb-item text-sm text-dark active font-weight-bold" aria-current="page">@yield('title')</li>--}}
{{--            </ol>--}}
{{--        </nav>--}}
{{--        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">--}}
{{--            <div class="ms-md-auto pe-md-3 d-flex align-items-center">--}}
{{--                <form method="POST" action="{{ route('logout') }}">--}}
{{--                    @csrf--}}
{{--                    <button type="submit" class="waves-effect waves-grey" style="display: inline-flex; align-items: center; gap: 5px; background: none; border: none; cursor: pointer;">--}}
{{--                        <i class="fa-solid fa-right-from-bracket"></i>--}}
{{--                        Sign&nbsp;Out--}}
{{--                    </button>--}}
{{--                </form>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</nav>--}}
