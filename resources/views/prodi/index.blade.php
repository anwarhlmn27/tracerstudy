@extends('layouts.app')

@section('title', 'Master Program Studi - Tracer Study')
@section('header', 'Master Program Studi')

@section('content')


<div class="container-fluid">
    <div class="card prodi-card">
        <div class="card-body p-0">
            <!-- Card Title & Add Button -->
            <div class="px-4 py-4 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center border-bottom">
                <div>
                    <h5 class="font-weight-bold text-dark mb-1">Daftar Program Studi</h5>
                    <p class="text-muted small mb-0">Kelola data program studi mahasiswa di sini</p>
                </div>
                <button type="button" class="btn btn-danger btn-md font-weight-bold m-0 mt-3 mt-sm-0" onclick="openAddModal()" style="border-radius: 10px;">
                    <i class="fas fa-plus mr-2"></i> Tambah Program Studi
                </button>
            </div>

            <!-- Table Wrapper -->
            <div class="table-responsive p-3">
                <table id="prodiTable" class="table table-hover w-100">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 px-4">No</th>
                            <th class="border-0">Kode Prodi</th>
                            <th class="border-0">Nama Program Studi</th>
                            <th class="border-0">Singkatan</th>
                            <th class="border-0">Fakultas</th>
                            <th class="border-0 text-center">Jumlah Alumni</th>
                            <th class="border-0 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($prodis as $index => $prodi)
                        <tr>
                            <td class="px-4 align-middle font-weight-bold text-dark">{{ $index + 1 }}</td>
                            <td class="align-middle font-weight-bold text-muted" style="font-family: monospace; font-size: 0.8rem;">{{ $prodi->kode_prodi }}</td>
                            <td class="align-middle font-weight-bold text-dark">{{ $prodi->nama_prodi }}</td>
                            <td class="align-middle text-dark font-weight-bold text-muted">{{ $prodi->short_name }}</td>
                            <td class="align-middle text-dark font-weight-bold text-muted">{{ $prodi->fakultas ? $prodi->fakultas->nama_fakultas : '-' }}</td>
                            <td class="align-middle text-center">
                                <span class="badge badge-light px-3 py-1.5 rounded-pill font-weight-bold" style="font-size: 10px;">
                                    {{ $prodi->students_count }} Alumni
                                </span>
                            </td>
                            <td class="align-middle text-center">
                                <div class="d-flex justify-content-center align-items-center">
                                    <button class="btn btn-link p-1 text-primary mr-1" onclick="openEditModal('{{ $prodi->id }}', '{{ addslashes($prodi->nama_prodi) }}', '{{ addslashes($prodi->kode_prodi) }}', '{{ addslashes($prodi->short_name) }}', '{{ $prodi->fakultas_id }}')" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-link p-1 text-danger" onclick="confirmDelete('{{ $prodi->id }}', '{{ addslashes($prodi->nama_prodi) }}', {{ $prodi->students_count }})" title="Hapus">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="py-5 text-center text-muted">
                                <i class="fas fa-university fa-2x mb-3 d-block"></i>
                                Belum ada data program studi.
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
<div class="modal fade" id="addProdiModal" tabindex="-1" role="dialog" aria-labelledby="addProdiModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('prodi.store') }}" method="POST" id="addForm">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title font-weight-bold" id="addProdiModalLabel">Tambah Program Studi</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark small mb-1" for="add_kode_prodi">Kode Program Studi</label>
                        <input id="add_kode_prodi" name="kode_prodi" type="text" class="form-control" placeholder="Contoh: IF, SI, TI" required style="border-radius: 8px;">
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark small mb-1" for="add_nama_prodi">Nama Program Studi</label>
                        <input id="add_nama_prodi" name="nama_prodi" type="text" class="form-control" placeholder="Masukkan nama program studi" required style="border-radius: 8px;">
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark small mb-1" for="add_short_name">Nama Singkat / Singkatan</label>
                        <input id="add_short_name" name="short_name" type="text" class="form-control" placeholder="Contoh: TI, SI" required style="border-radius: 8px;">
                    </div>
                    <div class="form-group mb-0">
                        <label class="font-weight-bold text-dark small mb-1" for="add_fakultas_id">Fakultas</label>
                        <select id="add_fakultas_id" name="fakultas_id" class="form-control" style="border-radius: 8px;">
                            <option value="">-- Pilih Fakultas --</option>
                            @foreach($fakultas as $fak)
                                <option value="{{ $fak->id }}">{{ $fak->nama_fakultas }}</option>
                            @endforeach
                        </select>
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
<div class="modal fade" id="editProdiModal" tabindex="-1" role="dialog" aria-labelledby="editProdiModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title font-weight-bold" id="editProdiModalLabel">Edit Program Studi</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark small mb-1" for="edit_kode_prodi">Kode Program Studi</label>
                        <input id="edit_kode_prodi" name="kode_prodi" type="text" class="form-control" required style="border-radius: 8px;">
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark small mb-1" for="edit_nama_prodi">Nama Program Studi</label>
                        <input id="edit_nama_prodi" name="nama_prodi" type="text" class="form-control" required style="border-radius: 8px;">
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark small mb-1" for="edit_short_name">Nama Singkat / Singkatan</label>
                        <input id="edit_short_name" name="short_name" type="text" class="form-control" required style="border-radius: 8px;">
                    </div>
                    <div class="form-group mb-0">
                        <label class="font-weight-bold text-dark small mb-1" for="edit_fakultas_id">Fakultas</label>
                        <select id="edit_fakultas_id" name="fakultas_id" class="form-control" style="border-radius: 8px;">
                            <option value="">-- Pilih Fakultas --</option>
                            @foreach($fakultas as $fak)
                                <option value="{{ $fak->id }}">{{ $fak->nama_fakultas }}</option>
                            @endforeach
                        </select>
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
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/addons/datatables.min.css') }}">

