<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-radius-lg fixed-start ms-2  bg-white my-2"
       id="sidenav-main">
    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-dark opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
           aria-hidden="true" id="iconSidenav"></i>
        <a class="navbar-brand px-4 py-3 m-0"
           href="#"
           >

                       <img src="{{config('constants.event_logo')}}" class="navbar-brand-img"    alt="main_logo">
            <br>
            {{-- <span class="ms-1 text-sm text-dark">{{config('constants.EVENT_NAME')}} {{config('constants.EVENT_YEAR')}}</span> --}}
        </a>
    </div>
    <hr class="horizontal dark mt-0 mb-2">
    <div class="collapse navbar-collapse  w-auto h-auto" id="sidenav-collapse-main">
        <ul class="navbar-nav">
            <li class="nav-item mb-2 mt-0">

                <p class="nav-link text-dark" aria-controls="ProfileNav"
                   role="button" aria-expanded="false">
                    {{--                    <img src="/asset/img/team-3.jpg" class="avatar">--}}
                    <span class=" ms-2 ps-1">{{Auth::user()->name}}</span>
                </p>

            </li>
            <hr class="horizontal dark mt-0">
            <li class="nav-item">
                <ul class="nav ">
                    <li class="nav-item">
                        @php
                            $active0 = route('user.dashboard') ? 'active' : '';
                        @endphp
                        <a class="nav-link text-dark {{$active0}}" href="{{route('user.dashboard')}}">
                            <i class="material-symbols-rounded opacity-5">space_dashboard</i>
                            <span class="sidenav-normal  ms-1  ps-1"> Dashboard </span>
                        </a>
                    </li>
                </ul>

            </li>
            <hr class="horizontal dark mt-0">
            <li class="nav-item">
                <ul class="nav ">
                    <li class="nav-item">
                        @php
                            $active1 = route('application.info') ? 'active' : '';
                        @endphp
                        <a class="nav-link text-dark " href="{{route('application.info')}}">
                            <i class="material-symbols-rounded opacity-5">space_dashboard</i>
                            <span class="sidenav-normal  ms-1  ps-1"> Application Info </span>
                        </a>
                    </li>
                </ul>
            </li>
            <hr class="horizontal dark mt-0">
            <li class="nav-item">
                <ul class="nav ">
                    <li class="nav-item">
                        @php
                            $active1 = route('exhibitor.invoices') ? 'active' : '';
                        @endphp
                        <a class="nav-link text-dark " href="{{route('exhibitor.invoices')}}">
                            <i class="fa-solid fa-file-invoice"></i>
                            <span class="sidenav-normal  ms-1  ps-1"> Invoices </span>
                        </a>
                    </li>
                </ul>

            </li>
            <li class="nav-item">
                <ul class="nav ">
                    <li class="nav-item">
                        @php
                            $active2= route('co_exhibitor') ? 'active' : '';
                        @endphp
                        <a class="nav-link text-dark " href="{{route('co_exhibitor')}}">
                            <i class="fa-solid fa-building"></i>
                            <span class="sidenav-normal  ms-1  ps-1"> Co - Exhibitors </span>
                        </a>
                    </li>
                </ul>

            </li>
            <li class="nav-item mt-3">
                <h6 class="ps-3  ms-2 text-uppercase text-xs font-weight-bolder text-dark">Passes</h6>
            </li>
            <li class="nav-item">
                <a data-bs-toggle="collapse" href="#pagesExamples" class="nav-link text-dark "
                   aria-controls="pagesExamples" role="button" aria-expanded="false">
                    <i class="fa-solid fa-ticket"></i>
                    <span class="nav-link-text ms-1 ps-1">Complimentary Passes</span>
                </a>
                <div class="collapse " id="pagesExamples">
                    <ul class="nav ">
                        <li class="nav-item ">
                            <a class="nav-link text-dark " href="/exhibitor/list/complimentary">
                                <span class="sidenav-mini-icon"> D </span>
                                <span class="sidenav-normal  ms-1  ps-1"> Delegate Passes </span>
                            </a>
                        </li>
                        <li class="nav-item ">
                            <a class="nav-link text-dark " href="/exhibitor/list/stall_manning">
                                <span class="sidenav-mini-icon"> S </span>
                                <span class="sidenav-normal  ms-1  ps-1"> Stall Manning Passes </span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            <hr class="horizontal dark mt-0">
            <li class="nav-item">
                <a data-bs-toggle="collapse" href="#requirements" class="nav-link text-dark "
                   aria-controls="pagesExamples" role="button" aria-expanded="false">
                    <i class="fa-solid fa-clipboard-list"></i>
                    <span class="nav-link-text ms-1 ps-1">Extra Requirements</span>
                </a>
                <div class="collapse " id="requirements">
                    <ul class="nav ">

                        <li class="nav-item ">
                            <a class="nav-link text-dark " href="{{route('exhibitor.orders')}}">
                                <span class="sidenav-mini-icon"> O </span>
                                <span class="sidenav-normal  ms-1  ps-1"> Order Details  </span>
                            </a>
                        </li>
                        <li class="nav-item ">
                            <a class="nav-link text-dark " href="{{route('extra_requirements.index')}}">
                                <span class="sidenav-mini-icon"> P </span>
                                <span class="sidenav-normal  ms-1  ps-1"> Purchase Item  </span>
                            </a>
                        </li>

                    </ul>
                </div>
            </li>
            <li class="nav-item">
                <form method="POST" action="{{ route('logout') }}" class="d-flex justify-content-start">
                    @csrf
                    <button type="submit" class="waves-effect waves-grey nav-link text-dark" style="display: inline-flex; align-items: center; gap: 5px; background: none; border: none; cursor: pointer;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-right" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z"></path>
                            <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z"></path>
                        </svg>
                        <span class="nav-link-text ms-1 ps-1 text-dark test-md "> Sign out </span>
                    </button>
                </form>
            </li>
{{--            <li class="nav-item">--}}
{{--                <ul class="nav ">--}}
{{--                    <li class="nav-item">--}}
{{--                        @php--}}
{{--                            $active2 = route('user.dashboard') ? 'active' : '';--}}
{{--                        @endphp--}}
{{--                        <a class="nav-link text-dark" href="{{route('extra_requirements.index')}}">--}}
{{--                            <i class="fa-solid fa-clipboard-list"></i>--}}

{{--                            <span class="sidenav-normal  ms-1  ps-1"> Extra Requirements </span>--}}
{{--                        </a>--}}
{{--                    </li>--}}
{{--                </ul>--}}

{{--            </li>--}}

        </ul>
    </div>
{{--    <div class="mt-auto">--}}
{{--        <ul class="navbar-nav">--}}
{{--            <li class="nav-item">--}}
{{--                <form method="POST" action="{{ route('logout') }}" class="d-flex justify-content-start">--}}
{{--                    @csrf--}}
{{--                    <button type="submit" class="waves-effect waves-grey"--}}
{{--                            style="display: inline-flex; align-items: center; gap: 5px; background: none; border: none; cursor: pointer;">--}}
{{--                        <i class="material-symbols-rounded opacity-6 me-2 text-md mx-3">login</i>--}}
{{--                        <span class="sidenav-normal  ms-1 ps-1"> Sign out </span>--}}
{{--                    </button>--}}
{{--                </form>--}}
{{--            </li>--}}
{{--        </ul>--}}
{{--    </div>--}}
</aside>
