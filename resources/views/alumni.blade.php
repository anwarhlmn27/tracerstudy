@extends('layouts.app')

@section('title', 'Alumni Data - Tracer Study')
@section('header', 'Alumni Data')

@section('content')


<div class="container-fluid">
    <div class="card alumni-card">
        <div class="card-body p-0">
            <!-- Card Title & Buttons -->
            <div class="px-4 py-4 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center border-bottom">
                <div>
                    <h5 class="font-weight-bold text-dark mb-1">Daftar Data Alumni</h5>
                    <p class="text-muted small mb-0">Kelola data alumni mahasiswa di sini</p>
                </div>
                <div class="d-flex flex-wrap mt-3 mt-sm-0" style="gap: 0.5rem;">
                    <a href="{{ route('alumni.template') }}" class="btn btn-outline-success btn-md font-weight-bold m-0" style="border-radius: 10px;">
                        <i class="fas fa-file-excel mr-2"></i> Download Template
                    </a>
                    <button type="button" class="btn btn-outline-danger btn-md font-weight-bold m-0" onclick="openImportModal()" style="border-radius: 10px;">
                        <i class="fas fa-file-upload mr-2"></i> Import Excel
                    </button>
                    <button type="button" class="btn btn-danger btn-md font-weight-bold m-0" onclick="openAddModal()" style="border-radius: 10px;">
                        <i class="fas fa-plus mr-2"></i> Tambah Alumni
                    </button>
                </div>
            </div>

            {{-- Flash Messages --}}
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mx-4 mt-3 mb-0" role="alert" style="border-radius:10px; font-size:0.88rem;">
                <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
            @endif
            @if(session('import_warning'))
            <div class="alert alert-warning alert-dismissible fade show mx-4 mt-3 mb-0" role="alert" style="border-radius:10px; font-size:0.88rem;">
                <i class="fas fa-exclamation-triangle mr-2"></i> {{ session('import_warning') }}
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
            @endif
            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mx-4 mt-3 mb-0" role="alert" style="border-radius:10px; font-size:0.88rem;">
                <i class="fas fa-times-circle mr-2"></i> {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
            @endif
            @if(session('import_errors') && count(session('import_errors')) > 0)
            <div class="mx-4 mt-2 mb-0">
                <div class="card border-warning" style="border-radius:10px;">
                    <div class="card-header bg-warning text-white py-2 px-3" style="border-radius:10px 10px 0 0; font-size:0.82rem; font-weight:700;">
                        <i class="fas fa-exclamation-circle mr-1"></i> Detail Baris yang Dilewati
                    </div>
                    <div class="card-body py-2 px-3" style="max-height:160px; overflow-y:auto;">
                        <ul class="mb-0 pl-3" style="font-size:0.8rem;">
                            @foreach(session('import_errors') as $err)
                                <li class="text-danger">{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            @endif

            <!-- Table Wrapper -->
            <div class="table-responsive p-3">
                <table id="alumniTable" class="table table-hover w-100">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 px-3">No</th>
                            <th class="border-0">NIM</th>
                            <th class="border-0">Nama</th>
                            <th class="border-0">Prodi & Angkatan</th>
                            <th class="border-0">Status Alumni</th>
                            <th class="border-0">Pekerjaan</th>
                            <th class="border-0">Waktu Tunggu</th>
                            <th class="border-0">Response Rate</th>
                            <th class="border-0 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $index => $student)
                        @php
                            $alumniStatusColors = [
                                'aktif' => 'background-color: rgba(0, 123, 255, 0.15) !important; color: #007bff !important;',
                                'lulus' => 'background-color: rgba(40, 167, 69, 0.15) !important; color: #28a745 !important;',
                                'cuti' => 'background-color: rgba(255, 193, 7, 0.15) !important; color: #ffc107 !important;',
                                'drop_out' => 'background-color: rgba(220, 53, 69, 0.15) !important; color: #dc3545 !important;',
                                'Bekerja (full time / part time)' => 'background-color: rgba(40, 167, 69, 0.15) !important; color: #28a745 !important;',
                                'Wiraswasta' => 'background-color: rgba(102, 16, 242, 0.15) !important; color: #6610f2 !important;',
                                'Melanjutkan Pendidikan' => 'background-color: rgba(111, 66, 193, 0.15) !important; color: #6f42c1 !important;',
                                'Tidak kerja tetapi sedang mencari kerja' => 'background-color: rgba(253, 126, 20, 0.15) !important; color: #fd7e14 !important;',
                                'Belum memungkinkan bekerja' => 'background-color: rgba(232, 62, 140, 0.15) !important; color: #e83e8c !important;',
                            ];
                            $statusBadgeColors = [
                                'bekerja' => 'background-color: rgba(40, 167, 69, 0.15) !important; color: #28a745 !important;',
                                'wiraswasta' => 'background-color: rgba(102, 16, 242, 0.15) !important; color: #6610f2 !important;',
                                'studi_lanjut' => 'background-color: rgba(111, 66, 193, 0.15) !important; color: #6f42c1 !important;',
                                'mencari_kerja' => 'background-color: rgba(253, 126, 20, 0.15) !important; color: #fd7e14 !important;',
                                'belum_memungkinkan' => 'background-color: rgba(232, 62, 140, 0.15) !important; color: #e83e8c !important;',
                            ];
                            $displayStatus = $student->status_alumni;
                            $badgeStyle = $alumniStatusColors[$displayStatus] ?? ($statusBadgeColors[$displayStatus] ?? 'background-color: rgba(108, 117, 125, 0.15); color: #6c757d;');
                        @endphp
                        <tr>
                            <td class="px-3 align-middle font-weight-bold text-dark">{{ $index + 1 }}</td>
                            <td class="align-middle font-weight-bold text-muted" style="font-family: monospace; font-size: 0.8rem;">{{ $student->nim }}</td>
                            <td class="align-middle">
                                <div class="d-flex align-items-center">
                                    @if(isset($student->user->avatar) && $student->user->avatar)
                                        <img src="{{ filter_var($student->user->avatar, FILTER_VALIDATE_URL) ? $student->user->avatar : asset('storage/' . $student->user->avatar) }}" alt="{{ $student->nama_student }}" class="rounded-circle mr-2" style="width: 32px; height: 32px; object-fit: cover; border: 1px solid #ddd;">
                                    @else
                                        <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center mr-2 font-weight-bold text-uppercase" style="width: 32px; height: 32px; font-size: 0.75rem;">
                                            {{ strtoupper(substr($student->nama_student, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-weight-bold text-dark text-truncate" style="max-width: 150px;">{{ $student->nama_student }}</div>
                                        <small class="text-muted">{{ $student->user->email ?? '-' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="align-middle">
                                <div class="font-weight-bold text-dark">{{ $student->prodi->nama_prodi ?? '-' }}</div>
                                <small class="text-muted">Angkatan <span class="font-weight-bold">{{ $student->angkatan }}</span></small>
                            </td>
                            <td class="align-middle">
                                @if($student->status_alumni)
                                    <span class="badge badge-status" style="{{ $badgeStyle }}">
                                        {{ $student->status_alumni }}
                                    </span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="align-middle">
                                @if($student->nama_perusahaan || $student->jabatan)
                                    <div class="font-weight-bold text-dark">{{ $student->nama_perusahaan ?? '-' }}</div>
                                    <small class="text-muted">
                                        {{ $student->jabatan ?? '-' }}
                                        @if($student->tempat_kerja)
                                            <span class="ml-1 badge badge-light font-weight-bold text-uppercase">{{ $student->tempat_kerja }}</span>
                                        @endif
                                    </small>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="align-middle text-muted">{{ $student->waktu_tunggu_kerja ?? '-' }}</td>
                            <td class="align-middle">
                                @if(isset($student->response_rate))
                                    <div class="d-flex align-items-center">
                                        <span class="font-weight-bold mr-2 text-dark" style="font-size: 0.8rem;">{{ $student->response_rate }}%</span>
                                        <div class="progress" style="width: 60px; height: 5px;">
                                            <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $student->response_rate }}%" aria-valuenow="{{ $student->response_rate }}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="align-middle text-center">
                                <div class="d-flex justify-content-center align-items-center">
                                    <button class="btn btn-link p-1 text-primary mr-1" onclick="openEditModal('{{ $student->id }}', '{{ addslashes($student->nama_student) }}', '{{ $student->nim }}', '{{ $student->prodi_id }}', '{{ $student->angkatan }}', '{{ $student->status }}', '{{ addslashes($student->status_alumni ?? '') }}', '{{ addslashes($student->nama_perusahaan ?? '') }}', '{{ addslashes($student->jabatan ?? '') }}', '{{ addslashes($student->tempat_kerja ?? '') }}', '{{ $student->response_rate ?? '' }}', '{{ addslashes($student->waktu_tunggu_kerja ?? '') }}')" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-link p-1 text-danger" onclick="confirmDelete('{{ $student->id }}', '{{ addslashes($student->nama_student) }}')" title="Hapus">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="py-5 text-center text-muted">
                                <i class="fas fa-users-slash fa-2x mb-3 d-block"></i>
                                Belum ada data alumni.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addAlumniModal" tabindex="-1" role="dialog" aria-labelledby="addAlumniModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="{{ route('alumni.store') }}" method="POST" id="addForm">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title font-weight-bold" id="addAlumniModalLabel">Tambah Data Alumni</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="font-weight-bold text-dark small mb-1" for="add_nama">Nama Lengkap</label>
                            <input id="add_nama" name="nama_student" type="text" class="form-control" placeholder="Masukkan nama lengkap" required style="border-radius: 8px;">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold text-dark small mb-1" for="add_nim">NIM</label>
                            <input id="add_nim" name="nim" type="text" class="form-control" placeholder="Contoh: 12345678" required style="border-radius: 8px;">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold text-dark small mb-1" for="add_email">Email</label>
                            <input id="add_email" name="email" type="email" class="form-control" placeholder="email@example.com" required style="border-radius: 8px;">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold text-dark small mb-1" for="add_prodi">Program Studi</label>
                            <select id="add_prodi" name="prodi_id" class="form-control browser-default custom-select" required style="border-radius: 8px;">
                                <option value="">Pilih Prodi</option>
                                @foreach($prodis as $prodi)
                                    <option value="{{ $prodi->id }}">{{ $prodi->nama_prodi }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold text-dark small mb-1" for="add_angkatan">Angkatan</label>
                            <input id="add_angkatan" name="angkatan" type="number" min="2000" max="2099" class="form-control" placeholder="Contoh: 2024" required style="border-radius: 8px;">
                        </div>
                        <input type="hidden" id="add_status" name="status" value="lulus">
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold text-dark small mb-1" for="add_status_alumni">Status Alumni</label>
                            <select id="add_status_alumni" name="status_alumni" onchange="toggleAddCompanyFields()" class="form-control browser-default custom-select" style="border-radius: 8px;">
                                <option value="">Pilih Status Alumni</option>
                                <option value="Bekerja (full time / part time)">Bekerja (full time / part time)</option>
                                <option value="Wiraswasta">Wiraswasta</option>
                                <option value="Melanjutkan Pendidikan">Melanjutkan Pendidikan</option>
                                <option value="Tidak kerja tetapi sedang mencari kerja">Tidak kerja tetapi sedang mencari kerja</option>
                                <option value="Belum memungkinkan bekerja">Belum memungkinkan bekerja</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold text-dark small mb-1" for="add_waktu_tunggu_kerja">Waktu Tunggu Pekerjaan</label>
                            <input id="add_waktu_tunggu_kerja" name="waktu_tunggu_kerja" type="text" class="form-control" placeholder="Contoh: 3 bulan, 1 tahun, langsung" style="border-radius: 8px;">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold text-dark small mb-1" for="add_response_rate">Response Rate (%)</label>
                            <input id="add_response_rate" name="response_rate" type="number" min="0" max="100" class="form-control" placeholder="Contoh: 85" style="border-radius: 8px;">
                        </div>
                        
                        <!-- Company fields (shown conditionally) -->
                        <div id="add_company_fields" class="col-12 mb-3 bg-light p-3 border rounded" style="display: none;">
                            <div class="row">
                                <div class="col-md-4 mb-2">
                                    <label class="font-weight-bold text-dark small mb-1" for="add_nama_perusahaan">Nama Perusahaan</label>
                                    <input id="add_nama_perusahaan" name="nama_perusahaan" type="text" class="form-control form-control-sm" placeholder="Nama perusahaan" style="border-radius: 6px;">
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="font-weight-bold text-dark small mb-1" for="add_jabatan">Jabatan</label>
                                    <input id="add_jabatan" name="jabatan" type="text" class="form-control form-control-sm" placeholder="Jabatan" style="border-radius: 6px;">
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="font-weight-bold text-dark small mb-1" for="add_tempat_kerja">Tempat Kerja</label>
                                    <select id="add_tempat_kerja" name="tempat_kerja" class="form-control browser-default custom-select custom-select-sm" style="border-radius: 6px;">
                                        <option value="">Pilih Tempat Kerja</option>
                                        <option value="Lokal">Lokal</option>
                                        <option value="Nasional">Nasional</option>
                                        <option value="Multinasional">Multinasional</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-outline-danger btn-md font-weight-bold" data-dismiss="modal" style="border-radius: 8px;">Batal</button>
                    <button type="submit" class="btn btn-danger btn-md font-weight-bold" style="border-radius: 8px;">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editAlumniModal" tabindex="-1" role="dialog" aria-labelledby="editAlumniModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title font-weight-bold" id="editAlumniModalLabel">Edit Data Alumni</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="font-weight-bold text-dark small mb-1" for="edit_nama">Nama Lengkap</label>
                            <input id="edit_nama" name="nama_student" type="text" class="form-control" required style="border-radius: 8px;">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold text-dark small mb-1" for="edit_nim">NIM</label>
                            <input id="edit_nim" name="nim" type="text" class="form-control" required style="border-radius: 8px;">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold text-dark small mb-1" for="edit_prodi">Program Studi</label>
                            <select id="edit_prodi" name="prodi_id" class="form-control browser-default custom-select" required style="border-radius: 8px;">
                                @foreach($prodis as $prodi)
                                    <option value="{{ $prodi->id }}">{{ $prodi->nama_prodi }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold text-dark small mb-1" for="edit_angkatan">Angkatan</label>
                            <input id="edit_angkatan" name="angkatan" type="number" min="2000" max="2099" class="form-control" required style="border-radius: 8px;">
                        </div>
                        <input type="hidden" id="edit_status" name="status" value="lulus">
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold text-dark small mb-1" for="edit_status_alumni">Status Alumni</label>
                            <select id="edit_status_alumni" name="status_alumni" onchange="toggleEditCompanyFields()" class="form-control browser-default custom-select" style="border-radius: 8px;">
                                <option value="">Pilih Status Alumni</option>
                                <option value="Bekerja (full time / part time)">Bekerja (full time / part time)</option>
                                <option value="Wiraswasta">Wiraswasta</option>
                                <option value="Melanjutkan Pendidikan">Melanjutkan Pendidikan</option>
                                <option value="Tidak kerja tetapi sedang mencari kerja">Tidak kerja tetapi sedang mencari kerja</option>
                                <option value="Belum memungkinkan bekerja">Belum memungkinkan bekerja</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold text-dark small mb-1" for="edit_waktu_tunggu_kerja">Waktu Tunggu Pekerjaan</label>
                            <input id="edit_waktu_tunggu_kerja" name="waktu_tunggu_kerja" type="text" class="form-control" style="border-radius: 8px;">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold text-dark small mb-1" for="edit_response_rate">Response Rate (%)</label>
                            <input id="edit_response_rate" name="response_rate" type="number" min="0" max="100" class="form-control" style="border-radius: 8px;">
                        </div>
                        
                        <!-- Company fields (shown conditionally) -->
                        <div id="edit_company_fields" class="col-12 mb-3 bg-light p-3 border rounded" style="display: none;">
                            <div class="row">
                                <div class="col-md-4 mb-2">
                                    <label class="font-weight-bold text-dark small mb-1" for="edit_nama_perusahaan">Nama Perusahaan</label>
                                    <input id="edit_nama_perusahaan" name="nama_perusahaan" type="text" class="form-control form-control-sm" style="border-radius: 6px;">
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="font-weight-bold text-dark small mb-1" for="edit_jabatan">Jabatan</label>
                                    <input id="edit_jabatan" name="jabatan" type="text" class="form-control form-control-sm" style="border-radius: 6px;">
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label class="font-weight-bold text-dark small mb-1" for="edit_tempat_kerja">Tempat Kerja</label>
                                    <select id="edit_tempat_kerja" name="tempat_kerja" class="form-control browser-default custom-select custom-select-sm" style="border-radius: 6px;">
                                        <option value="">Pilih Tempat Kerja</option>
                                        <option value="Lokal">Lokal</option>
                                        <option value="Nasional">Nasional</option>
                                        <option value="Multinasional">Multinasional</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-outline-danger btn-md font-weight-bold" data-dismiss="modal" style="border-radius: 8px;">Batal</button>
                    <button type="submit" class="btn btn-danger btn-md font-weight-bold" style="border-radius: 8px;">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Form (hidden) -->
<form id="deleteForm" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>

<!-- ══════════════════════════════════════════════════════════
     IMPORT EXCEL MODAL
══════════════════════════════════════════════════════════ -->
<div class="modal fade" id="importAlumniModal" tabindex="-1" role="dialog" aria-labelledby="importAlumniModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content" style="border-radius:16px; border:none; box-shadow:0 16px 48px rgba(0,0,0,0.15);">
            <form action="{{ route('alumni.import') }}" method="POST" enctype="multipart/form-data" id="importForm">
                @csrf
                <div class="modal-header border-0 pb-0 px-4 pt-4">
                    <div>
                        <h5 class="modal-title font-weight-bold text-dark mb-0" id="importAlumniModalLabel">
                            <i class="fas fa-file-upload text-danger mr-2"></i>Import Data Alumni
                        </h5>
                        <p class="text-muted small mb-0 mt-1">Upload file Excel (.xlsx/.xls) sesuai template</p>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top:-0.5rem;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body px-4 py-3">

                    {{-- Download Template Banner --}}
                    <div class="d-flex align-items-center mb-3 p-3" style="background:#f0fdf4; border:1.5px dashed #16a34a; border-radius:10px;">
                        <i class="fas fa-file-excel text-success mr-3" style="font-size:1.6rem;"></i>
                        <div class="flex-grow-1">
                            <div class="font-weight-bold text-dark" style="font-size:0.85rem;">Belum punya template?</div>
                            <small class="text-muted">Download template Excel lalu isi data alumni sesuai format.</small>
                        </div>
                        <a href="{{ route('alumni.template') }}" class="btn btn-success btn-sm font-weight-bold ml-2 text-nowrap" style="border-radius:8px; font-size:0.78rem;">
                            <i class="fas fa-download mr-1"></i> Template
                        </a>
                    </div>

                    {{-- Drop Zone --}}
                    <div id="dropZone" onclick="document.getElementById('importFile').click()"
                         style="border: 2px dashed #d1d5db; border-radius: 12px; padding: 2rem 1rem;
                                text-align: center; cursor: pointer; transition: all 0.2s;
                                background: #fafafa;">
                        <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                        <div class="font-weight-bold text-dark" style="font-size:0.9rem;">Klik atau seret file ke sini</div>
                        <div class="text-muted small mt-1">Format: .xlsx atau .xls &bull; Maks. 5 MB</div>
                        <input type="file" id="importFile" name="file" accept=".xlsx,.xls"
                               style="display:none;" onchange="handleFileSelect(this)">
                    </div>

                    {{-- File Preview --}}
                    <div id="filePreview" style="display:none;" class="mt-3 p-3 d-flex align-items-center"
                         style="background:#fffbeb; border:1.5px solid #fde68a; border-radius:10px;">
                        <i class="fas fa-file-excel text-success mr-3" style="font-size:1.4rem;"></i>
                        <div class="flex-grow-1">
                            <div class="font-weight-bold text-dark" id="fileName" style="font-size:0.85rem;"></div>
                            <small class="text-muted" id="fileSize"></small>
                        </div>
                        <button type="button" onclick="clearFile()" class="btn btn-link text-danger p-0 ml-2" title="Hapus">
                            <i class="fas fa-times-circle"></i>
                        </button>
                    </div>

                    {{-- Validation errors from previous attempt --}}
                    @error('file')
                    <div class="alert alert-danger mt-2 py-2" style="border-radius:8px; font-size:0.82rem;">
                        <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                    </div>
                    @enderror

                    <div class="mt-3 p-3" style="background:#eff6ff; border-radius:10px; font-size:0.78rem; color:#1e40af;">
                        <i class="fas fa-info-circle mr-1"></i>
                        <strong>Format kolom:</strong>
                        NIM &bull; Nama Lengkap &bull; Email &bull; Kode Prodi &bull; Angkatan &bull; Status<br>
                        <span class="mt-1 d-block">
                            <i class="fas fa-link mr-1"></i>
                            Kode Prodi otomatis terhubung ke <strong>Fakultas &amp; Universitas</strong>.
                            Password default alumni: <code>password123</code>. Data karir diisi lewat kuesioner.
                        </span>
                    </div>
                </div>

                <div class="modal-footer border-0 px-4 pb-4 pt-2">
                    <button type="button" class="btn btn-outline-secondary font-weight-bold" data-dismiss="modal" style="border-radius:8px;">Batal</button>
                    <button type="submit" id="importSubmitBtn" class="btn btn-danger font-weight-bold" style="border-radius:8px;" disabled>
                        <i class="fas fa-upload mr-1"></i> Mulai Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/addons/datatables.min.css') }}">

@endpush

@push('scripts')
<script src="{{ asset('assets/js/addons/datatables.min.js') }}"></script>
<script>
    $(document).ready(function() {
        if ($('#alumniTable tbody tr').length > 0 && !$('#alumniTable tbody tr td[colspan]').length) {
            $('#alumniTable').DataTable({
                responsive: true,
                order: [[0, 'asc']],
                language: {
                    url: '{{ asset('js_old/datatables-id.json') }}',
                }
            });
        }
    });

    // ── Add Modal ──
    function openAddModal() {
        document.getElementById('addForm').reset();
        toggleAddCompanyFields();
        $('#addAlumniModal').modal('show');
    }

    function toggleAddCompanyFields() {
        const status = document.getElementById('add_status_alumni').value;
        const compFields = document.getElementById('add_company_fields');
        if (status === 'Bekerja (full time / part time)' || status === 'Wiraswasta') {
            $(compFields).slideDown();
        } else {
            $(compFields).slideUp();
        }
    }

    function toggleEditCompanyFields() {
        const status = document.getElementById('edit_status_alumni').value;
        const compFields = document.getElementById('edit_company_fields');
        if (status === 'Bekerja (full time / part time)' || status === 'Wiraswasta') {
            $(compFields).slideDown();
        } else {
            $(compFields).slideUp();
        }
    }

    function openEditModal(id, nama, nim, prodiId, angkatan, status, statusAlumni, namaPerusahaan, jabatan, tempatKerja, responseRate, waktuTungguKerja) {
        document.getElementById('edit_nama').value = nama;
        document.getElementById('edit_nim').value = nim;
        document.getElementById('edit_prodi').value = prodiId;
        document.getElementById('edit_angkatan').value = angkatan;
        document.getElementById('edit_status').value = status;
        document.getElementById('edit_status_alumni').value = statusAlumni || '';
        document.getElementById('edit_nama_perusahaan').value = namaPerusahaan || '';
        document.getElementById('edit_jabatan').value = jabatan || '';
        document.getElementById('edit_tempat_kerja').value = tempatKerja || '';
        document.getElementById('edit_response_rate').value = responseRate || '';
        document.getElementById('edit_waktu_tunggu_kerja').value = waktuTungguKerja || '';
        document.getElementById('editForm').action = '/alumni/' + id;
        toggleEditCompanyFields();
        $('#editAlumniModal').modal('show');
    }

    function confirmDelete(id, nama) {
        Swal.fire({
            title: 'Hapus Data Alumni?',
            html: `Yakin ingin menghapus data <strong>${nama}</strong>? Tindakan ini tidak bisa dibatalkan.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#800000',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            customClass: { popup: 'rounded-2xl' }
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('deleteForm');
                form.action = '/alumni/' + id;
                form.submit();
            }
        });
    }

    // ── Import Modal ──
    function openImportModal() {
        clearFile();
        $('#importAlumniModal').modal('show');
    }

    function handleFileSelect(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const sizeMB = (file.size / 1024 / 1024).toFixed(2);

            document.getElementById('fileName').textContent = file.name;
            document.getElementById('fileSize').textContent = sizeMB + ' MB';

            const preview = document.getElementById('filePreview');
            preview.style.display = 'flex';
            preview.style.background = '#f0fdf4';
            preview.style.border = '1.5px solid #86efac';
            preview.style.borderRadius = '10px';

            document.getElementById('dropZone').style.borderColor = '#16a34a';
            document.getElementById('dropZone').style.background = '#f0fdf4';
            document.getElementById('importSubmitBtn').disabled = false;
        }
    }

    function clearFile() {
        document.getElementById('importFile').value = '';
        document.getElementById('filePreview').style.display = 'none';
        document.getElementById('dropZone').style.borderColor = '#d1d5db';
        document.getElementById('dropZone').style.background = '#fafafa';
        document.getElementById('importSubmitBtn').disabled = true;
    }

    // Drag & Drop
    const dropZone = document.getElementById('dropZone');
    if (dropZone) {
        dropZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.style.borderColor = '#800000';
            this.style.background = '#fff5f5';
        });
        dropZone.addEventListener('dragleave', function() {
            this.style.borderColor = '#d1d5db';
            this.style.background = '#fafafa';
        });
        dropZone.addEventListener('drop', function(e) {
            e.preventDefault();
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                document.getElementById('importFile').files = files;
                handleFileSelect(document.getElementById('importFile'));
            }
        });
    }

    // Submit loading state
    document.getElementById('importForm')?.addEventListener('submit', function() {
        const btn = document.getElementById('importSubmitBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Mengimpor...';
    });
</script>
@endpush
