@extends(in_array(Auth::user()->role, ['alumni', 'atasan']) ? 'layouts.form' : 'layouts.app')

@section('title', 'Profile Settings - Tracer Study')
@section('header', 'Profile Settings')

@section('content')


<div class="container-fluid" style="max-width: 900px; margin: 0 auto;">
    @if(in_array(Auth::user()->role, ['alumni', 'atasan']))
        <div class="mb-4">
            <a href="{{ route('form.create') }}" class="btn btn-outline-danger btn-md font-weight-bold m-0" style="border-radius: 8px;">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>
    @endif
    
    <div class="card profile-card">
        <!-- Header Section -->
        <div class="card-header bg-light py-4 px-4 px-md-5" style="border-top-left-radius: 15px; border-top-right-radius: 15px;">
            <div class="d-flex flex-column flex-sm-row align-items-center">
                <!-- Avatar placeholder -->
                <div class="avatar-wrapper mb-3 mb-sm-0 mr-sm-4">
                    @if($user->avatar)
                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                        <span class="font-weight-bold text-danger h2 mb-0">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                    @endif
                </div>
                
                <div class="text-center text-sm-left">
                    <h4 class="font-weight-bold text-dark mb-1">{{ $user->name }}</h4>
                    <p class="text-muted small mb-2"><i class="fas fa-envelope mr-1"></i> {{ $user->email }}</p>
                    <span class="badge badge-danger text-capitalize px-3 py-1.5 rounded-pill font-weight-bold" style="font-size: 10px;">
                        Role: {{ $user->role === 'alumni' ? 'Student' : $user->role }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Form Section -->
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="card-body p-4 p-md-5">
            @csrf
            @push('scripts')
                <!-- Empty scripts section placeholder just in case -->
            @endpush
            @method('PUT')

            <div class="row">
                <!-- Avatar File Input -->
                <div class="col-12 mb-4">
                    <label for="avatar" class="font-weight-bold text-dark small mb-1">Foto Profil</label>
                    <input type="file" name="avatar" id="avatar" class="form-control" accept="image/*" style="border-radius: 8px; padding-top: 6px;">
                    <small class="text-muted d-block mt-1">Format yang didukung: JPG, JPEG, PNG, GIF. Maksimal 2MB.</small>
                    @error('avatar')
                        <small class="text-danger font-weight-bold mt-1 d-block">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Name Field -->
                <div class="col-md-6 mb-4">
                    <label for="name" class="font-weight-bold text-dark small mb-1">Nama Lengkap</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-light" style="border-radius: 8px 0 0 8px;"><i class="fas fa-user text-muted"></i></span>
                        </div>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" class="form-control" required style="border-radius: 0 8px 8px 0;">
                    </div>
                    @error('name')
                        <small class="text-danger font-weight-bold mt-1 d-block">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Email Field -->
                <div class="col-md-6 mb-4">
                    <label for="email" class="font-weight-bold text-dark small mb-1">Email Address</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-light" style="border-radius: 8px 0 0 8px;"><i class="fas fa-envelope text-muted"></i></span>
                        </div>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" class="form-control" required style="border-radius: 0 8px 8px 0;">
                    </div>
                    @error('email')
                        <small class="text-danger font-weight-bold mt-1 d-block">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            @if($user->role === 'alumni')
                <hr class="my-4">
                
                <div class="mb-4">
                    <h5 class="font-weight-bold text-danger mb-1">Informasi Alumni (Tracer Study)</h5>
                    <p class="text-muted small">Lengkapi data akademik dan pekerjaan Anda untuk mendukung basis data tracer study.</p>

                    <!-- Academic Info (Editable) -->
                    <div class="row mt-3">
                        <!-- NIM -->
                        <div class="col-md-4 mb-3">
                            <label for="nim" class="font-weight-bold text-dark small mb-1">NIM (Nomor Induk Mahasiswa)</label>
                            <input type="text" name="nim" id="nim" value="{{ old('nim', $user->student->nim ?? '') }}" class="form-control" placeholder="Masukkan NIM Anda" required style="border-radius: 8px;">
                            @error('nim')
                                <small class="text-danger font-weight-bold mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Program Studi -->
                        <div class="col-md-4 mb-3">
                            <label for="prodi_id" class="font-weight-bold text-dark small mb-1">Program Studi</label>
                            <select name="prodi_id" id="prodi_id" class="form-control browser-default custom-select" required style="border-radius: 8px;">
                                <option value="">Pilih Program Studi</option>
                                @foreach($prodis as $prodi)
                                    <option value="{{ $prodi->id }}" {{ old('prodi_id', $user->student->prodi_id ?? '') === $prodi->id ? 'selected' : '' }}>{{ $prodi->nama_prodi }}</option>
                                @endforeach
                            </select>
                            @error('prodi_id')
                                <small class="text-danger font-weight-bold mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Angkatan -->
                        <div class="col-md-4 mb-3">
                            <label for="angkatan" class="font-weight-bold text-dark small mb-1">Angkatan</label>
                            <input type="number" name="angkatan" id="angkatan" min="2000" max="2099" value="{{ old('angkatan', $user->student->angkatan ?? '') }}" class="form-control" placeholder="Contoh: 2020" required style="border-radius: 8px;">
                            @error('angkatan')
                                <small class="text-danger font-weight-bold mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <!-- Status Alumni -->
                        <div class="col-md-4 mb-3">
                            <label for="status_alumni" class="font-weight-bold text-dark small mb-1">Status Alumni</label>
                            <select name="status_alumni" id="status_alumni" onchange="toggleProfileCompanyFields()" class="form-control browser-default custom-select" required style="border-radius: 8px;">
                                <option value="">Pilih Status Alumni</option>
                                <option value="Bekerja (full time / part time)" {{ old('status_alumni', $user->student->status_alumni ?? '') === 'Bekerja (full time / part time)' ? 'selected' : '' }}>Bekerja (full time / part time)</option>
                                <option value="Wiraswasta" {{ old('status_alumni', $user->student->status_alumni ?? '') === 'Wiraswasta' ? 'selected' : '' }}>Wiraswasta</option>
                                <option value="Melanjutkan Pendidikan" {{ old('status_alumni', $user->student->status_alumni ?? '') === 'Melanjutkan Pendidikan' ? 'selected' : '' }}>Melanjutkan Pendidikan</option>
                                <option value="Tidak kerja tetapi sedang mencari kerja" {{ old('status_alumni', $user->student->status_alumni ?? '') === 'Tidak kerja tetapi sedang mencari kerja' ? 'selected' : '' }}>Tidak kerja tetapi sedang mencari kerja</option>
                                <option value="Belum memungkinkan bekerja" {{ old('status_alumni', $user->student->status_alumni ?? '') === 'Belum memungkinkan bekerja' ? 'selected' : '' }}>Belum memungkinkan bekerja</option>
                            </select>
                            @error('status_alumni')
                                <small class="text-danger font-weight-bold mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Waktu Tunggu Kerja -->
                        <div class="col-md-4 mb-3">
                            <label for="waktu_tunggu_kerja" class="font-weight-bold text-dark small mb-1">Waktu Tunggu Pekerjaan</label>
                            <input type="text" name="waktu_tunggu_kerja" id="waktu_tunggu_kerja" value="{{ old('waktu_tunggu_kerja', $user->student->waktu_tunggu_kerja ?? '') }}" class="form-control" placeholder="Contoh: 3 bulan, langsung" style="border-radius: 8px;">
                            @error('waktu_tunggu_kerja')
                                <small class="text-danger font-weight-bold mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Response Rate -->
                        <div class="col-md-4 mb-3">
                            <label for="response_rate" class="font-weight-bold text-dark small mb-1">Response Rate (%)</label>
                            <input type="number" name="response_rate" id="response_rate" min="0" max="100" value="{{ old('response_rate', $user->student->response_rate ?? '') }}" class="form-control" placeholder="Contoh: 85" style="border-radius: 8px;">
                            @error('response_rate')
                                <small class="text-danger font-weight-bold mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <!-- Company specific fields -->
                    <div id="profile_company_fields" class="row bg-light p-3 border rounded mx-0 mb-3" style="display: none;">
                        <div class="col-md-4 mb-2">
                            <label for="nama_perusahaan" class="font-weight-bold text-dark small mb-1">Nama Perusahaan</label>
                            <input type="text" name="nama_perusahaan" id="nama_perusahaan" value="{{ old('nama_perusahaan', $user->student->nama_perusahaan ?? '') }}" class="form-control form-control-sm" placeholder="Nama perusahaan" style="border-radius: 6px;">
                            @error('nama_perusahaan')
                                <small class="text-danger font-weight-bold mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-2">
                            <label for="jabatan" class="font-weight-bold text-dark small mb-1">Jabatan</label>
                            <input type="text" name="jabatan" id="jabatan" value="{{ old('jabatan', $user->student->jabatan ?? '') }}" class="form-control form-control-sm" placeholder="Jabatan" style="border-radius: 6px;">
                            @error('jabatan')
                                <small class="text-danger font-weight-bold mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-2">
                            <label for="tempat_kerja" class="font-weight-bold text-dark small mb-1">Tempat Bekerja</label>
                            <select name="tempat_kerja" id="tempat_kerja" class="form-control browser-default custom-select custom-select-sm" style="border-radius: 6px;">
                                <option value="">Pilih Tempat Kerja</option>
                                <option value="Lokal" {{ old('tempat_kerja', $user->student->tempat_kerja ?? '') === 'Lokal' ? 'selected' : '' }}>Lokal</option>
                                <option value="Nasional" {{ old('tempat_kerja', $user->student->tempat_kerja ?? '') === 'Nasional' ? 'selected' : '' }}>Nasional</option>
                                <option value="Multinasional" {{ old('tempat_kerja', $user->student->tempat_kerja ?? '') === 'Multinasional' ? 'selected' : '' }}>Multinasional</option>
                            </select>
                            @error('tempat_kerja')
                                <small class="text-danger font-weight-bold mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>
            @endif

            <hr class="my-4">

            <div>
                <h5 class="font-weight-bold text-dark mb-1">Ubah Password</h5>
                <p class="text-muted small">Kosongkan jika Anda tidak ingin mengubah password saat ini.</p>
                
                <div class="row mt-3">
                    <!-- Password Field -->
                    <div class="col-md-6 mb-3">
                        <label for="password" class="font-weight-bold text-dark small mb-1">Password Baru</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light" style="border-radius: 8px 0 0 8px;"><i class="fas fa-lock text-muted"></i></span>
                            </div>
                            <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" style="border-radius: 0 8px 8px 0;">
                        </div>
                        @error('password')
                            <small class="text-danger font-weight-bold mt-1 d-block">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Confirm Password Field -->
                    <div class="col-md-6 mb-3">
                        <label for="password_confirmation" class="font-weight-bold text-dark small mb-1">Konfirmasi Password Baru</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light" style="border-radius: 8px 0 0 8px;"><i class="fas fa-shield-alt text-muted"></i></span>
                            </div>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="••••••••" style="border-radius: 0 8px 8px 0;">
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-4 border-top mt-4 d-flex justify-content-end">
                <button type="submit" class="btn btn-danger btn-md font-weight-bold m-0 d-flex align-items-center" style="border-radius: 8px;">
                    <i class="fas fa-save mr-2"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@if($user->role === 'alumni')
@push('scripts')
<script>
    function toggleProfileCompanyFields() {
        const status = document.getElementById('status_alumni').value;
        const compFields = document.getElementById('profile_company_fields');
        if (!compFields) return;
        if (status === 'Bekerja (full time / part time)' || status === 'Wiraswasta') {
            $(compFields).slideDown();
        } else {
            $(compFields).slideUp();
        }
    }

    // Run when the DOM is fully loaded to set the correct initial state
    $(document).ready(function() {
        toggleProfileCompanyFields();
    });
</script>
@endpush
@endif
