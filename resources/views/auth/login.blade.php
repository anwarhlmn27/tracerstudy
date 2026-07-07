<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Tracer Study</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css">
    <!-- Bootstrap core CSS -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Material Design Bootstrap -->
    <link href="{{ asset('assets/css/mdb.min.css') }}" rel="stylesheet">
    <!-- Custom styles -->
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">

    <!-- Extracted Views CSS -->
    <link href="{{ asset('css/custom-views.css') }}" rel="stylesheet">
</head>
<body class="login-page">

    <div class="login-container">
        <div class="row no-gutters h-100 min-vh-50">
            <!-- Left Side: Login Form -->
            <div class="col-lg-6 login-left">
                <div class="max-w-md w-100 mx-auto px-lg-4">
                    <h1 class="h2 font-weight-bold brand-text mb-1">Welcome Back!</h1>
                    <p class="text-muted mb-4" style="font-size: 0.9rem;">Please Log in to your account.</p>

                    <form method="POST" action="{{ route('login') }}" class="mt-2">
                        @csrf
                        
                        @if($errors->any())
                            <div class="alert alert-danger border-0 rounded-lg p-3 mb-4" style="font-size: 0.85rem; background-color: #fff5f5; color: #c53030;">
                                <ul class="list-unstyled mb-0 pl-0">
                                    @foreach ($errors->all() as $error)
                                        <li><i class="fas fa-exclamation-circle mr-2"></i>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Email Input -->
                        <div class="md-form md-outline mb-4">
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus class="form-control" style="border-radius: 12px; font-size: 0.9rem;">
                            <label for="email">Email Address</label>
                        </div>

                        <!-- Password Input -->
                        <div class="md-form md-outline mb-4">
                            <input type="password" name="password" id="password" required class="form-control" style="border-radius: 12px; font-size: 0.9rem;">
                            <label for="password">Password</label>
                        </div>

                        <!-- Remember me & Forgot Password -->
                        <div class="d-flex justify-content-between align-items-center mb-4" style="font-size: 0.85rem;">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="remember" name="remember">
                                <label class="custom-control-label font-weight-bold text-dark" for="remember" style="cursor: pointer;">Remember me</label>
                            </div>
                            <a href="#" class="font-weight-bold text-danger" style="text-decoration: none;">Forgot password?</a>
                        </div>

                        <!-- Action Buttons -->
                        <div class="form-row mt-4">
                            <div class="col-sm-6 mb-2">
                                <button type="submit" class="btn btn-maroon btn-block m-0">Login</button>
                            </div>
                            <div class="col-sm-6 mb-2">
                                <button type="button" class="btn btn-outline-maroon btn-block m-0">Create account</button>
                            </div>
                        </div>
                    </form>

                    <!-- Footer Info Text -->
                    <p class="text-muted mt-5 mb-0" style="font-size: 0.75rem; line-height: 1.5;">
                        By signing up you agree to our terms and that you have read our data policy.
                    </p>
                </div>
            </div>

            <!-- Right Side: Graphic Illustration -->
            <div class="col-lg-6 login-right d-none d-lg-flex">
                <div class="text-center px-4">
                    <img src="{{ asset('assets/img/banner.jpg') }}" 
                         alt="Banner Image" 
                         class="img-fluid drop-shadow-xl hover-scale transition-all" 
                         style="max-width: 100%; height: auto; transition: transform 0.5s; cursor: pointer;"
                         onmouseover="this.style.transform='scale(1.05)'"
                         onmouseout="this.style.transform='scale(1)'">
                </div>
            </div>
        </div>
    </div>

    <!-- Core Javascript Files -->
    <script type="text/javascript" src="{{ asset('assets/js/jquery-3.4.1.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/popper.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/mdb.min.js') }}"></script>

</body>
</html>
