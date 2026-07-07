@extends('layouts.app')

@section('title', 'Master Universitas - Tracer Study')
@section('header', 'Master Universitas')

@section('content')


<div class="container-fluid">
    <div class="card card-cascade narrower mb-4">
        <!-- Card header -->
        <div class="view view-cascade gradient-card-header maroon-gradient d-flex flex-column flex-sm-row justify-content-between align-items-sm-center px-4 py-4 text-white">
            <div>
                <h5 class="font-weight-bold text-white mb-1">Daftar Universitas</h5>
                <p class="text-white-50 small mb-0">Kelola data perguruan tinggi utama di sini</p>
            </div>
            <button type="button" class="btn btn-light btn-md font-weight-bold m-0 mt-3 mt-sm-0 text-dark" onclick="openAddModal()" style="border-radius: 10px; background-color: #fff !important;">
                <i class="fas fa-plus mr-2 text-danger"></i> Tambah Universitas
            </button>
        </div>

        <div class="card-body card-body-cascade p-0">
            <!-- Table Wrapper -->
            <div class="table-responsive p-3">
                <table id="univTable" class="table table-hover w-100">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 px-4">No</th>
                            <th class="border-0">Kode</th>
                            <th class="border-0">Nama Universitas</th>
                            <th class="border-0">Kontak</th>
                            <th class="border-0 text-center">Fakultas</th>
                            <th class="border-0 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($univs as $index => $univ)
                        <tr>
                            <td class="px-4 align-middle font-weight-bold text-dark">{{ $index + 1 }}</td>
                            <td class="align-middle font-weight-bold text-muted" style="font-family: monospace; font-size: 0.8rem;">{{ $univ->kode_univ }}</td>
                            <td class="align-middle font-weight-bold text-dark">{{ $univ->nama_univ }}</td>
                            <td class="align-middle">
                                <div class="small">
                                    <strong>Email:</strong> {{ $univ->email }}<br>
                                    <strong>Web:</strong> <a href="{{ $univ->website }}" target="_blank" class="text-danger">{{ $univ->website }}</a>
                                </div>
                            </td>
                            <td class="align-middle text-center">
                                <span class="badge badge-light px-3 py-1.5 rounded-pill font-weight-bold" style="font-size: 10px;">
                                    {{ $univ->fakultas_count }} Fakultas
                                </span>
                            </td>
                            <td class="align-middle text-center">
                                <div class="d-flex justify-content-center align-items-center">
                                    <button class="btn btn-link p-1 text-primary mr-1" 
                                            onclick="openEditModal('{{ $univ->id }}', '{{ addslashes($univ->nama_univ) }}', '{{ addslashes($univ->kode_univ) }}', '{{ addslashes($univ->address) }}', '{{ $univ->email }}', '{{ $univ->website }}')" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-link p-1 text-danger" onclick="confirmDelete('{{ $univ->id }}', '{{ addslashes($univ->nama_univ) }}', {{ $univ->fakultas_count }})" title="Hapus">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-5 text-center text-muted">
                                <i class="fas fa-university fa-2x mb-3 d-block"></i>
                                Belum ada data universitas.
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
<div class="modal fade" id="addUnivModal" tabindex="-1" role="dialog" aria-labelledby="addUnivModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('univ.store') }}" method="POST" id="addForm">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title font-weight-bold" id="addUnivModalLabel">Tambah Universitas</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="form-row">
                        <div class="form-group col-md-4 mb-3">
                            <label class="font-weight-bold text-dark small mb-1" for="add_kode_univ">Kode</label>
                            <input id="add_kode_univ" name="kode_univ" type="text" class="form-control" placeholder="Contoh: UNP" required style="border-radius: 8px;">
                        </div>
                        <div class="form-group col-md-8 mb-3">
                            <label class="font-weight-bold text-dark small mb-1" for="add_nama_univ">Nama Universitas</label>
                            <input id="add_nama_univ" name="nama_univ" type="text" class="form-control" placeholder="Masukkan nama universitas" required style="border-radius: 8px;">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6 mb-3">
                            <label class="font-weight-bold text-dark small mb-1" for="add_email">Email</label>
                            <input id="add_email" name="email" type="email" class="form-control" placeholder="info@univ.ac.id" required style="border-radius: 8px;">
                        </div>
                        <div class="form-group col-md-6 mb-3">
                            <label class="font-weight-bold text-dark small mb-1" for="add_website">Website URL</label>
                            <input id="add_website" name="website" type="text" class="form-control" placeholder="https://univ.ac.id" required style="border-radius: 8px;">
                        </div>
                    </div>
                    <div class="form-group mb-0">
                        <label class="font-weight-bold text-dark small mb-1" for="add_address">Alamat Universitas</label>
                        <textarea id="add_address" name="address" rows="3" class="form-control" placeholder="Masukkan alamat lengkap universitas..." required style="border-radius: 8px;"></textarea>
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
<div class="modal fade" id="editUnivModal" tabindex="-1" role="dialog" aria-labelledby="editUnivModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title font-weight-bold" id="editUnivModalLabel">Edit Universitas</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="form-row">
                        <div class="form-group col-md-4 mb-3">
                            <label class="font-weight-bold text-dark small mb-1" for="edit_kode_univ">Kode</label>
                            <input id="edit_kode_univ" name="kode_univ" type="text" class="form-control" required style="border-radius: 8px;">
                        </div>
                        <div class="form-group col-md-8 mb-3">
                            <label class="font-weight-bold text-dark small mb-1" for="edit_nama_univ">Nama Universitas</label>
                            <input id="edit_nama_univ" name="nama_univ" type="text" class="form-control" required style="border-radius: 8px;">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6 mb-3">
                            <label class="font-weight-bold text-dark small mb-1" for="edit_email">Email</label>
                            <input id="edit_email" name="email" type="email" class="form-control" required style="border-radius: 8px;">
                        </div>
                        <div class="form-group col-md-6 mb-3">
                            <label class="font-weight-bold text-dark small mb-1" for="edit_website">Website URL</label>
                            <input id="edit_website" name="website" type="text" class="form-control" required style="border-radius: 8px;">
                        </div>
                    </div>
                    <div class="form-group mb-0">
                        <label class="font-weight-bold text-dark small mb-1" for="edit_address">Alamat Universitas</label>
                        <textarea id="edit_address" name="address" rows="3" class="form-control" required style="border-radius: 8px;"></textarea>
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
        if ($('#univTable tbody tr').length > 0 && !$('#univTable tbody tr td[colspan]').length) {
            $('#univTable').DataTable({
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
        $('#addUnivModal').modal('show');
    }

    function openEditModal(id, nama, kode, address, email, website) {
        document.getElementById('edit_nama_univ').value = nama;
        document.getElementById('edit_kode_univ').value = kode;
        document.getElementById('edit_address').value = address;
        document.getElementById('edit_email').value = email;
        document.getElementById('edit_website').value = website;
        document.getElementById('editForm').action = '/univs/' + id;
        $('#editUnivModal').modal('show');
    }

    function confirmDelete(id, name, count) {
        if (count > 0) {
            Swal.fire({
                title: 'Tidak Bisa Menghapus!',
                html: `Universitas <strong>${name}</strong> masih memiliki <strong>${count}</strong> fakultas terkait. Silakan hapus fakultas tersebut terlebih dahulu.`,
                icon: 'error',
                confirmButtonColor: '#800000',
                confirmButtonText: 'Tutup',
                customClass: { popup: 'rounded-2xl' }
            });
            return;
        }

        Swal.fire({
            title: 'Hapus Universitas?',
            html: `Yakin ingin menghapus universitas <strong>${name}</strong>? Tindakan ini tidak bisa dibatalkan.`,
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
                form.action = '/univs/' + id;
                form.submit();
            }
        });
    }
</script>
@endpush
