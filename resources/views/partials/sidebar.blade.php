<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-radius-lg fixed-start ms-2  bg-white my-2"
    style="background: #FFFFFF;" id="sidenav-main">
    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-dark opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
            aria-hidden="true" id="iconSidenav"></i>
        <a class="navbar-brand px-4 py-3 m-0" href="#">

            <img src="{{config('constants.event_logo')}}" class="navbar-brand-img"
                alt="main_logo">
            <br>
            {{-- <span class="text-sm text-dark">{{config('constants.EVENT_NAME')}}
                {{config('constants.EVENT_YEAR')}}</span> --}}
        </a>
    </div>
    <hr class="horizontal dark mt-0 mb-2">
    <div class="collapse navbar-collapse  w-auto h-auto  min-vh-75" id="sidenav-collapse-main">
        <ul class="navbar-nav">
            <li class="nav-item mb-2 mt-0">

                <p href="#ProfileNav" class="nav-link text-dark d-flex align-items-center" aria-controls="ProfileNav"
                    role="button" aria-expanded="false">
                    <span class="ms-2 ps-1 text-truncate" style="max-width: 150px;">{{ Auth::user()->name }}</span>
                </p>
            </li>
            <hr class="horizontal dark mt-0">
            <li class="nav-item">
                <ul class="nav ">
                    <li class="nav-item active">
                        @php
                            $active = route('dashboard.admin') ? 'active' : '';
                        @endphp
                        <a class="nav-link text-dark" href="{{route('dashboard.admin')}}">
                            {{-- <i class="fa-solid fa-chart-line"></i>--}}
                            {{-- <i class="material-symbols-rounded  text-dark "
                                style="margin-left:-6px">space_dashboard</i>--}}
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-ui-checks-grid" viewBox="0 0 16 16" style="margin-left:-5px">
                                <path
                                    d="M2 10h3a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1m9-9h3a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-3a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1m0 9a1 1 0 0 0-1 1v3a1 1 0 0 0 1 1h3a1 1 0 0 0 1-1v-3a1 1 0 0 0-1-1zm0-10a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h3a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zM2 9a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h3a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2zm7 2a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2h-3a2 2 0 0 1-2-2zM0 2a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm5.354.854a.5.5 0 1 0-.708-.708L3 3.793l-.646-.647a.5.5 0 1 0-.708.708l1 1a.5.5 0 0 0 .708 0z" />
                            </svg>
                            <span class="nav-link-text text-dark ms-1"> Dashboard </span>
                        </a>
                    </li>
                </ul>
            </li>
            {{-- <li class="nav-item mt-3">--}}
                {{-- <h6 class="ps-3  ms-2 text-uppercase text-xs font-weight-bolder text-dark">Sales</h6>--}}
                {{-- </li>--}}
            <li class="nav-item">
                <a href="/sales" class="nav-link text-dark " aria-controls="pagesExamples" role="button"
                    aria-expanded="false">
                    {{-- <i
                        class="material-symbols-rounded opacity-5 {% if page.brand == 'RTL' %}ms-2{% else %} me-2{% endif %}">fa-user</i>--}}
                    <i class="fa-solid fa-chart-line"></i>
                    <span class="nav-link-text ms-1 ps-1 text-dark ">Sales</span>
                </a>
            </li>
            {{-- <li class="nav-item mt-3">--}}
                {{-- <h6 class="ps-3  ms-2 text-uppercase text-xs font-weight-bolder text-dark">Users</h6>--}}
                {{-- </li>--}}
            <li class="nav-item">
                <a href="/users/list" class="nav-link text-dark " aria-controls="pagesExamples" role="button"
                    aria-expanded="false">
                    {{-- <i
                        class="material-symbols-rounded opacity-5 {% if page.brand == 'RTL' %}ms-2{% else %} me-2{% endif %}">fa-user</i>--}}
                    <i class="fa-regular fa-user"></i>
                    <span class="nav-link-text ms-1 ps-1">Users</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('extra_requirements.admin')}}" class="nav-link text-dark "
                    aria-controls="pagesExamples" role="button" aria-expanded="false">
                    {{-- <i
                        class="material-symbols-rounded opacity-5 {% if page.brand == 'RTL' %}ms-2{% else %} me-2{% endif %}">fa-user</i>--}}
                    <i class="fa-solid fa-list"></i>
                    <span class="nav-link-text ms-1 ps-1 text-dark ">Extra Requirements </span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('allocations.list') }}" class="nav-link text-dark" aria-controls="pagesExamples" role="button" aria-expanded="false">
                    <i class="fa-solid fa-id-badge"></i>
                    <span class="nav-link-text ms-1 ps-1 text-dark">Complimentary Badge</span>
                </a>
            </li>
            <li class="nav-item">
                <a data-bs-toggle="collapse" href="#invoices" class="nav-link text-dark " aria-controls="pagesExamples"
                    role="button" aria-expanded="false">
                    <i class="fa-solid fa-file-invoice"></i>
                    <span class="nav-link-text ms-1 ps-1 text-dark ">Invoices</span>
                </a>
                <div class="collapse" id="invoices">
                    <ul class="nav">
                        <li class="nav-item">
                            <a class="nav-link text-dark" href="{{route('invoice.list')}}">
                                <span class="sidenav-mini-icon"> A </span>
                                <span class="sidenav-normal ms-1 ps-1 text-dark "> All Invoices </span>
                            </a>
                        </li>
                        {{-- <li class="nav-item">--}}
                            {{-- <a class="nav-link text-dark" href="">--}}
                                {{-- <span class="sidenav-mini-icon"> P </span>--}}
                                {{-- <span class="sidenav-normal ms-1 ps-1"> Pending Invoices </span>--}}
                                {{-- </a>--}}
                            {{-- </li>--}}
                        {{-- <li class="nav-item">--}}
                            {{-- <a class="nav-link text-dark" href="">--}}
                                {{-- <span class="sidenav-mini-icon"> P </span>--}}
                                {{-- <span class="sidenav-normal ms-1 ps-1"> Paid Invoices </span>--}}
                                {{-- </a>--}}
                            {{-- </li>--}}
                    </ul>
                </div>
            </li>
            {{-- <li class="nav-item mt-3">--}}
                {{-- <h6 class="ps-3  ms-2 text-uppercase text-xs font-weight-bolder text-dark">Sponsorship</h6>--}}
                {{-- </li>--}}
            <li class="nav-item">
                <a data-bs-toggle="collapse" href="#sponsorship" class="nav-link text-dark "
                    aria-controls="pagesExamples" role="button" aria-expanded="false">
                    <i
                        class="material-symbols-rounded opacity-5 {% if page.brand == 'RTL' %}ms-2{% else %} me-2{% endif %}">s</i>
                    <span class="nav-link-text ms-1 ps-1 text-dark ">Sponsors</span>
                </a>
                <div class="collapse " id="sponsorship">
                    <ul class="nav ">
                        <li class="nav-item ">
                            <a class="nav-link text-dark " href="{{route('sponsor.create_new')}}">
                                <span class="sidenav-mini-icon"> M </span>
                                <span class="sidenav-normal  ms-1  ps-1 text-dark "> Manage Sponsor Items </span>
                            </a>
                        </li>
                        <li class="nav-item ">
                            <a class="nav-link text-dark " href="/sponsorship-list/">
                                <span class="sidenav-mini-icon"> T </span>
                                <span class="sidenav-normal  ms-1  ps-1 text-dark "> Total Applications </span>
                            </a>
                        </li>
                        <li class="nav-item ">
                            <a class="nav-link text-dark " href="/sponsorship-list/in-progress">
                                <span class="sidenav-mini-icon"> I </span>
                                <span class="sidenav-normal  ms-1  ps-1 text-dark "> Initiated Applications </span>
                            </a>
                        </li>
                        <li class="nav-item ">
                            <a class="nav-link text-dark " href="/sponsorship-list/submitted">
                                <span class="sidenav-mini-icon"> S </span>
                                <span class="sidenav-normal  ms-1  ps-1 text-dark "> Submitted Applications </span>
                            </a>
                        </li>
                        <li class="nav-item ">
                            <a class="nav-link text-dark " href="/sponsorship-list/approved">
                                <span class="sidenav-mini-icon"> A </span>
                                <span class="sidenav-normal  ms-1  ps-1 text-dark "> Approved Applications </span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            {{-- <li class="nav-item mt-3">--}}
                {{-- <h6 class="ps-3  ms-2 text-uppercase text-xs font-weight-bolder text-dark">Exhibitors</h6>--}}
                {{-- </li>--}}
            <li class="nav-item">
                <a data-bs-toggle="collapse" href="#exhibitors" class="nav-link text-dark "
                    aria-controls="pagesExamples" role="button" aria-expanded="false">
                    <i
                        class="material-symbols-rounded opacity-5 {% if page.brand == 'RTL' %}ms-2{% else %} me-2{% endif %}">E</i>
                    <span class="nav-link-text ms-1 ps-1 text-dark ">Exhibitors</span>
                </a>
                <div class="collapse " id="exhibitors">
                    <ul class="nav ">
                        <li class="nav-item ">
                            <a class="nav-link text-dark " href="/application-list">
                                <span class="sidenav-mini-icon"> T </span>
                                <span class="sidenav-normal  ms-1  ps-1 text-dark "> Total Applications </span>
                            </a>
                        </li>
                        <li class="nav-item ">
                            <a class="nav-link text-dark " href="/application-list/in-progress">
                                <span class="sidenav-mini-icon"> I </span>
                                <span class="sidenav-normal  ms-1  ps-1 text-dark "> Initiated Applications </span>
                            </a>
                        </li>
                        <li class="nav-item ">
                            <a class="nav-link text-dark " href="/application-list/submitted">
                                <span class="sidenav-mini-icon"> S </span>
                                <span class="sidenav-normal  ms-1  ps-1 text-dark "> Submitted Applications </span>
                            </a>
                        </li>
                        <li class="nav-item ">
                            <a class="nav-link text-dark " href="/application-list/approved">
                                <span class="sidenav-mini-icon"> A </span>
                                <span class="sidenav-normal  ms-1  ps-1 text-dark "> Approved Applications </span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            {{-- <li class="nav-item">
                <a href="{{route('co_exhibitors')}}" class="nav-link text-dark " aria-controls="pagesExamples"
                    role="button" aria-expanded="false">
                    <i
                        class="material-symbols-rounded opacity-5 {% if page.brand == 'RTL' %}ms-2{% else %} me-2{% endif %}">C</i>
                    <span class="nav-link-text ms-1 ps-1 text-dark ">Co - Exhibitors</span>
                </a>
            </li> --}}
            <li class="nav-item">
                <a href="{{route('visitor.list')}}" class="nav-link text-dark " aria-controls="pagesExamples"
                    role="button" aria-expanded="false">
                    <i class="fa-solid fa-user-group"></i>
                    <span class="nav-link-text ms-1 ps-1 text-dark ">Visitors</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('tickets.index') }}" class="nav-link text-dark" aria-controls="pagesExamples"
                    role="button" aria-expanded="false">
                    <i class="fa-solid fa-user-group"></i>
                    <span class="nav-link-text ms-1 ps-1 text-dark">Delegates</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('tickets.index') }}" class="nav-link text-dark" aria-controls="pagesExamples"
                    role="button" aria-expanded="false">
                    <i class="fa-solid fa-user-group"></i>
                    <span class="nav-link-text ms-1 ps-1 text-dark">Delegate Tickets</span>
                </a>
            </li>
        </ul>
    </div>
    <div class="mt-auto">
        <ul class="navbar-nav">
            <li class="nav-item">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="waves-effect waves-grey nav-link text-dark"
                        style="display: inline-flex; align-items: center; gap: 5px; background: none; border: none; cursor: pointer;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-box-arrow-right" viewBox="0 0 16 16">
                            <path fill-rule="evenodd"
                                d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z" />
                            <path fill-rule="evenodd"
                                d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z" />
                        </svg>
                        <span class="nav-link-text ms-1 ps-1 text-dark test-md "> Sign out </span>
                    </button>
                </form>
            </li>
        </ul>
    </div>
</aside>
