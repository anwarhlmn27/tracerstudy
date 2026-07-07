@extends('layouts.app')

@section('title', 'Questionnaires - Tracer Study')
@section('header', 'Questionnaire Responses')

@section('content')


<div class="container-fluid">
    <div class="card q-card">
        <div class="card-body p-0">
            <!-- Header section -->
            <div class="px-4 py-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center border-bottom">
                <div>
                    <h5 class="font-weight-bold text-dark mb-1">Daftar Respons Kuesioner</h5>
                    <p class="text-muted small mb-0">Semua respons tracer study dari mahasiswa</p>
                </div>
                
                <div class="d-flex flex-column flex-sm-row align-items-center mt-3 mt-md-0" style="gap: 10px;">
                    <form method="GET" action="{{ route('questionnaires.index') }}" class="w-100 w-sm-auto">
                        <select name="angkatan" onchange="this.form.submit()" class="browser-default custom-select font-weight-bold text-muted" style="border-radius: 10px; height: 42px;">
                            <option value="">Semua Angkatan</option>
                            @foreach($angkatanList as $angkatan)
                                <option value="{{ $angkatan }}" {{ $selectedAngkatan == $angkatan ? 'selected' : '' }}>
                                    Angkatan {{ $angkatan }}
                                </option>
                            @endforeach
                        </select>
                    </form>

                    <a href="{{ route('questionnaires.export', ['angkatan' => request('angkatan')]) }}" class="btn btn-success btn-md font-weight-bold m-0 w-100 w-sm-auto d-flex align-items-center justify-content-center" style="border-radius: 10px; height: 42px;">
                        <i class="fas fa-file-excel mr-2"></i> Export to Excel
                    </a>
                </div>
            </div>

            <!-- Summary Cards Section -->
            <div class="row p-4 bg-light border-bottom no-gutters">
                <div class="col-md-4 px-2 mb-3 mb-md-0">
                    <div class="card stat-mini-card bg-white">
                        <div class="card-body d-flex align-items-center py-3">
                            <div class="rounded p-2 bg-primary text-primary mr-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background-color: rgba(0, 123, 255, 0.15) !important;">
                                <i class="fas fa-file-invoice fa-lg"></i>
                            </div>
                            <div>
                                <h4 class="font-weight-bold text-dark mb-0">{{ $responses->count() }}</h4>
                                <small class="text-muted">Total Respons Form</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 px-2 mb-3 mb-md-0">
                    <div class="card stat-mini-card bg-white">
                        <div class="card-body py-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="d-flex align-items-center">
                                    <div class="rounded p-2 bg-success text-success mr-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background-color: rgba(40, 167, 69, 0.15) !important;">
                                        <i class="fas fa-user-graduate fa-lg"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-weight-bold text-dark mb-0">{{ $alumniFilled }} <span style="font-size: 0.8rem; font-weight: normal; color: #777;">/ {{ $totalAlumni }}</span></h4>
                                        <small class="text-muted">Alumni Mengisi</small>
                                    </div>
                                </div>
                                @php $alumniPercent = $totalAlumni > 0 ? round(($alumniFilled / $totalAlumni) * 100) : 0; @endphp
                                <span class="font-weight-bold text-success" style="font-size: 0.95rem;">{{ $alumniPercent }}%</span>
                            </div>
                            <div class="progress" style="height: 5px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $alumniPercent }}%" aria-valuenow="{{ $alumniPercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 px-2">
                    <div class="card stat-mini-card bg-white">
                        <div class="card-body py-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="d-flex align-items-center">
                                    <div class="rounded p-2 bg-indigo text-indigo mr-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background-color: rgba(79, 70, 229, 0.15) !important; color: #4f46e5 !important;">
                                        <i class="fas fa-user-tie fa-lg"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-weight-bold text-dark mb-0">{{ $atasanFilled }} <span style="font-size: 0.8rem; font-weight: normal; color: #777;">/ {{ $totalAtasan }}</span></h4>
                                        <small class="text-muted">Atasan Mengisi</small>
                                    </div>
                                </div>
                                @php $atasanPercent = $totalAtasan > 0 ? round(($atasanFilled / $totalAtasan) * 100) : 0; @endphp
                                <span class="font-weight-bold text-indigo" style="font-size: 0.95rem; color: #4f46e5;">{{ $atasanPercent }}%</span>
                            </div>
                            <div class="progress" style="height: 5px;">
                                <div class="progress-bar bg-indigo" role="progressbar" style="width: {{ $atasanPercent }}%; background-color: #4f46e5 !important;" aria-valuenow="{{ $atasanPercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table section -->
            <div class="table-responsive p-3">
                <table id="questionnaireTable" class="table table-hover w-100">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 px-3">No</th>
                            <th class="border-0">Timestamp</th>
                            <th class="border-0">Target Role</th>
                            <th class="border-0">Nama Responden</th>
                            <th class="border-0">Judul Form</th>
                            <th class="border-0">Total Jawaban</th>
                            <th class="border-0 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($responses as $index => $response)
                        @php
                            $roleTarget = $response->form->target_role ?? '-';
                            $namaResponden = $response->user->name ?? '-';
                            if ($roleTarget === 'alumni' && $response->user->student) {
                                $namaResponden = $response->user->student->nama_student;
                            }
                            $answersArray = $response->answers->map(function($a) {
                                return [
                                    'question' => $a->question->question_text ?? 'Pertanyaan Dihapus',
                                    'answer' => $a->answer_text ?? '-'
                                ];
                            })->values()->toArray();
                        @endphp
                        <tr>
                            <td class="px-3 align-middle font-weight-bold text-dark">{{ $index + 1 }}</td>
                            <td class="align-middle">
                                <div class="font-weight-bold text-dark" style="font-size: 0.8rem;">{{ $response->created_at->format('d M Y') }}</div>
                                <small class="text-muted">{{ $response->created_at->format('H:i:s') }}</small>
                            </td>
                            <td class="align-middle">
                                <span class="badge badge-role" style="
                                    {{ $roleTarget === 'alumni' ? 'background-color: rgba(5, 150, 105, 0.15); color: #059669;' : 'background-color: rgba(79, 70, 229, 0.15); color: #4f46e5;' }}">
                                    {{ $roleTarget }}
                                </span>
                            </td>
                            <td class="align-middle">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center mr-2 font-weight-bold" style="width: 28px; height: 28px; font-size: 0.7rem;">
                                        {{ strtoupper(substr($namaResponden, 0, 1)) }}
                                    </div>
                                    <span class="font-weight-bold text-dark">{{ $namaResponden }}</span>
                                </div>
                            </td>
                            <td class="align-middle font-weight-bold text-muted text-truncate" style="max-width: 180px;" title="{{ $response->form->title ?? '-' }}">
                                {{ $response->form->title ?? '-' }}
                            </td>
                            <td class="align-middle">
                                <span class="badge badge-light px-2.5 py-1.5 rounded font-weight-bold" style="font-size: 10px;">
                                    {{ $response->answers->count() }} Jawaban
                                </span>
                            </td>
                            <td class="align-middle text-center">
                                <button type="button" 
                                    class="btn btn-outline-primary btn-sm font-weight-bold px-3 py-1.5 m-0 detail-btn"
                                    style="border-radius: 8px;"
                                    data-name="{{ $namaResponden }}"
                                    data-form="{{ $response->form->title ?? '-' }}"
                                    data-answers="{{ json_encode($answersArray) }}">
                                    <i class="fas fa-eye mr-1"></i> Detail
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="py-5 text-center text-muted">
                                <i class="fas fa-file-invoice fa-2x mb-3 d-block"></i>
                                Belum ada respons kuesioner.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Detail Modal -->
