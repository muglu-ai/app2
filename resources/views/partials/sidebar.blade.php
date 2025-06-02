<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-radius-lg fixed-start ms-2  bg-white my-2"
    style="background: #FFFFFF;" id="sidenav-main">
    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-dark opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
            aria-hidden="true" id="iconSidenav"></i>
        <a class="navbar-brand px-4 py-3 m-0" href="#">
            <svg class="navbar-brand-img" width="140" height="90" viewBox="0 0 163 40"
                xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M43.751 18.973c-2.003-.363-4.369-.454-7.009-.363-8.011 9.623-20.846 17.974-29.403 19.064-2.093.272-3.641.091-4.915-.454.819.726 2.184 1.362 4.096 1.725 8.193 1.634 23.213-1.544 33.499-7.081 10.286-5.538 12.016-11.348 3.732-12.891zm-31.587 2.996c8.557-5.175 19.662-8.897 29.129-10.077C45.299 4.357 43.387-.454 35.923.545c-9.012 1.18-22.758 10.439-30.586 20.607-5.735 7.444-6.737 13.254-3.46 15.523-2.366-3.54 1.275-9.169 10.287-14.706zm58.35-.726l-4.643-1.271c-1.274-.363-1.911-.908-1.911-1.634 0-1.271 2.184-1.907 4.278-1.907 1.912 0 3.186.636 4.187 1.09.638.272 1.184.544 1.73.544 1.457 0 1.73-.635 1.73-1.18l-.182-.635c-.82-1.09-4.37-1.998-8.102-1.998-3.641 0-7.373 1.635-7.373 4.267 0 2.27 2.184 3.177 4.096 3.722l5.28 1.453c1.547.454 3.004.907 3.004 2.178 0 1.18-1.639 2.361-4.734 2.361-2.458 0-4.005-.817-5.098-1.453-.728-.363-1.274-.726-1.82-.726-.82 0-1.639.726-1.639 1.271 0 1.271 3.55 3.086 8.466 3.086 5.189 0 8.648-1.906 8.648-4.629-.091-2.724-3.004-3.722-5.917-4.539zm22.757-6.991c-6.554 0-10.013 4.086-10.013 8.08 0 3.722 2.731 8.079 10.559 8.079 5.371 0 9.103-2.178 9.103-3.268 0-1.271-1.183-1.271-1.638-1.271-.546 0-1.092.273-1.73.727-1.183.726-2.822 1.634-5.917 1.634-3.823 0-6.281-2.361-6.554-4.721h13.928c1.547 0 2.276-.454 2.276-1.452-.091-3.813-3.187-7.808-10.014-7.808zm6.19 6.991h-12.38c.273-2.452 2.367-4.812 6.19-4.812 3.732 0 5.917 2.451 6.19 4.812zm53.253-6.991c-1.093 0-1.73.545-1.73 1.544v12.981c0 .999.637 1.544 1.73 1.544 1.092 0 1.729-.545 1.729-1.544V15.796c0-.999-.637-1.544-1.729-1.544zm-26.399 2.633c1.457-1.543 4.096-2.633 6.645-2.633 4.006 0 8.375 1.816 8.375 5.72v8.896c0 .999-.637 1.543-1.73 1.543-1.092 0-1.729-.544-1.729-1.543v-8.442c0-2.542-1.639-3.722-4.916-3.722-2.458 0-5.006 1.361-5.006 3.722v8.442c0 .999-.638 1.543-1.73 1.543s-1.73-.544-1.73-1.543v-8.442c0-2.452-2.639-3.813-5.006-3.813-3.368 0-4.916 1.271-4.916 3.813v8.442c0 .999-.637 1.543-1.729 1.543-1.093 0-1.73-.544-1.73-1.543v-8.896c0-3.904 4.37-5.72 8.375-5.72 2.64 0 5.189.999 6.645 2.633l.182.091v-.091zm33.044-1.906h-.455a.196.196 0 0 1-.182-.182c0-.091.091-.181.182-.181h1.365c.091 0 .182.09.182.181a.196.196 0 0 1-.182.182h-.455v1.634c0 .091-.091.181-.182.181-.182 0-.182-.09-.182-.181v-1.634h-.091zm1.365 0c0-.273.091-.363.273-.363.091 0 .273 0 .364.181l.547 1.362.455-1.362c.091-.181.182-.181.364-.181s.273.09.273.363v1.634c0 .091-.091.181-.182.181s-.182-.09-.182-.181V15.07l-.546 1.543c0 .181-.091.181-.182.181s-.182-.09-.182-.181l-.547-1.543v1.543c0 .091-.091.181-.182.181s-.182-.09-.182-.181v-1.634h-.091z"
                    id="Shape" fill-rule="nonzero"></path>
            </svg>
            {{-- <img src="/asset/img/logo-ct-dark.png" class="navbar-brand-img" width="26" height="26"
                alt="main_logo">--}}
            <br>
            <span class="ms-1 text-lg text-dark">{{config('constants.EVENT_NAME')}}
                {{config('constants.EVENT_YEAR')}}</span>
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
            {{-- <li class="nav-item mt-3">--}}
                {{-- <h6 class="ps-3  ms-2 text-uppercase text-xs font-weight-bolder text-dark">Invoices</h6>--}}
                {{-- </li>--}}
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
            <li class="nav-item">
                <a href="{{route('co_exhibitors')}}" class="nav-link text-dark " aria-controls="pagesExamples"
                    role="button" aria-expanded="false">
                    <i
                        class="material-symbols-rounded opacity-5 {% if page.brand == 'RTL' %}ms-2{% else %} me-2{% endif %}">C</i>
                    <span class="nav-link-text ms-1 ps-1 text-dark ">Co - Exhibitors</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('visitor.list')}}" class="nav-link text-dark " aria-controls="pagesExamples"
                    role="button" aria-expanded="false">
                    <i class="fa-solid fa-user-group"></i>
                    <span class="nav-link-text ms-1 ps-1 text-dark ">Visitors</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('organizations.index') }}" class="nav-link text-dark">
                    <i class="fa-solid fa-building"></i>
                    <span class="nav-link-text ms-1 ps-1 text-dark">Organizations</span>
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