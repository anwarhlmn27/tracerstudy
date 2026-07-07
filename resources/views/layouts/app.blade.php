<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Tracer Study Dashboard')</title>
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

    @php
        $appSettings = \Illuminate\Support\Facades\Schema::hasTable('settings') 
            ? \App\Models\Setting::pluck('value', 'key')->toArray() 
            : [];
    @endphp
    <style>
        body {
            @if(isset($appSettings['font_family'])) font-family: {!! $appSettings['font_family'] !!} !important; @endif
            @if(isset($appSettings['font_color'])) color: {{ $appSettings['font_color'] }} !important; @endif
        }
        #sidebar {
            @if(isset($appSettings['sidebar_color'])) background-color: {{ $appSettings['sidebar_color'] }} !important; @endif
        }
        #main-header {
            @if(isset($appSettings['navbar_color'])) background-color: {{ $appSettings['navbar_color'] }} !important; @endif
        }
        #main-content {
            @if(isset($appSettings['contentbar_color'])) background-color: {{ $appSettings['contentbar_color'] }} !important; @endif
        }
        footer {
            @if(isset($appSettings['footer_color'])) background-color: {{ $appSettings['footer_color'] }} !important; @endif
        }
    </style>
</head>
<body>
    <!-- Preloader -->
    <div id="page-preloader" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 9999; background: rgba(255,255,255,0.9); display: flex; align-items: center; justify-content: center; transition: opacity 0.4s;">
        <div class="text-center">
            <div class="spinner-border text-danger" role="status" style="width: 3rem; height: 3rem;">
                <span class="sr-only">Loading...</span>
            </div>
            <div class="mt-3 font-weight-bold text-muted" style="letter-spacing: 0.15em; font-size: 0.8rem;">LOADING SYSTEM...</div>
        </div>
    </div>

    <!-- Sidebar Backdrop for Mobile -->
    <div id="sidebar-backdrop"></div>

    <!-- Sidebar -->
    <aside id="sidebar">
        <div class="sidebar-header d-flex align-items-center justify-content-center" style="height: 75px; padding: 10px 20px;">
            <div style="background-color: #ffffff; padding: 5px 15px; border-radius: 4px; display: flex; align-items: center; justify-content: center; width: 100%; height: 100%; box-shadow: 0 2px 4px rgba(0,0,0,0.08);">
                <img src="{{ asset('assets/img/logo_UHI.png') }}" alt="UHI Logo" style="max-height: 100%; max-width: 100%; object-fit: contain;">
            </div>
        </div>

        <!-- User Info -->
        <div class="sidebar-user">
            <div class="d-flex align-items-center">
                @if(Auth::user()->avatar)
                    <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}" class="rounded-circle mr-3 border border-light" style="width: 40px; height: 40px; object-fit: cover;">
                @else
                    <div class="rounded-circle bg-light text-dark d-flex align-items-center justify-content-center mr-3 font-weight-bold" style="width: 40px; height: 40px; font-size: 0.9rem;">
                        {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 2)) }}
                    </div>
                @endif
                <div class="text-truncate">
                    <div class="font-weight-bold text-white text-truncate" style="font-size: 0.85rem;">{{ Auth::user()->name ?? 'User' }}</div>
                    <span class="badge text-white px-2 py-1 mt-1" style="font-size: 9px; text-transform: uppercase; background-color: rgba(255,255,255,0.2);">
                        {{ Auth::user()->role === 'alumni' ? 'Student' : Auth::user()->role }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Sidebar Navigation -->
        <nav class="sidebar-nav">
            <div class="nav-header">Menu Utama</div>

            {{-- Dashboard: admin, dosen --}}
            @if(in_array(Auth::user()->role, ['admin', 'dosen']))
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i> Dashboard
                </a>
            @endif

            {{-- Form: alumni (student), atasan --}}
            @if(in_array(Auth::user()->role, ['alumni', 'atasan']))
                <a href="{{ route('form.create') }}" class="nav-link {{ request()->routeIs('form.create') ? 'active' : '' }}">
                    <i class="fas fa-edit"></i> Isi Kuesioner
                </a>
            @endif

            @if(in_array(Auth::user()->role, ['admin', 'dosen']))
                {{-- Data & Laporan Dropdown --}}
                @php
                    $isDataActive = request()->routeIs('univ.*', 'fakultas.*', 'prodi.*', 'alumni.*', 'questionnaires.*', 'reports.*', 'email.*');
                @endphp
                <a href="#collapseData" data-toggle="collapse" aria-expanded="{{ $isDataActive ? 'true' : 'false' }}" class="nav-link d-flex justify-content-between align-items-center mt-2 {{ $isDataActive ? '' : 'collapsed' }}">
                    <span><i class="fas fa-database text-center" style="width:24px; margin-right:6px;"></i> Data & Laporan</span>
                    <i class="fas fa-angle-down" style="transition: transform 0.3s ease-in-out;"></i>
                </a>
                <div class="collapse {{ $isDataActive ? 'show' : '' }}" id="collapseData">
                    <div class="pl-3">
                        <a href="{{ route('univ.index') }}" class="nav-link {{ request()->routeIs('univ.*') ? 'active' : '' }}">
                            <i class="fas fa-university"></i> Universitas
                        </a>
                        <a href="{{ route('fakultas.index') }}" class="nav-link {{ request()->routeIs('fakultas.*') ? 'active' : '' }}">
                            <i class="fas fa-graduation-cap"></i> Fakultas
                        </a>
                        <a href="{{ route('prodi.index') }}" class="nav-link {{ request()->routeIs('prodi.*') ? 'active' : '' }}">
                            <i class="fas fa-book-open"></i> Program Studi
                        </a>
                        <a href="{{ route('alumni.index') }}" class="nav-link {{ request()->routeIs('alumni.*') ? 'active' : '' }}">
                            <i class="fas fa-users"></i> Data Alumni
                        </a>
                        <a href="{{ route('questionnaires.index') }}" class="nav-link {{ request()->routeIs('questionnaires.*') ? 'active' : '' }}">
                            <i class="fas fa-file-alt"></i> Kuesioner Respon
                        </a>
                        <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                            <i class="fas fa-chart-pie"></i> Laporan Karir
                        </a>
                        <a href="{{ route('email.index') }}" class="nav-link {{ request()->routeIs('email.*') ? 'active' : '' }}">
                            <i class="fas fa-envelope"></i> Email Blast
                        </a>
                    </div>
                </div>

                {{-- Pengaturan Dropdown --}}
                @php
                    $isSettingsActive = request()->routeIs('master-form.*', 'settings');
                @endphp
                <a href="#collapseSettings" data-toggle="collapse" aria-expanded="{{ $isSettingsActive ? 'true' : 'false' }}" class="nav-link d-flex justify-content-between align-items-center mt-2 {{ $isSettingsActive ? '' : 'collapsed' }}">
                    <span><i class="fas fa-cog text-center" style="width:24px; margin-right:6px;"></i> Pengaturan</span>
                    <i class="fas fa-angle-down" style="transition: transform 0.3s ease-in-out;"></i>
                </a>
                <div class="collapse {{ $isSettingsActive ? 'show' : '' }}" id="collapseSettings">
                    <div class="pl-3">
                        @if(Auth::user()->role === 'admin')
                            <a href="{{ route('master-form.index') }}" class="nav-link {{ request()->routeIs('master-form.*') ? 'active' : '' }}">
                                <i class="fas fa-list-alt"></i> Master Form
                            </a>
                        @endif
                        <a href="{{ route('settings') }}" class="nav-link {{ request()->routeIs('settings') ? 'active' : '' }}">
                            <i class="fas fa-sliders-h"></i> Settings
                        </a>
                    </div>
                </div>
            @endif
        </nav>
    </aside>

    <!-- Main Wrapper -->
    <div id="main-wrapper">
        <!-- Header -->
        <header id="main-header">
            <div class="d-flex align-items-center">
                <!-- Toggler -->
                <button type="button" class="btn btn-link p-0 mr-3 text-dark sidebar-toggler" style="font-size: 1.25rem;">
                    <i class="fas fa-bars"></i>
                </button>
                <h4 class="h5 mb-0 text-danger font-weight-bold">@yield('header', 'Overview')</h4>
            </div>

            <div class="d-flex align-items-center">
                <!-- Notifications -->
                <button class="btn btn-link p-2 text-muted mr-3 position-relative" style="font-size: 1.15rem;">
                    <i class="fas fa-bell"></i>
                    <span class="badge badge-danger badge-pill position-absolute" style="top: 2px; right: 2px; font-size: 7px; padding: 3px 5px;">!</span>
                </button>

                <!-- User Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-link p-0 dropdown-toggle d-flex align-items-center text-dark font-weight-bold" type="button" id="userMenuDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="text-decoration: none;">
                        @if(Auth::user()->avatar)
                            <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}" class="rounded-circle mr-2" style="width: 32px; height: 32px; object-fit: cover; border: 1px solid #ccc;">
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
                            <span class="role-badge role-badge-{{ Auth::user()->role }} mt-1 d-inline-block">{{ Auth::user()->role === 'alumni' ? 'Student' : Auth::user()->role }}</span>
                        </div>
                        <a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="fas fa-user-edit mr-2 text-muted"></i> Profile Settings</a>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger font-weight-bold"><i class="fas fa-sign-out-alt mr-2"></i> Log Out</button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Page Content -->
        <main id="main-content">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-white py-3 text-center border-top text-muted" style="font-size: 0.8rem; font-weight: 500;">
            &copy; {{ date('Y') }} Tracer Study System. All Rights Reserved.
        </footer>
    </div>

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
        // Page preloader fade out
        $(window).on('load', function() {
            $('#page-preloader').css('opacity', '0');
            setTimeout(function() {
                $('#page-preloader').hide();
            }, 400);
        });

        // Fallback preloader removal
        setTimeout(function() {
            if ($('#page-preloader').is(':visible')) {
                $('#page-preloader').hide();
            }
        }, 3000);

        // Responsive Sidebar Toggle
        $(document).ready(function() {
            $('.sidebar-toggler').on('click', function() {
                $('body').toggleClass('sidebar-toggled');
            });
            $('#sidebar-backdrop').on('click', function() {
                $('body').removeClass('sidebar-toggled');
            });
        });

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
