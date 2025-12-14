<!DOCTYPE html>
<html lang="en" class="h-100">
<head>
    <meta charset="utf-8">
    <title>Sign Up | Dunki</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="assets/images/Favicon.png">
    <script>const AUTH_LAYOUT = true;</script>
    <script src="assets/js/layout/layout-auth.js"></script>
    <script src="assets/js/layout/layout.js"></script>

    <link rel="stylesheet" href="assets/libs/choices.js/public/assets/styles/choices.min.css">
    <link href="assets/libs/simplebar/simplebar.min.css" rel="stylesheet">
    <link href="assets/css/icons.min.css" rel="stylesheet">
    <link href="assets/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet">
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/app.min.css" rel="stylesheet">
    <link href="assets/css/custom.min.css" rel="stylesheet">
</head>
<body>

<div class="account-pages">
    <img src="{{ asset('assets/images/auth/auth_bg.jpg') }}" alt="" class="auth-bg light">
    <img src="{{ asset('assets/images/auth/auth_bg_dark.jpg') }}" alt="" class="auth-bg dark">

    <div class="container">
        <div class="row gy-0 justify-content-center">

            <!-- Left Banner -->
            <div class="col-lg-6 auth-banners">
                <div class="bg-login card card-body m-0 h-100 border-0">
                    <img src="{{ asset('assets/images/auth/111.png') }}" class="img-fluid auth-banner" alt="Dunki">
                    <div class="auth-contain">
                        <div class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                <div class="carousel-item active text-center text-white my-4 p-4">
                                    <h3>Start Your Study Abroad Journey</h3>
                                    <p>Join thousands of students achieving their dreams with Dunki.</p>
                                </div>
                                <div class="carousel-item text-center text-white my-4 p-4">
                                    <h3>Track Applications Seamlessly</h3>
                                    <p>Real-time updates on university and visa applications.</p>
                                </div>
                                <div class="carousel-item text-center text-white my-4 p-4">
                                    <h3>Secure & Private</h3>
                                    <p>Your documents and data are fully protected.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Form -->
            <div class="col-lg-6">
                <div class="auth-box card card-body m-0 h-100 border-0 d-flex align-items-center">
                    <div class="w-100">

                        <div class="text-center mb-5">
                            <h4>Welcome to <span class="text-primary fw-bold">Dunki</span></h4>
                            <p class="text-muted">Create your student account in seconds</p>
                        </div>

                        <!-- Show Errors -->
                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show">
                                <strong>Please fix:</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
                            @csrf

                            <!-- Full Name -->
                            <div class="mb-4">
                                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name') }}" placeholder="John Doe" required>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <!-- Email -->
                            <div class="mb-4">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email') }}" placeholder="you@example.com" required>
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <!-- Phone -->
                            <div class="mb-4">
                                <label class="form-label">Phone Number</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}"
                                       placeholder="+92 300 1234567">
                            </div>

                            <!-- Country -->
                            <div class="mb-4">
                                <label class="form-label">Country <span class="text-danger">*</span></label>
                                <input type="text" name="country" class="form-control @error('country') is-invalid @enderror"
                                       value="{{ old('country') }}" placeholder="Pakistan" required>
                                @error('country') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <!-- City -->
                            <div class="mb-4">
                                <label class="form-label">City <span class="text-danger">*</span></label>
                                <input type="text" name="location" class="form-control @error('location') is-invalid @enderror"
                                       value="{{ old('location') }}" placeholder="Lahore" required>
                                @error('location') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <!-- Profile Picture -->
                            <div class="mb-4">
                                <label class="form-label">Profile Picture <small class="text-muted"></small></label>
                                <input type="file" name="profile_pic" class="form-control @error('profile_pic') is-invalid @enderror"
                                       accept="image/jpeg,image/png,image/jpg">
                                @error('profile_pic') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                <small class="text-muted">JPG/PNG</small>
                            </div>

                            <!-- Password -->
                            <div class="mb-4">
                                <label class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                                       placeholder="••••••••" required>
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <!-- Confirm Password -->
                            <div class="mb-4">
                                <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                <input type="password" name="password_confirmation" class="form-control" placeholder="••••••••" required>
                            </div>

                            <div class="mb-4 text-muted small">
                                By signing up, you agree to our <a href="#!" class="text-primary">Terms</a> and <a href="#!" class="text-primary">Privacy Policy</a>
                            </div>

                            <!-- Clean Button -->
                            <button type="submit" class="btn btn-primary rounded-2 w-100">
                                Create Account
                            </button>

                            <p class="text-center mt-4 text-muted">
                                Already have an account?
                                <a href="{{ route('login') }}" class="text-primary fw-medium">Sign In</a>
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/libs/simplebar/simplebar.min.js"></script>
<script src="assets/js/pages/scroll-top.init.js"></script>
</body>
</html>
