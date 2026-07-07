<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Form Kuesioner')</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/img/logo.png') }}">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css">
    <!-- Bootstrap core CSS -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Material Design Bootstrap -->
    <link href="{{ asset('assets/css/mdb.min.css') }}" rel="stylesheet">
    <!-- Custom styles -->
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">

    <!-- SweetAlert2 CSS (CDN) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- Extracted Views CSS -->
    <link href="{{ asset('css/custom-views.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body class="antialiased min-h-screen d-flex flex-column">

    <!-- Header Minimalis -->
    <header class="form-header d-flex align-items-center">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <div class="p-2 rounded bg-danger text-white mr-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                    <i class="fas fa-file-alt" style="font-size: 1.1rem;"></i>
                </div>
                <span class="h5 mb-0 font-weight-bold text-danger">Tracer Study</span>
            </div>

            <!-- User Dropdown -->
            <div class="dropdown">
                <button class="btn btn-link p-0 dropdown-toggle d-flex align-items-center text-dark font-weight-bold" type="button" id="userMenuDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="text-decoration: none;">
                    @if(Auth::user()->avatar)
                        <img src="{{ filter_var(Auth::user()->avatar, FILTER_VALIDATE_URL) ? Auth::user()->avatar : asset('storage/' . Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}" class="rounded-circle mr-2" style="width: 32px; height: 32px; object-fit: cover; border: 1px solid #ccc;">
                    @else
                        <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center mr-2 font-weight-bold" style="width: 32px; height: 32px; font-size: 0.8rem;">
                            {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                        </div>
                    @endif
                    <span class="d-none d-sm-inline" style="font-size: 0.85rem;">{{ Auth::user()->name ?? 'User' }}</span>
                </button>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userMenuDropdown" style="border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.08);">
                    <div class="dropdown-header border-bottom pb-2 mb-2">
                        <div class="font-weight-bold text-dark text-truncate" style="max-width: 160px;">{{ Auth::user()->email ?? '' }}</div>
                        <span class="role-badge role-badge-{{ Auth::user()->role }} mt-1 d-inline-block">{{ Auth::user()->role === 'alumni' ? 'Alumni' : ucfirst(Auth::user()->role) }}</span>
                    </div>
                    <a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="fas fa-user-edit mr-2 text-muted"></i> Edit Profil</a>
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger font-weight-bold"><i class="fas fa-sign-out-alt mr-2"></i> Keluar</button>
                    </form>
                </div>
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

    <!-- Core JavaScript Files -->
    <!-- jQuery -->
    <script type="text/javascript" src="{{ asset('assets/js/jquery-3.4.1.min.js') }}"></script>
    <!-- Bootstrap tooltips -->
    <script type="text/javascript" src="{{ asset('assets/js/popper.min.js') }}"></script>
    <!-- Bootstrap core JavaScript -->
    <script type="text/javascript" src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
    <!-- MDB core JavaScript -->
    <script type="text/javascript" src="{{ asset('assets/js/mdb.min.js') }}"></script>
    <!-- SweetAlert2 (CDN) -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

    <script>
        // Toaster Toast logic
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        @if(session('success'))
            Toast.fire({
                icon: 'success',
                title: '{{ session("success") }}'
            });
        @endif

        @if(session('error'))
            Toast.fire({
                icon: 'error',
                title: '{{ session("error") }}'
            });
        @endif
    </script>
    @stack('scripts')
</body>
</html>
