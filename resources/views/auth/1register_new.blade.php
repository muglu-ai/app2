
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1, shrink-to-fit=no"
    />
    <title>SEMICON India 2025</title>
    <link
        rel="icon"
        href="https://www.semiconindia.org/themes/custom/semicon/favicon.ico"
        type="image/vnd.microsoft.icon"
    />
    <!--     Fonts and icons     -->
    <link
        rel="stylesheet"
        type="text/css"
        href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900"
    />
    <!-- Nucleo Icons -->
    <link
        href="/assets/css/nucleo-icons.css"
        rel="stylesheet"
    />
    <link
        href="/asset/css/nucleo-svg.css"
        rel="stylesheet"
    />
    <!-- Font Awesome Icons -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css"
        integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ=="
        crossorigin="anonymous"
        referrerpolicy="no-referrer"
    />
    <!-- Material Icons -->
    <link
        rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0"
    />
    <!-- CSS Files -->
    <link
        id="pagestyle"
        href="/asset/css/material-dashboard.min.css?v=3.1.0"
        rel="stylesheet"
    />

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Anti-flicker snippet (recommended)  -->
    <style>
        .async-hide {
            opacity: 0 !important;
        }
    </style>
    <!-- End Google Tag Manager -->
</head>

<body class="bg-gray-200">
<!-- Extra details for Live View on GitHub Pages --><!-- Google Tag Manager (noscript) -->
<!-- End Google Tag Manager (noscript) -->
<!-- Navbar -->
<nav
    class="navbar navbar-expand-lg position-absolute top-0 z-index-3 w-100 shadow-none my-3 navbar-transparent mt-4"
>
    <div class="container">
        <a
            class="navbar-brand ms-2 d-flex flex-column align-items-center"
            href="/"
        >
            {{config('constants.EVENT_NAME')}
            </svg>
            <span>SEMICON India 2025</span>
        </a>
    </div>
</nav>
<!-- End Navbar -->
<main class="main-content mt-0">
    <div
        class="page-header align-items-start min-height-300 m-3 border-radius-xl"
        style="
          background-image: url('https://www.semiconindia.org/sites/semiconindia.org/files/styles/2100x600/public/2024-02/2200x860_Carousel_SCIndia_1%404x_resized.png.webp?itok=g3Bp68Lz');
        "
    >
        <span class="mask bg-gradient-dark opacity-6"></span>
    </div>
    <div class="container mb-4">
        <div class="row mt-lg-n12 mt-md-n12 mt-n12 justify-content-center">
            <div class="col-xl-4 col-lg-5 col-md-7 mx-auto mt-5">
                <div class="card mt-5">
                    <div
                        class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 mb-4 mt-3"
                    >
                        <div
                            class="bg-gradient-success shadow-success border-radius-lg py-3 pe-1 text-center py-4"
                        >
                            <h4 class="font-weight-bolder text-white mt-1">Register</h4>
                            <p class="mb-1 text-sm text-white">
                                Enter your details to register
                            </p>
                        </div>
                    </div>
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="p-4 mb-4 text-red-700 bg-red-100 rounded">
                                {{ $errors->first() }}
                            </div>
                        @endif
                        <form method="POST" action="{{ route('register') }}" role="form" class="text-start">
                            @csrf
                        <div class="input-group input-group-static mb-4">
                            <label for="name">Name:</label>
                            <input type="text" name="name" class="form-control validate" required />
                        </div>
                        <div class="input-group input-group-static mb-4">
                            <label for="email">Email:</label>
                            <input type="email" name="email" class="form-control validate" required />
                        </div>
                        <div class="input-group input-group-static mb-4">
                            <label for="password">Password:</label>
                            <input type="password" id="password" name="password" class="form-control validate" required minlength="6" /><br />
                        </div>
                        <div class="input-group input-group-static mb-4">
                            <label for="password_confirmation">Confirm Password:</label>
                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                class="form-control validate"
                                required
                                minlength="6"
                            />
                        </div>

                        <div class="text-center">
                            <button
                                onclick="return validatePasswords()"
                                type="submit"
                                class="btn bg-gradient-dark w-100 mt-3 mb-0"
                            >
                                Register
                            </button>
                        </div>
                            <div class="card-footer text-center pt-0 px-lg-2 px-1">
                                <p class="mb-1 text-sm mx-auto">
                      Already have an account?<br />
                      <a href="{{route('login')}}" class="text-success text-gradient font-weight-bold">Click here to login</a>
                                </p>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function validatePasswords() {
            var password = document.getElementById('password').value;
            var confirmPassword = document.getElementById('password_confirmation').value;
            if (password !== confirmPassword) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Passwords do not match!',
                });
                return false;
            }
            return true;
        }
    </script>

    <footer class="footer position-absolute bottom-2 py-2 w-100">
        <div class="container">
            <div class="row align-items-center justify-content-lg-between">
                <div class="col-12 col-md-6 my-auto"></div>
            </div>
        </div>
    </footer>
</main>
<!--   Core JS Files   -->
<script src="/asset/js/core/popper.min.js"></script>
<script src="/asset/js/core/bootstrap.min.js"></script>
<script src="/asset/js/plugins/perfect-scrollbar.min.js"></script>
<script src="/asset/js/plugins/smooth-scrollbar.min.js"></script>
<script src="/asset/js/plugins/choices.min.js"></script>
<script src="/asset/js/plugins/dropzone.min.js"></script>
<script src="/assets/js/plugins/quill.min.js"></script>
<script src="/assets/plugins/multistep-form.js"></script>
<script src="/asset/js/plugins/choices.min.js"></script>
<!-- Kanban scripts -->

<script>
    var win = navigator.platform.indexOf("Win") > -1;
    if (win && document.querySelector("#sidenav-scrollbar")) {
        var options = {
            damping: "0.5",
        };
        Scrollbar.init(document.querySelector("#sidenav-scrollbar"), options);
    }
</script>
<!-- Github buttons -->
<script async defer src="https://buttons.github.io/buttons.js"></script>

</body>
</html>