<div class="modal fade" id="detailResponseModal" tabindex="-1" role="dialog" aria-labelledby="detailResponseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <div>
                    <h5 class="modal-title font-weight-bold text-dark" id="modalName">Detail Respons: </h5>
                    <small class="text-muted" id="modalForm">Form: </small>
                </div>
                <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4" id="modalBody" style="max-height: 70vh; overflow-y: auto;">
                <!-- Dynamically filled in JS -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-danger btn-md font-weight-bold" data-dismiss="modal" style="border-radius: 8px;">Tutup</button>
            </div>
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
        if ($('#questionnaireTable tbody tr').length > 0 && !$('#questionnaireTable tbody tr td[colspan]').length) {
            $('#questionnaireTable').DataTable({
                responsive: true,
                order: [[1, 'desc']],
                language: {
                    url: '{{ asset('js_old/datatables-id.json') }}',
                }
            });
        }

        // Handle detail button click
        $('.detail-btn').on('click', function() {
            const name = $(this).data('name');
            const formTitle = $(this).data('form');
            const answers = $(this).data('answers');
            
            $('#modalName').text('Detail Respons: ' + name);
            $('#modalForm').text('Form: ' + formTitle);
            
            let html = '';
            if (answers && answers.length > 0) {
                answers.forEach((item, index) => {
                    html += `
                        <div class="bg-light rounded p-3 mb-3 border" style="border-radius: 12px !important;">
                            <p class="text-uppercase font-weight-bold text-danger mb-1" style="font-size: 0.7rem; letter-spacing: 0.05em;">Pertanyaan ${index + 1}</p>
                            <p class="font-weight-bold text-dark mb-2" style="font-size: 0.9rem;">${item.question}</p>
                            <div class="bg-white rounded p-3 border" style="border-radius: 8px !important;">
                    `;
                    if (item.answer && item.answer.startsWith('uploads/questions/')) {
                        html += `
                                <a href="/${item.answer}" target="_blank" class="font-weight-bold text-primary text-uppercase" style="font-size: 0.8rem; text-decoration: none;">
                                    <i class="fas fa-download mr-1"></i> Lihat Berkas / Unduh
                                </a>
                        `;
                    } else {
                        html += `
                                <p class="mb-0 text-dark" style="font-size: 0.85rem; white-space: pre-wrap;">${item.answer || '-'}</p>
                        `;
                    }
                    html += `
                            </div>
                        </div>
                    `;
                });
            } else {
                html = '<div class="text-center py-4 text-muted">Tidak ada jawaban untuk ditampilkan.</div>';
            }
            
            $('#modalBody').html(html);
            $('#detailResponseModal').modal('show');
        });
    });
</script>
@endpush
