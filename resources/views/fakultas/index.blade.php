@extends('layouts.app')

@section('title', 'Master Fakultas - Tracer Study')
@section('header', 'Master Fakultas')

@section('content')


<div class="container-fluid">
    <div class="card card-cascade narrower mb-4">
        <!-- Card header -->
        <div class="view view-cascade gradient-card-header maroon-gradient d-flex flex-column flex-sm-row justify-content-between align-items-sm-center px-4 py-4 text-white">
            <div>
                <h5 class="font-weight-bold text-white mb-1">Daftar Fakultas</h5>
                <p class="text-white-50 small mb-0">Kelola data fakultas di lingkungan universitas di sini</p>
            </div>
            <button type="button" class="btn btn-light btn-md font-weight-bold m-0 mt-3 mt-sm-0 text-dark" onclick="openAddModal()" style="border-radius: 10px; background-color: #fff !important;">
                <i class="fas fa-plus mr-2 text-danger"></i> Tambah Fakultas
            </button>
        </div>

        <div class="card-body card-body-cascade p-0">
            <!-- Table Wrapper -->
            <div class="table-responsive p-3">
                <table id="fakultasTable" class="table table-hover w-100">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 px-4">No</th>
                            <th class="border-0">Kode</th>
                            <th class="border-0">Nama Fakultas</th>
                            <th class="border-0">Singkatan</th>
                            <th class="border-0">Universitas</th>
                            <th class="border-0 text-center">Prodi</th>
                            <th class="border-0 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($fakultas as $index => $fak)
                        <tr>
                            <td class="px-4 align-middle font-weight-bold text-dark">{{ $index + 1 }}</td>
                            <td class="align-middle font-weight-bold text-muted" style="font-family: monospace; font-size: 0.8rem;">{{ $fak->kode_fakultas }}</td>
                            <td class="align-middle font-weight-bold text-dark">{{ $fak->nama_fakultas }}</td>
                            <td class="align-middle text-muted">{{ $fak->short_name }}</td>
                            <td class="align-middle text-dark">{{ $fak->univ ? $fak->univ->nama_univ : '-' }}</td>
                            <td class="align-middle text-center">
                                <span class="badge badge-light px-3 py-1.5 rounded-pill font-weight-bold" style="font-size: 10px;">
                                    {{ $fak->prodis_count }} Prodi
                                </span>
                            </td>
                            <td class="align-middle text-center">
                                <div class="d-flex justify-content-center align-items-center">
                                    <button class="btn btn-link p-1 text-primary mr-1" 
                                            onclick="openEditModal('{{ $fak->id }}', '{{ addslashes($fak->nama_fakultas) }}', '{{ addslashes($fak->kode_fakultas) }}', '{{ $fak->id_univs }}', '{{ addslashes($fak->short_name) }}')" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-link p-1 text-danger" onclick="confirmDelete('{{ $fak->id }}', '{{ addslashes($fak->nama_fakultas) }}', {{ $fak->prodis_count }})" title="Hapus">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="py-5 text-center text-muted">
                                <i class="fas fa-graduation-cap fa-2x mb-3 d-block"></i>
                                Belum ada data fakultas.
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
<div class="modal fade" id="addFakultasModal" tabindex="-1" role="dialog" aria-labelledby="addFakultasModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('fakultas.store') }}" method="POST" id="addForm">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title font-weight-bold" id="addFakultasModalLabel">Tambah Fakultas</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="form-row">
                        <div class="form-group col-md-4 mb-3">
                            <label class="font-weight-bold text-dark small mb-1" for="add_kode_fakultas">Kode</label>
                            <input id="add_kode_fakultas" name="kode_fakultas" type="text" class="form-control" placeholder="Contoh: FTI" required style="border-radius: 8px;">
                        </div>
                        <div class="form-group col-md-8 mb-3">
                            <label class="font-weight-bold text-dark small mb-1" for="add_nama_fakultas">Nama Fakultas</label>
                            <input id="add_nama_fakultas" name="nama_fakultas" type="text" class="form-control" placeholder="Masukkan nama fakultas" required style="border-radius: 8px;">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12 mb-3">
                            <label class="font-weight-bold text-dark small mb-1" for="add_short_name">Nama Singkat / Singkatan</label>
                            <input id="add_short_name" name="short_name" type="text" class="form-control" placeholder="Contoh: FTI" required style="border-radius: 8px;">
                        </div>
                    </div>
                    <div class="form-group mb-0">
                        <label class="font-weight-bold text-dark small mb-1" for="add_id_univs">Universitas</label>
                        <select id="add_id_univs" name="id_univs" class="form-control" required style="border-radius: 8px;">
                            <option value="">-- Pilih Universitas --</option>
                            @foreach($univs as $u)
                                <option value="{{ $u->id }}">{{ $u->nama_univ }}</option>
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
<div class="modal fade" id="editFakultasModal" tabindex="-1" role="dialog" aria-labelledby="editFakultasModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title font-weight-bold" id="editFakultasModalLabel">Edit Fakultas</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="form-row">
                        <div class="form-group col-md-4 mb-3">
                            <label class="font-weight-bold text-dark small mb-1" for="edit_kode_fakultas">Kode</label>
                            <input id="edit_kode_fakultas" name="kode_fakultas" type="text" class="form-control" required style="border-radius: 8px;">
                        </div>
                        <div class="form-group col-md-8 mb-3">
                            <label class="font-weight-bold text-dark small mb-1" for="edit_nama_fakultas">Nama Fakultas</label>
                            <input id="edit_nama_fakultas" name="nama_fakultas" type="text" class="form-control" required style="border-radius: 8px;">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12 mb-3">
                            <label class="font-weight-bold text-dark small mb-1" for="edit_short_name">Nama Singkat / Singkatan</label>
                            <input id="edit_short_name" name="short_name" type="text" class="form-control" required style="border-radius: 8px;">
                        </div>
                    </div>
                    <div class="form-group mb-0">
                        <label class="font-weight-bold text-dark small mb-1" for="edit_id_univs">Universitas</label>
                        <select id="edit_id_univs" name="id_univs" class="form-control" required style="border-radius: 8px;">
                            <option value="">-- Pilih Universitas --</option>
                            @foreach($univs as $u)
                                <option value="{{ $u->id }}">{{ $u->nama_univ }}</option>
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
        if ($('#fakultasTable tbody tr').length > 0 && !$('#fakultasTable tbody tr td[colspan]').length) {
            $('#fakultasTable').DataTable({
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
        $('#addFakultasModal').modal('show');
    }

    function openEditModal(id, nama, kode, univId, shortName) {
        document.getElementById('edit_nama_fakultas').value = nama;
        document.getElementById('edit_kode_fakultas').value = kode;
        document.getElementById('edit_id_univs').value = univId;
        document.getElementById('edit_short_name').value = shortName;
        document.getElementById('editForm').action = '{{ url("fakultas") }}/' + id;
        $('#editFakultasModal').modal('show');
    }

    function confirmDelete(id, name, count) {
        if (count > 0) {
            Swal.fire({
                title: 'Tidak Bisa Menghapus!',
                html: `Fakultas <strong>${name}</strong> masih memiliki <strong>${count}</strong> program studi terhubung. Silakan hapus program studi tersebut terlebih dahulu.`,
                icon: 'error',
                confirmButtonColor: '#800000',
                confirmButtonText: 'Tutup',
                customClass: { popup: 'rounded-2xl' }
            });
            return;
        }

        Swal.fire({
            title: 'Hapus Fakultas?',
            html: `Yakin ingin menghapus fakultas <strong>${name}</strong>? Tindakan ini tidak bisa dibatalkan.`,
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
                form.action = '{{ url("fakultas") }}/' + id;
                form.submit();
            }
        });
    }
</script>
@endpush
