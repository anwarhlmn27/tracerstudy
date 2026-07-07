@extends('layouts.app')

@section('title', 'Settings - Tracer Study')
@section('header', 'Settings')

@section('content')


<div class="container-fluid" style="max-width: 900px; margin: 0 auto;">

    <!-- Profile Section -->
    <div class="card settings-card mb-4">
        <div class="card-body p-4">
            <div class="border-bottom pb-3 mb-4">
                <h5 class="font-weight-bold text-dark mb-1">Profil Pengguna</h5>
                <p class="text-muted small mb-0">Informasi akun dan pengaturan profil</p>
            </div>
            
            <div class="d-flex align-items-center">
                <div class="rounded bg-danger text-white d-flex align-items-center justify-content-center mr-4 font-weight-bold text-uppercase" style="width: 70px; height: 70px; font-size: 1.8rem; box-shadow: 0 4px 15px rgba(128, 0, 0, 0.2);">
                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 2)) }}
                </div>
                <div>
                    <h5 class="font-weight-bold text-dark mb-1">{{ Auth::user()->name ?? 'User' }}</h5>
                    <p class="text-muted small mb-2">{{ Auth::user()->email ?? '' }}</p>
                    <span class="badge text-capitalize" style="
                        @if(Auth::user()->role === 'admin') background-color: rgba(124, 58, 237, 0.15); color: #7c3aed;
                        @elseif(Auth::user()->role === 'alumni') background-color: rgba(5, 150, 105, 0.15); color: #059669;
                        @else background-color: rgba(217, 119, 6, 0.15); color: #d97706;
                        @endif
                        font-size: 10px; font-weight: 700; padding: 5px 10px; border-radius: 50px;
                    ">
                        {{ Auth::user()->role === 'alumni' ? 'Student' : Auth::user()->role }}
                    </span>
                </div>
            </div>
            
            <div class="mt-4 pt-3 border-top">
                <a href="{{ route('profile.edit') }}" class="btn btn-danger btn-md font-weight-bold m-0" style="border-radius: 8px;">
                    <i class="fas fa-user-edit mr-2"></i> Edit Profil
                </a>
            </div>
        </div>
    </div>

    <!-- System Information -->
    <!-- <div class="card settings-card mb-4">
        <div class="card-body p-4">
            <div class="border-bottom pb-3 mb-4">
                <h5 class="font-weight-bold text-dark mb-1">Informasi Sistem</h5>
                <p class="text-muted small mb-0">Detail sistem Tracer Study</p>
            </div>
            
            <div class="row">
                <div class="col-sm-6 mb-3">
                    <div class="bg-light rounded p-3 border">
                        <small class="text-muted text-uppercase font-weight-bold d-block" style="font-size: 0.65rem; letter-spacing: 0.05em;">Versi Sistem</small>
                        <span class="font-weight-bold text-dark mt-1 d-block" style="font-size: 0.9rem;">Tracer Study v1.0</span>
                    </div>
                </div>
                <div class="col-sm-6 mb-3">
                    <div class="bg-light rounded p-3 border">
                        <small class="text-muted text-uppercase font-weight-bold d-block" style="font-size: 0.65rem; letter-spacing: 0.05em;">Framework</small>
                        <span class="font-weight-bold text-dark mt-1 d-block" style="font-size: 0.9rem;">Laravel {{ app()->version() }}</span>
                    </div>
                </div>
                <div class="col-sm-6 mb-3">
                    <div class="bg-light rounded p-3 border">
                        <small class="text-muted text-uppercase font-weight-bold d-block" style="font-size: 0.65rem; letter-spacing: 0.05em;">PHP Version</small>
                        <span class="font-weight-bold text-dark mt-1 d-block" style="font-size: 0.9rem;">{{ phpversion() }}</span>
                    </div>
                </div>
                <div class="col-sm-6 mb-3">
                    <div class="bg-light rounded p-3 border">
                        <small class="text-muted text-uppercase font-weight-bold d-block" style="font-size: 0.65rem; letter-spacing: 0.05em;">Terakhir Login</small>
                        <span class="font-weight-bold text-dark mt-1 d-block" style="font-size: 0.9rem;">{{ now()->format('d M Y, H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div> -->

    <!-- Access Control Info -->
    <!-- <div class="card settings-card">
        <div class="card-body p-4">
            <div class="border-bottom pb-3 mb-4">
                <h5 class="font-weight-bold text-dark mb-1">Hak Akses Role</h5>
                <p class="text-muted small mb-0">Daftar menu yang tersedia berdasarkan role</p>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light text-muted">
                        <tr>
                            <th class="border-0 px-4">Menu</th>
                            <th class="border-0 text-center">Admin</th>
                            <th class="border-0 text-center">Student</th>
                            <th class="border-0 text-center">Dosen</th>
                            <th class="border-0 text-center">Atasan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $menus = [
                                ['Dashboard', true, false, true, false],
                                ['Master Form', true, false, false, false],
                                ['Form', false, true, false, true],
                                ['Alumni Data', true, false, true, false],
                                ['Questionnaires', true, false, true, false],
                                ['Reports & Analytics', true, false, true, false],
                                ['Settings', true, false, true, false],
                            ];
                        @endphp
                        @foreach($menus as $menu)
                        <tr>
                            <td class="px-4 align-middle font-weight-bold text-dark">{{ $menu[0] }}</td>
                            <td class="align-middle text-center">
                                @if($menu[1])
                                    <i class="fas fa-check-circle text-success" style="font-size: 1.2rem;"></i>
                                @else
                                    <i class="fas fa-times-circle text-danger" style="font-size: 1.2rem;"></i>
                                @endif
                            </td>
                            <td class="align-middle text-center">
                                @if($menu[2])
                                    <i class="fas fa-check-circle text-success" style="font-size: 1.2rem;"></i>
                                @else
                                    <i class="fas fa-times-circle text-danger" style="font-size: 1.2rem;"></i>
                                @endif
                            </td>
                            <td class="align-middle text-center">
                                @if($menu[3])
                                    <i class="fas fa-check-circle text-success" style="font-size: 1.2rem;"></i>
                                @else
                                    <i class="fas fa-times-circle text-danger" style="font-size: 1.2rem;"></i>
                                @endif
                            </td>
                            <td class="align-middle text-center">
                                @if(isset($menu[4]) && $menu[4])
                                    <i class="fas fa-check-circle text-success" style="font-size: 1.2rem;"></i>
                                @else
                                    <i class="fas fa-times-circle text-danger" style="font-size: 1.2rem;"></i>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div> -->

    @if(Auth::user()->role === 'admin')
    <!-- Appearance Settings -->
    <div class="card settings-card mt-4 mb-4">
        <div class="card-body p-4">
            <div class="border-bottom pb-3 mb-4">
                <h5 class="font-weight-bold text-dark mb-1">Pengaturan Tampilan (Appearance)</h5>
                <p class="text-muted small mb-0">Sesuaikan warna dan font antarmuka aplikasi</p>
            </div>
            
            <form action="{{ route('settings.update') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold text-dark small mb-1" for="sidebar_color">Warna Sidebar</label>
                        <input type="color" class="form-control" id="sidebar_color" name="sidebar_color" value="{{ $settings['sidebar_color'] ?? '#800000' }}" style="height: 40px; padding: 2px;">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold text-dark small mb-1" for="navbar_color">Warna Navbar (Header)</label>
                        <input type="color" class="form-control" id="navbar_color" name="navbar_color" value="{{ $settings['navbar_color'] ?? '#ffffff' }}" style="height: 40px; padding: 2px;">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold text-dark small mb-1" for="contentbar_color">Warna Background Konten</label>
                        <input type="color" class="form-control" id="contentbar_color" name="contentbar_color" value="{{ $settings['contentbar_color'] ?? '#f7f8fa' }}" style="height: 40px; padding: 2px;">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold text-dark small mb-1" for="footer_color">Warna Background Footer</label>
                        <input type="color" class="form-control" id="footer_color" name="footer_color" value="{{ $settings['footer_color'] ?? '#ffffff' }}" style="height: 40px; padding: 2px;">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold text-dark small mb-1" for="font_family">Font Family</label>
                        <select class="form-control browser-default custom-select" id="font_family" name="font_family" style="border-radius: 8px;">
                            <option value="'Roboto', sans-serif" {{ ($settings['font_family'] ?? '') == "'Roboto', sans-serif" ? 'selected' : '' }}>Roboto</option>
                            <option value="'Inter', sans-serif" {{ ($settings['font_family'] ?? '') == "'Inter', sans-serif" ? 'selected' : '' }}>Inter</option>
                            <option value="'Open Sans', sans-serif" {{ ($settings['font_family'] ?? '') == "'Open Sans', sans-serif" ? 'selected' : '' }}>Open Sans</option>
                            <option value="'Poppins', sans-serif" {{ ($settings['font_family'] ?? '') == "'Poppins', sans-serif" ? 'selected' : '' }}>Poppins</option>
                            <option value="'Nunito', sans-serif" {{ ($settings['font_family'] ?? '') == "'Nunito', sans-serif" ? 'selected' : '' }}>Nunito</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold text-dark small mb-1" for="font_color">Warna Teks (Default)</label>
                        <input type="color" class="form-control" id="font_color" name="font_color" value="{{ $settings['font_color'] ?? '#212529' }}" style="height: 40px; padding: 2px;">
                    </div>
                </div>
                <div class="mt-3 text-right">
                    <button type="submit" class="btn btn-danger font-weight-bold" style="border-radius: 8px;">Simpan Pengaturan</button>
                </div>
            </form>
        </div>
    </div>
    @endif


</div>
@endsection
