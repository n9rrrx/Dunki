<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <meta charset="utf-8">
    <title>Sign In | Dunki</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Herozi is the top-selling Bootstrap 5 admin dashboard template. With Dark Mode, multi-demo options, RTL support, and lifetime updates, it's perfect for web developers.">
    <meta name="keywords" content="Herozi bootstrap dashboard, bootstrap, bootstrap 5, html dashboard, web dashboard, admin themes, web design, figma, web development, fullcalendar, datatables, free templates, free admin themes, bootstrap theme, bootstrap template, bootstrap dashboard, bootstrap dark mode, bootstrap button, frontend dashboard, responsive bootstrap theme">
    <meta content="SRBThemes" name="author">
    <link rel="shortcut icon" href="assets/images/Favicon.png">

    <meta property="og:locale" content="en_US">
    <meta property="og:type" content="article">
    <meta property="og:title" content="Herozi - Bootstrap Admin & Dashboard Template">
    <meta property="og:description" content="Herozi is the top-selling Bootstrap 5 admin dashboard template. With Dark Mode, multi-demo options, RTL support, and lifetime updates, it's perfect for web developers.">
    <meta property="og:url" content="https://themeforest.net/user/srbthemes/portfolio">
    <meta property="og:site_name" content="Herozi by SRBThemes">
    <script>
        const AUTH_LAYOUT = true;
    </script>
    <!-- Layout JS -->
    <script src="assets/js/layout/layout-auth.js"></script>

    <script src="assets/js/layout/layout.js"></script>

    <!-- Choice Css -->
    <link rel="stylesheet" href="assets/libs/choices.js/public/assets/styles/choices.min.css">
    <!-- Simplebar Css -->
    <link href="assets/libs/simplebar/simplebar.min.css" rel="stylesheet">
    <!--icons css-->
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css">
    <!-- Sweet Alert -->
    <link href="assets/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css">
    <!-- Bootstrap Css -->
    <link href="assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css">
    <!-- App Css-->
    <link href="assets/css/app.min.css" id="app-style" rel="stylesheet" type="text/css">

    <link href="assets/css/custom.min.css" id="custom-style" rel="stylesheet" type="text/css">
</head>

<body>

<!-- START HEADER -->
<!-- END HEADER -->
<!-- START SIDEBAR -->
<!-- END SIDEBAR -->

<!-- START SMALL SCREEN SIDEBAR -->
<!-- START PRELOADER -->
<!-- END PRELOADER -->