@endpush

@push('scripts')
<script src="{{ asset('assets/js/addons/datatables.min.js') }}"></script>
<script>
    $(document).ready(function() {
        if ($('#prodiTable tbody tr').length > 0 && !$('#prodiTable tbody tr td[colspan]').length) {
            $('#prodiTable').DataTable({
                responsive: true,
                order: [[2, 'asc']],
                language: {
                    url: '{{ asset('js_old/datatables-id.json') }}',
                }
            });
        }
    });

    function openAddModal() {
        document.getElementById('addForm').reset();
        $('#addProdiModal').modal('show');
    }

    function openEditModal(id, nama, kode, shortName, fakultasId) {
        document.getElementById('edit_nama_prodi').value = nama;
        document.getElementById('edit_kode_prodi').value = kode;
        document.getElementById('edit_short_name').value = shortName;
        document.getElementById('edit_fakultas_id').value = fakultasId || '';
        document.getElementById('editForm').action = '/prodi/' + id;
        $('#editProdiModal').modal('show');
    }

    function confirmDelete(id, nama, count) {
        if (count > 0) {
            Swal.fire({
                title: 'Tidak Bisa Menghapus!',
                html: `Program Studi <strong>${nama}</strong> masih memiliki <strong>${count}</strong> data alumni terkait. Silakan pindahkan atau hapus data alumni tersebut terlebih dahulu.`,
                icon: 'error',
                confirmButtonColor: '#800000',
                confirmButtonText: 'Tutup',
                customClass: { popup: 'rounded-2xl' }
            });
            return;
        }

        Swal.fire({
            title: 'Hapus Program Studi?',
            html: `Yakin ingin menghapus program studi <strong>${nama}</strong>? Tindakan ini tidak bisa dibatalkan.`,
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
                form.action = '/prodi/' + id;
                form.submit();
            }
        });
    }
</script>
@endpush
