<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Kuesioner Tracer Study')</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/img/logo.png') }}">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css">
    <!-- Bootstrap core CSS -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Material Design Bootstrap -->
    <link href="{{ asset('assets/css/mdb.min.css') }}" rel="stylesheet">
    <!-- Custom styles -->
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- Extracted Views CSS -->
    <link href="{{ asset('css/custom-views.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body class="antialiased min-h-screen d-flex flex-column">

    <!-- Header Minimalis (No Auth Required) -->
    <header class="public-form-header d-flex align-items-center">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <img src="{{ asset('assets/img/logo_UHI.png') }}" alt="Logo" style="height: 38px; object-fit: contain;" class="mr-2">
                <span class="h6 mb-0 font-weight-bold text-danger">Tracer Study</span>
            </div>
            <div>
                <a href="{{ route('login') }}" class="btn btn-outline-danger btn-sm font-weight-bold m-0" style="border-radius: 8px; font-size: 0.8rem;">
                    <i class="fas fa-sign-in-alt mr-1"></i> Login
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-grow-1 py-5">
        <div class="container" style="max-width: 800px;">
            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="py-4 text-center text-muted border-top bg-white" style="font-size: 0.8rem; font-weight: 500;">
        &copy; {{ date('Y') }} Tracer Study System. All Rights Reserved.
    </footer>

    <!-- Core JavaScript -->
    <script type="text/javascript" src="{{ asset('assets/js/jquery-3.4.1.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/popper.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/mdb.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3500,
            timerProgressBar: true,
        });

        @if(session('success'))
            Toast.fire({ icon: 'success', title: '{{ session("success") }}' });
        @endif
        @if(session('error'))
            Toast.fire({ icon: 'error', title: '{{ session("error") }}' });
        @endif
    </script>
    @stack('scripts')
</body>
</html>