<!-- START -->
<div class="account-pages">
    <img src="{{ asset('assets/images/auth/auth_bg.jpg') }}" alt="auth_bg" class="auth-bg light">  <!-- Laravel asset -->
    <img src="{{ asset('assets/images/auth/auth_bg_dark.jpg') }}" alt="auth_bg_dark" class="auth-bg dark">  <!-- Laravel asset -->

    <div class="container">
        <div class="justify-content-center row gy-0">

            <div class="col-lg-6 auth-banners">
                <div class="bg-login card card-body m-0 h-100 border-0">
                    <img src="{{ asset('assets/images/auth/111.png') }}" class="img-fluid auth-banner" alt="Dunki - Study Abroad Journey">  <!-- Laravel asset -->
                    <div class="auth-contain">
                        <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-indicators">
                                <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                                <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
                                <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2" aria-label="Slide 3"></button>
                            </div>
                            <div class="carousel-inner">
                                <div class="carousel-item active">
                                    <div class="text-center text-white my-4 p-4">
                                        <h3 class="text-white">Start Your Study Abroad Journey</h3>
                                        <p class="mt-3">Create your profile and explore personalized guidance for universities, visas, and target countries. Join Dunki today!</p>
                                    </div>
                                </div>
                                <div class="carousel-item">
                                    <div class="text-center text-white my-4 p-4">
                                        <h3 class="text-white">Track Applications Seamlessly</h3>
                                        <p class="mt-3">Monitor your study visa and university applications in real-time. Stay updated on status, tasks, and consultant assignments with ease.</p>
                                    </div>
                                </div>
                                <div class="carousel-item">
                                    <div class="text-center text-white my-4 p-4">
                                        <h3 class="text-white">Secure Document Management</h3>
                                        <p class="mt-3">Upload and verify passports, transcripts, and SOPs safely. Manage roles for students, consultants, and admins with full privacy control.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="auth-box card card-body m-0 h-100 border-0 justify-content-center">
                    <div class="mb-5 text-center">
                        <h4 class="fw-normal">Welcome to <span class="fw-bold text-primary">Dunki</span></h4>
                        <p class="text-muted mb-0">Please enter your information to access your account.</p>
                    </div>

                    <!-- ✅ Laravel Login Form (replaces static form) -->
                    <form method="POST" action="{{ route('login') }}">  <!-- Laravel login route -->
                        @csrf  <!-- CSRF protection -->

                        <!-- General Errors (shows if login fails, e.g., wrong credentials) -->
                        @if ($errors->any())
                            <div class="alert alert-danger mb-4">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Email -->
                        <div class="mb-5">
                            <label class="form-label" for="login-email">Email<span class="text-danger ms-1">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="login-email" name="email" value="{{ old('email') }}" placeholder="Enter your email" required autofocus>
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="mb-5">
                            <label class="form-label" for="LoginPassword">Password<span class="text-danger ms-1">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="LoginPassword" name="password" value="" placeholder="Enter your password" data-visible="false" required>
                                <a class="input-group-text bg-transparent toggle-password" href="javascript:;" data-target="password">
                                    <i class="ri-eye-off-line text-muted toggle-icon"></i>
                                </a>
                            </div>
                            @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Remember Me & Forgot Password -->
                        <div class="row mb-5">
                            <div class="col-sm-6">
                                <div class="form-check form-check-sm d-flex align-items-center gap-2 mb-0">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember-me" {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="remember-me">
                                        Remember me
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-6 text-end">
                                <a href="{{ route('password.request') }}" class="text-muted fs-14">  <!-- Laravel reset -->
                                    Forgot your password?
                                </a>
                            </div>
                        </div>

                        <!-- Submit Button (Laravel submit, keeps Herozi spinner) -->
                        <button type="submit" class="btn btn-primary rounded-2 w-100 btn-loader">  <!-- type="submit" triggers POST -->
                            <span class="indicator-label">Sign In</span>
                            <span class="indicator-progress flex gap-2 justify-content-center w-100">
            <span>Please Wait...</span>
            <i class="ri-loader-2-fill"></i>
        </span>
                        </button>

                        <div class="center-hr my-10 text-nowrap text-muted">Or with email</div>  <!-- Keep social if static -->

                        <div class="d-flex flex-wrap align-items-center justify-content-center gap-2">

                            <a href="https://www.instagram.com/n9rrrx/" target="_blank" class="btn btn-outline-instagram icon-btn" title="Instagram">
                                <i class="ri-instagram-fill"></i>
                            </a>

                            <a href="https://github.com/n9rrrx" target="_blank" class="btn btn-outline-dark icon-btn" title="GitHub">
                                <i class="ri-github-fill"></i>
                            </a>

                            <a href="https://x.com/n9rx2102" target="_blank" class="btn btn-outline-twitter icon-btn" title="X (Twitter)">
                                <i class="ri-twitter-fill"></i>
                            </a>

                            <a href="https://n9rrrx.github.io/Portfolio/" target="_blank" class="btn btn-outline-primary icon-btn" title="Portfolio">
                                <i class="ri-global-line"></i>
                            </a>
                        </div>

                        <p class="mb-0 mt-5 text-muted text-center">
                            Don't have an account?
                            <a href="{{ route('register') }}" class="text-primary fw-medium text-decoration-underline ms-1">  <!-- Laravel register -->
                                Sign up
                            </a>
                        </p>
                    </form>  <!-- End Laravel Login Form -->
                </div>
            </div>

        </div>
    </div>
</div>

<!-- JAVASCRIPT -->
{{--<script src="assets/js/sidebar.js"></script>--}}
<script src="assets/libs/choices.js/public/assets/scripts/choices.min.js"></script>
<script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/libs/simplebar/simplebar.min.js"></script>
<script src="assets/js/pages/scroll-top.init.js"></script>
<script src="assets/libs/sweetalert2/sweetalert2.min.js"></script>

<!-- App js -->
{{--<script src="assets/js/app.js"></script>--}}


</body>

</html>
