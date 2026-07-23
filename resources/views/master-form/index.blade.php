@extends('layouts.app')

@section('title', 'Master Form - Tracer Study')
@section('header', 'Master Form Kuesioner')

@section('content')


<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4">
        <div>
            <h5 class="font-weight-bold text-dark mb-1">Kelola Form Kuesioner</h5>
            <p class="text-muted small mb-0">Buat dan atur pertanyaan untuk form yang diisi oleh alumni dan atasan</p>
        </div>
        <a href="{{ route('master-form.create') }}" class="btn btn-danger btn-md font-weight-bold m-0 mt-3 mt-sm-0" style="border-radius: 10px;">
            <i class="fas fa-plus mr-2"></i> Buat Form Baru
        </a>
    </div>

    <!-- Forms Layout Grid -->
    <div class="row">
        <!-- Alumni Forms -->
        <div class="col-lg-6 mb-4">
            <div class="d-flex align-items-center mb-3">
                <div class="icon-title-box bg-success text-success mr-2" style="background-color: rgba(40, 167, 69, 0.15) !important;">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <h5 class="font-weight-bold text-dark mb-0">Form Alumni</h5>
            </div>
            
            <div class="d-flex flex-column" style="gap: 12px;">
                @forelse($forms->where('target_role', 'alumni') as $form)
                <div class="card form-list-card bg-white shadow-sm border-0 mb-3" style="border-radius: 14px;">
                    <div class="card-body p-4">
                        <!-- Top Row: Title + Status + Action Buttons -->
                        <div class="d-flex justify-content-between align-items-start mb-2" style="gap: 12px;">
                            <div class="d-flex align-items-center flex-wrap" style="gap: 8px; flex: 1; min-width: 0;">
                                <h6 class="font-weight-bold text-dark mb-0" style="font-size: 1rem; line-height: 1.4; word-break: break-word;">
                                    {{ $form->title }}
                                </h6>
                                @if($form->is_active)
                                    <span class="badge badge-success px-2 py-1 font-weight-bold" style="font-size: 9px; letter-spacing: 0.5px;">AKTIF</span>
                                @else
                                    <span class="badge badge-light text-muted px-2 py-1 font-weight-bold border" style="font-size: 9px; letter-spacing: 0.5px;">NONAKTIF</span>
                                @endif
                            </div>

                            <!-- Action buttons -->
                            <div class="d-flex align-items-center bg-light px-2 py-1 rounded-lg" style="gap: 4px; flex-shrink: 0;">
                                <a href="{{ route('master-form.preview', $form->id) }}" target="_blank" class="btn btn-sm btn-link p-1 text-info" title="Preview Tampilan Kuesioner">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button type="button"
                                    class="btn btn-sm btn-link p-1 text-secondary get-link-btn"
                                    title="Salin Link Kuesioner"
                                    data-form-role="{{ $form->target_role }}"
                                    data-form-title="{{ $form->title }}"
                                    data-form-slug="{{ \Illuminate\Support\Str::slug($form->title) }}">
                                    <i class="fas fa-link"></i>
                                </button>
                                <form action="{{ route('master-form.toggle', $form->id) }}" method="POST" class="m-0 d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-link p-1 {{ $form->is_active ? 'text-warning' : 'text-success' }}" title="{{ $form->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                        <i class="fas {{ $form->is_active ? 'fa-pause-circle' : 'fa-play-circle' }}"></i>
                                    </button>
                                </form>
                                <a href="{{ route('master-form.edit', $form->id) }}" class="btn btn-sm btn-link p-1 text-primary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('master-form.destroy', $form->id) }}" method="POST" class="m-0 d-inline" onsubmit="return confirm('Hapus form ini beserta semua pertanyaan dan responnya?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-link p-1 text-danger" title="Hapus">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Middle Row: Badges -->
                        <div class="d-flex flex-wrap align-items-center my-3" style="gap: 6px;">
                            @if($form->angkatan)
                                <span class="badge badge-primary px-2.5 py-1" style="font-size: 10px; background-color: rgba(0, 123, 255, 0.1) !important; color: #007bff !important; border: 1px solid rgba(0, 123, 255, 0.2);">
                                    <i class="fas fa-graduation-cap mr-1"></i> Angkatan {{ $form->angkatan }}
                                </span>
                            @else
                                <span class="badge badge-light px-2.5 py-1 border" style="font-size: 10px; color: #6c757d !important;">
                                    <i class="fas fa-users mr-1"></i> Semua Angkatan
                                </span>
                            @endif

                            @if($form->form_group)
                                <span class="badge badge-purple px-2.5 py-1 border" style="font-size: 10px; background-color: rgba(111, 66, 193, 0.1) !important; color: #6f42c1 !important; border-color: rgba(111, 66, 193, 0.2) !important;">
                                    <i class="fas fa-layer-group mr-1"></i> {{ $form->form_group }}
                                </span>
                            @endif
                        </div>

                        <!-- Footer Meta Info -->
                        <div class="d-flex align-items-center text-muted small pt-2 border-top" style="gap: 16px; font-size: 0.8rem;">
                            <span><i class="far fa-question-circle mr-1 text-danger"></i> <strong>{{ $form->questions_count }}</strong> pertanyaan</span>
                            <span><i class="far fa-file-alt mr-1 text-primary"></i> <strong>{{ $form->responses_count }}</strong> respons</span>
                            <span class="ml-auto"><i class="far fa-calendar-alt mr-1 text-secondary"></i> {{ $form->created_at->format('d M Y') }}</span>
                        </div>
                    </div>
                </div>
                @empty
                <div class="card text-center py-5 text-muted bg-white border" style="border-style: dashed !important; border-width: 2px !important; border-radius: 12px;">
                    <div class="card-body">
                        <i class="fas fa-file-signature fa-2x mb-2"></i>
                        <p class="small mb-0">Belum ada form untuk alumni</p>
                    </div>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Atasan Forms -->
        <div class="col-lg-6 mb-4">
            <div class="d-flex align-items-center mb-3">
                <div class="icon-title-box bg-primary text-primary mr-2" style="background-color: rgba(0, 123, 255, 0.15) !important;">
                    <i class="fas fa-briefcase"></i>
                </div>
                <h5 class="font-weight-bold text-dark mb-0">Form Atasan</h5>
            </div>
            
            <div class="d-flex flex-column" style="gap: 12px;">
                @forelse($forms->where('target_role', 'atasan') as $form)
                <div class="card form-list-card bg-white shadow-sm border-0 mb-3" style="border-radius: 14px;">
                    <div class="card-body p-4">
                        <!-- Top Row: Title + Status + Action Buttons -->
                        <div class="d-flex justify-content-between align-items-start mb-2" style="gap: 12px;">
                            <div class="d-flex align-items-center flex-wrap" style="gap: 8px; flex: 1; min-width: 0;">
                                <h6 class="font-weight-bold text-dark mb-0" style="font-size: 1rem; line-height: 1.4; word-break: break-word;">
                                    {{ $form->title }}
                                </h6>
                                @if($form->is_active)
                                    <span class="badge badge-success px-2 py-1 font-weight-bold" style="font-size: 9px; letter-spacing: 0.5px;">AKTIF</span>
                                @else
                                    <span class="badge badge-light text-muted px-2 py-1 font-weight-bold border" style="font-size: 9px; letter-spacing: 0.5px;">NONAKTIF</span>
                                @endif
                            </div>

                            <!-- Action buttons -->
                            <div class="d-flex align-items-center bg-light px-2 py-1 rounded-lg" style="gap: 4px; flex-shrink: 0;">
                                <a href="{{ route('master-form.preview', $form->id) }}" target="_blank" class="btn btn-sm btn-link p-1 text-info" title="Preview Tampilan Kuesioner">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button type="button"
                                    class="btn btn-sm btn-link p-1 text-secondary get-link-btn"
                                    title="Salin Link Kuesioner"
                                    data-form-role="{{ $form->target_role }}"
                                    data-form-title="{{ $form->title }}"
                                    data-form-slug="{{ \Illuminate\Support\Str::slug($form->title) }}">
                                    <i class="fas fa-link"></i>
                                </button>
                                <form action="{{ route('master-form.toggle', $form->id) }}" method="POST" class="m-0 d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-link p-1 {{ $form->is_active ? 'text-warning' : 'text-success' }}" title="{{ $form->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                        <i class="fas {{ $form->is_active ? 'fa-pause-circle' : 'fa-play-circle' }}"></i>
                                    </button>
                                </form>
                                <a href="{{ route('master-form.edit', $form->id) }}" class="btn btn-sm btn-link p-1 text-primary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('master-form.destroy', $form->id) }}" method="POST" class="m-0 d-inline" onsubmit="return confirm('Hapus form ini beserta semua pertanyaan dan responnya?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-link p-1 text-danger" title="Hapus">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Middle Row: Badges -->
                        <div class="d-flex flex-wrap align-items-center my-3" style="gap: 6px;">
                            @if($form->angkatan)
                                <span class="badge badge-primary px-2.5 py-1" style="font-size: 10px; background-color: rgba(0, 123, 255, 0.1) !important; color: #007bff !important; border: 1px solid rgba(0, 123, 255, 0.2);">
                                    <i class="fas fa-graduation-cap mr-1"></i> Angkatan {{ $form->angkatan }}
                                </span>
                            @else
                                <span class="badge badge-light px-2.5 py-1 border" style="font-size: 10px; color: #6c757d !important;">
                                    <i class="fas fa-users mr-1"></i> Semua Angkatan
                                </span>
                            @endif

                            @if($form->form_group)
                                <span class="badge badge-purple px-2.5 py-1 border" style="font-size: 10px; background-color: rgba(111, 66, 193, 0.1) !important; color: #6f42c1 !important; border-color: rgba(111, 66, 193, 0.2) !important;">
                                    <i class="fas fa-layer-group mr-1"></i> {{ $form->form_group }}
                                </span>
                            @endif
                        </div>

                        <!-- Footer Meta Info -->
                        <div class="d-flex align-items-center text-muted small pt-2 border-top" style="gap: 16px; font-size: 0.8rem;">
                            <span><i class="far fa-question-circle mr-1 text-danger"></i> <strong>{{ $form->questions_count }}</strong> pertanyaan</span>
                            <span><i class="far fa-file-alt mr-1 text-primary"></i> <strong>{{ $form->responses_count }}</strong> respons</span>
                            <span class="ml-auto"><i class="far fa-calendar-alt mr-1 text-secondary"></i> {{ $form->created_at->format('d M Y') }}</span>
                        </div>
                    </div>
                </div>
                @empty
                <div class="card text-center py-5 text-muted bg-white border" style="border-style: dashed !important; border-width: 2px !important; border-radius: 12px;">
                    <div class="card-body">
                        <i class="fas fa-file-signature fa-2x mb-2"></i>
                        <p class="small mb-0">Belum ada form untuk atasan</p>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- Get Link Modal --}}
