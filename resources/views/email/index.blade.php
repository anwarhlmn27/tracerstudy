@extends('layouts.app')

@section('title', 'Email Blast')
@section('header', 'Email Blast')

@push('styles')
<!-- Quill CSS (CDN) -->
<link href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css" rel="stylesheet">
<!-- TomSelect Bootstrap 4 CSS (CDN) -->
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap4.min.css" rel="stylesheet">

@endpush

@section('content')
<div class="container-fluid">
    
    <!-- Title Area -->
    <div class="mb-4">
        <h4 class="font-weight-bold text-dark mb-1">Kirim Email Massal</h4>
        <p class="text-muted small mb-0">Buat dan kirim pengumuman atau undangan kuesioner ke alumni dan atasan.</p>
    </div>

    <div class="card email-card">
        <!-- Form Header -->
        <div class="card-header bg-danger text-white d-flex align-items-center py-3" style="border-top-left-radius: 15px; border-top-right-radius: 15px;">
            <i class="fas fa-paper-plane fa-lg mr-2"></i>
            <h5 class="font-weight-bold mb-0">Komposisi Email</h5>
        </div>

        <form action="{{ route('email.send') }}" method="POST" enctype="multipart/form-data" id="emailForm" class="card-body p-4 p-md-5">
            @csrf

            <!-- Target Selection Area -->
            <div class="row mb-4 pb-4 border-bottom">
                <div class="col-lg-4 mb-4 mb-lg-0">
                    <label class="font-weight-bold text-dark small">Tujuan Pengiriman</label>
                    <p class="text-muted small mb-3">Pilih apakah email ini untuk satu grup peran tertentu atau beberapa orang terpilih.</p>
                    
                    <div class="d-flex flex-column" style="gap: 12px;">
                        <div class="target-box active" id="box-group">
                            <div class="custom-control custom-radio">
                                <input type="radio" class="custom-control-input" id="target-group" name="target_type" value="group" checked>
                                <label class="custom-control-label w-100" for="target-group" style="cursor: pointer;">
                                    <span class="d-block font-weight-bold text-dark" style="font-size: 0.9rem;">Berdasarkan Grup</span>
                                    <span class="d-block text-muted small">Kirim ke semua anggota grup</span>
                                </label>
                            </div>
                        </div>
                        
                        <div class="target-box" id="box-individual">
                            <div class="custom-control custom-radio">
                                <input type="radio" class="custom-control-input" id="target-individual" name="target_type" value="individual">
                                <label class="custom-control-label w-100" for="target-individual" style="cursor: pointer;">
                                    <span class="d-block font-weight-bold text-dark" style="font-size: 0.9rem;">Pilih Individual</span>
                                    <span class="d-block text-muted small">Cari dan pilih per orang</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <!-- Group Selection -->
                    <div id="group-selection-area">
                        <label class="font-weight-bold text-dark small mb-2">Pilih Grup Penerima</label>
                        <select name="group" class="form-control browser-default custom-select" style="border-radius: 8px; height: 42px;">
                            <option value="alumni">Grup: Alumni (Seluruh Alumni Terdaftar)</option>
                            <option value="atasan">Grup: Atasan (Seluruh Atasan Terdaftar)</option>
                        </select>
                        <p class="text-muted small mt-2">
                            <i class="fas fa-exclamation-circle text-warning mr-1"></i> Pastikan format email user valid agar email dapat terkirim.
                        </p>
                    </div>

                    <!-- Individual Selection -->
                    <div id="individual-selection-area" style="display: none;">
                        <label class="font-weight-bold text-dark small mb-2">Pilih Pengguna</label>
                        <select name="users[]" id="users-select" multiple placeholder="Cari nama atau email pengguna...">
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }}) - {{ ucfirst($user->role) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Subject & Content Area -->
            <div class="row">
                <!-- Subject -->
                <div class="col-12 mb-4">
                    <label class="font-weight-bold text-dark small mb-2">Subjek Email</label>
                    <input type="text" name="subject" required class="form-control" placeholder="Contoh: Undangan Pengisian Kuesioner Tracer Study 2026" style="border-radius: 8px; height: 42px;">
                </div>

                <!-- Attachment -->
                <div class="col-12 mb-4">
                    <label class="font-weight-bold text-dark small mb-2">Lampiran Dokumen</label>
                    <div>
                        <input type="file" name="attachments[]" id="attachment" class="d-none" multiple>
                        
                        <!-- Combined Dropzone Area -->
                        <div class="border rounded p-4 text-center bg-light" style="border-style: dashed !important; border-width: 2px !important; border-radius: 12px !important;">
                            <label for="attachment" class="m-0 cursor-pointer d-block">
                                <i class="fas fa-cloud-upload-alt fa-3x text-warning mb-2"></i>
                                <p class="mb-1 text-dark font-weight-bold" style="font-size: 0.9rem;">Klik untuk unggah atau seret file ke sini</p>
                                <p class="text-muted small mb-0">Bisa memilih lebih dari satu file (Maks 10MB/file)</p>
                            </label>

                            <!-- File Cards Container -->
                            <div id="file-cards-container" class="row mt-3 text-left"></div>
                        </div>
                    </div>
                </div>

                <!-- Body (Quill) -->
                <div class="col-12 mb-4">
                    <label class="font-weight-bold text-dark small mb-2">Pesan Email</label>
                    <input type="hidden" name="body" id="bodyInput">
                    <div class="border rounded" style="border-radius: 8px; overflow: hidden;">
                        <div id="editor-container"></div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="d-flex justify-content-end align-items-center border-top pt-4" style="gap: 15px;">
                <a href="{{ route('dashboard') }}" class="btn btn-outline-danger btn-md font-weight-bold m-0" style="border-radius: 8px;">
                    Batal
                </a>
                <button type="submit" class="btn btn-danger btn-md font-weight-bold m-0 d-flex align-items-center" style="border-radius: 8px;">
                    <i class="fas fa-paper-plane mr-2"></i> Kirim Blast Sekarang
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<!-- Quill JS (CDN) -->
<script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
<!-- TomSelect JS (CDN) -->
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize TomSelect for individual users
        new TomSelect('#users-select', {
            plugins: ['remove_button'],
            maxOptions: null,
            placeholder: 'Pilih satu atau lebih pengguna...',
        });

        // Toggle destination sections and layout styles
        $('input[name="target_type"]').on('change', function() {
            const val = $(this).val();
            if (val === 'group') {
                $('#box-group').addClass('active');
                $('#box-individual').removeClass('active');
                $('#group-selection-area').show();
                $('#individual-selection-area').hide();
            } else {
                $('#box-individual').addClass('active');
                $('#box-group').removeClass('active');
                $('#individual-selection-area').show();
                $('#group-selection-area').hide();
            }
        });

        // Initialize Quill Editor
        var quill = new Quill('#editor-container', {
            theme: 'snow',
            placeholder: 'Halo,\n\nTerima kasih atas kontribusi Anda...',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'color': [] }, { 'background': [] }],
                    ['blockquote', 'code-block'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    [{ 'align': [] }],
                    ['link'],
                    ['clean']
                ]
            }
        });

        // Sync Quill content to hidden input before submit
        var form = document.getElementById('emailForm');
        
        // Prevent form submission when pressing Enter in standard inputs
        form.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                const target = e.target;
                if (target.tagName === 'TEXTAREA' || target.isContentEditable || target.closest('.ql-editor')) {
                    return;
                }
                e.preventDefault();
            }
        });

        form.onsubmit = function() {
            var bodyInput = document.getElementById('bodyInput');
            bodyInput.value = quill.root.innerHTML;
            
            if (quill.getText().trim().length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Pesan Kosong',
                    text: 'Pesan email tidak boleh kosong.',
                    confirmButtonColor: '#800000'
                });
                return false;
            }
            return true;
        };

        // File upload multiple display and remove logic
        const fileInput = document.getElementById('attachment');
        const fileCardsContainer = document.getElementById('file-cards-container');
        let dataTransfer = new DataTransfer();
        
        fileInput.addEventListener('change', function() {
            for (let i = 0; i < this.files.length; i++) {
                dataTransfer.items.add(this.files[i]);
            }
            fileInput.files = dataTransfer.files;
            renderFileCards();
        });

        function renderFileCards() {
            fileCardsContainer.innerHTML = '';
            Array.from(dataTransfer.files).forEach((file, index) => {
                const col = document.createElement('div');
                col.className = 'col-sm-6 mb-2';
                col.innerHTML = `
                    <div class="card d-flex flex-row align-items-center justify-content-between p-2 border bg-white" style="border-radius: 8px;">
                        <div class="d-flex align-items-center text-truncate pr-2">
                            <div class="p-2 bg-light text-danger mr-2 rounded d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <div class="text-truncate">
                                <div class="font-weight-bold text-dark text-truncate small" title="${file.name}">${file.name}</div>
                                <small class="text-muted">${(file.size / 1024 / 1024).toFixed(2)} MB</small>
                            </div>
                        </div>
                        <button type="button" class="btn btn-link p-1 text-danger m-0" onclick="removeFile(${index})" title="Hapus File">
                            <i class="fas fa-times-circle fa-lg"></i>
                        </button>
                    </div>
                `;
                fileCardsContainer.appendChild(col);
            });
        }

        window.removeFile = function(index) {
            const newDt = new DataTransfer();
            Array.from(dataTransfer.files).forEach((file, i) => {
                if (i !== index) newDt.items.add(file);
            });
            dataTransfer = newDt;
            fileInput.files = dataTransfer.files;
            renderFileCards();
        }
    });
</script>
@endpush