<div class="modal fade" id="getLinkModal" tabindex="-1" role="dialog" aria-labelledby="getLinkModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 12px 40px rgba(0,0,0,0.15);">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <div class="d-flex align-items-center">
                    <div class="rounded d-flex align-items-center justify-content-center mr-3" style="width:42px;height:42px;background:rgba(128,0,0,0.1);">
                        <i class="fas fa-link text-danger"></i>
                    </div>
                    <div>
                        <h5 class="font-weight-bold text-dark mb-0" id="getLinkModalLabel">Link Kuesioner</h5>
                        <small class="text-muted" id="getLinkModalSubtitle">Bagikan link ini kepada responden</small>
                    </div>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body px-4 py-3">
                <p class="text-muted small mb-2">Bagikan link berikut kepada alumni. Alumni <strong>tidak perlu login</strong> untuk mengisi kuesioner:</p>
                <div class="input-group">
                    <input type="text" id="form-link-input" class="form-control font-weight-bold" readonly style="border-radius: 8px 0 0 8px; background: #f8f9fa; font-size:0.85rem;">
                    <div class="input-group-append">
                        <button class="btn btn-danger font-weight-bold m-0" type="button" id="copy-link-btn" style="border-radius: 0 8px 8px 0;">
                            <i class="fas fa-copy mr-1"></i> Salin
                        </button>
                    </div>
                </div>
                <div id="copy-success-msg" class="text-success small mt-2 d-none">
                    <i class="fas fa-check-circle mr-1"></i> Link berhasil disalin!
                </div>
                <hr class="my-3">
                <p class="text-muted small mb-0">
                    <i class="fas fa-check-circle mr-1 text-success"></i>
                    Alumni <strong>tidak perlu login</strong> untuk mengisi kuesioner.<br>
                    Identitas alumni dipilih langsung dari form (Universitas → Fakultas → Prodi → Nama).
                </p>
            </div>
            <div class="modal-footer border-0 pt-0 px-4 pb-4">
                <button type="button" class="btn btn-outline-secondary font-weight-bold m-0" data-dismiss="modal" style="border-radius: 8px;">Tutup</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    const FORM_LINK = '{{ route("public.form") }}';

    $('.get-link-btn').on('click', function() {
        const role = $(this).data('form-role');
        const title = $(this).data('form-title');
        const slug = $(this).data('form-slug');

        $('#getLinkModalSubtitle').text('Form: ' + title + ' (' + role + ')');
        $('#form-link-input').val(FORM_LINK + '?form=' + slug);
        $('#copy-success-msg').addClass('d-none');
        $('#getLinkModal').modal('show');
    });

    $('#copy-link-btn').on('click', function() {
        const input = document.getElementById('form-link-input');
        input.select();
        input.setSelectionRange(0, 99999);
        try {
            navigator.clipboard.writeText(input.value).then(function() {
                showCopied();
            }).catch(function() {
                document.execCommand('copy');
                showCopied();
            });
        } catch(e) {
            document.execCommand('copy');
            showCopied();
        }
    });

    function showCopied() {
        $('#copy-success-msg').removeClass('d-none');
        $('#copy-link-btn').html('<i class="fas fa-check mr-1"></i> Tersalin!');
        setTimeout(function() {
            $('#copy-success-msg').addClass('d-none');
            $('#copy-link-btn').html('<i class="fas fa-copy mr-1"></i> Salin');
        }, 2500);
    }
});
</script>
@endpush
