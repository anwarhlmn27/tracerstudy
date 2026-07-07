@extends('layouts.form')

@section('title', 'Kuesioner Tracer Study')

@section('content')


<div class="pb-5">

    {{-- Flash Messages --}}
    @if($errors->any())
        <div class="alert alert-danger border-0 rounded-lg p-3 mb-4" style="font-size: 0.85rem; background-color: #fff5f5; color: #c53030;">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-triangle fa-lg mr-3"></i>
                <div>
                    <h6 class="font-weight-bold mb-1">Periksa kembali isian Anda</h6>
                    <ul class="mb-0 pl-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{-- ============================================= --}}
    {{-- STATE: Atasan needs to select alumni first     --}}
    {{-- ============================================= --}}
    @if(isset($needsToSelectStudent) && $needsToSelectStudent)
        <!-- TomSelect CSS -->
        <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap4.min.css" rel="stylesheet">

        <div class="card form-container-card">
            <div class="bg-danger" style="height: 6px;"></div>
            <div class="card-body p-4 p-md-5">
                <div class="d-flex align-items-center mb-4">
                    <div class="rounded bg-light text-danger p-3 mr-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background-color: rgba(220, 53, 69, 0.1) !important;">
                        <i class="fas fa-user-friends fa-lg"></i>
                    </div>
                    <div>
                        <h4 class="font-weight-bold text-dark mb-0">Evaluasi Alumni</h4>
                        <p class="text-muted small mb-0">Cari dan pilih alumni yang akan Anda evaluasi.</p>
                    </div>
                </div>
                
                <form action="{{ route('form.create') }}" method="GET" class="mt-4">
                    <div class="form-group mb-4">
                        <label for="alumni-search" class="font-weight-bold text-dark small mb-2">Nama Alumni</label>
                        <select name="student_id" id="alumni-search" class="form-control" required placeholder="Ketik nama alumni (min. 3 huruf)..."></select>
                    </div>
                    <button type="submit" class="btn btn-danger font-weight-bold m-0 d-flex align-items-center" style="border-radius: 8px;">
                        Mulai Evaluasi <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                </form>
            </div>
        </div>

        @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                new TomSelect('#alumni-search', {
                    valueField: 'id',
                    labelField: 'text',
                    searchField: 'text',
                    loadThrottle: 300,
                    placeholder: 'Ketik nama alumni (min. 3 huruf)...',
                    load: function(query, callback) {
                        if (query.length < 3) return callback();
                        fetch('{{ route("form.search_alumni") }}?q=' + encodeURIComponent(query))
                            .then(response => response.json())
                            .then(json => callback(json.results))
                            .catch(() => callback());
                    }
                });
            });
        </script>
        @endpush

    {{-- ============================================= --}}
    {{-- STATE: No active form available                --}}
    {{-- ============================================= --}}
    @elseif(!$activeForm)
        <div class="card form-container-card py-5 text-center bg-white">
            <div class="card-body">
                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 70px; height: 70px;">
                    <i class="fas fa-file-excel fa-2x text-muted"></i>
                </div>
                <h4 class="font-weight-bold text-dark mb-2">Tidak Ada Kuesioner</h4>
                <p class="text-muted small max-w-md mx-auto">Belum ada kuesioner aktif untuk peran Anda saat ini. Hubungi administrator jika ini tidak seharusnya terjadi.</p>
            </div>
        </div>

    @else
        {{-- ============================================= --}}
        {{-- STATE: Already filled                         --}}
        {{-- ============================================= --}}
        @if($hasFilledActiveForm)
            <div class="card form-container-card py-5 text-center bg-white">
                <div class="bg-success" style="height: 6px; position: absolute; top: 0; left: 0; right: 0;"></div>
                <div class="card-body">
                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 70px; height: 70px; background-color: rgba(40, 167, 69, 0.1) !important;">
                        <i class="fas fa-check fa-2x text-success"></i>
                    </div>
                    <h3 class="font-weight-bold text-dark mb-2">Terima Kasih!</h3>
                    <p class="text-muted small max-w-md mx-auto mb-4">Anda sudah mengisi kuesioner <strong>"{{ $activeForm->title }}"</strong>. Jawaban Anda sangat berharga bagi peningkatan mutu layanan kami.</p>
                    
                    <div class="mt-4">
                        <a href="{{ route('profile.edit') }}" class="btn btn-outline-danger btn-md font-weight-bold m-0" style="border-radius: 8px;">
                            <i class="fas fa-user-edit mr-2"></i> Lengkapi Profil Anda
                        </a>
                    </div>
                </div>
            </div>

        {{-- ============================================= --}}
        {{-- STATE: Active form — main questionnaire       --}}
        {{-- ============================================= --}}
        @else
            <form action="{{ route('form.store') }}" method="POST" id="questionnaireForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="form_id" value="{{ $activeForm->id }}">
                @if($user->role === 'atasan')
                    <input type="hidden" name="evaluated_student_id" value="{{ $evaluatedStudentId }}">
                @endif

                {{-- ====== HEADER CARD ====== --}}
                <div class="card form-container-card mb-4">
                    <div class="bg-danger" style="height: 6px;"></div>
                    <div class="card-body p-4 p-md-5">
                        <h3 class="font-weight-bold text-dark mb-2">{{ $activeForm->title }}</h3>
                        <p class="text-muted small leading-relaxed mb-4">
                            Isi kuesioner ini dengan sebenar-benarnya. Data Anda sangat berharga bagi peningkatan mutu layanan dan kurikulum institusi kami.
                        </p>
                        <hr class="my-4">
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center" style="gap: 15px;">
                            <p class="text-danger small font-weight-bold mb-0 d-flex align-items-center">
                                <span class="rounded-circle bg-danger mr-2 d-inline-block" style="width: 6px; height: 6px;"></span>
                                Tanda <span class="text-danger font-weight-bold mx-1">*</span> menunjukkan pertanyaan wajib
                            </p>
                            <div class="d-flex align-items-center" style="gap: 10px;">
                                <small class="text-muted font-weight-bold text-nowrap" id="answered-count-text">0/{{ $activeForm->questions->filter(fn($q) => $q->question_type !== 'select')->count() }}</small>
                                <div class="progress" style="width: 100px; height: 6px; border-radius: 5px;">
                                    <div class="progress-bar progress-bar-danger" id="progress-fill-bar" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <small class="text-muted font-weight-bold text-nowrap" id="progress-percent-text">0%</small>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ====== IDENTITAS ALUMNI CARD (cascading selects) ====== --}}
                @php
                    $selectQuestions = $activeForm->questions->filter(fn($q) => $q->question_type === 'select');
                    $regularQuestions = $activeForm->questions->filter(fn($q) => $q->question_type !== 'select');
                @endphp

                @if($selectQuestions->count() > 0)
                <div class="card question-card mb-4 bg-white" style="border-left: 4px solid #800000;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded d-flex align-items-center justify-content-center mr-3" style="width: 36px; height: 36px; background: rgba(128,0,0,0.1);">
                                <i class="fas fa-id-card text-danger"></i>
                            </div>
                            <div>
                                <h6 class="font-weight-bold text-dark mb-0">Identitas Alumni</h6>
                                @if($selectQuestions->contains(fn($q) => strpos(strtolower($q->question_text), 'universitas') !== false))
                                    <small class="text-muted">Pilih universitas, fakultas, prodi, lalu nama alumni</small>
                                @else
                                    <small class="text-muted">Pilih program studi, lalu nama alumni</small>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            @foreach($selectQuestions as $sq)
                            @php
                                $lowerText = strtolower(trim($sq->question_text));
                                $selectId = '';
                                $placeholder = '-- Pilih --';
                                $icon = 'fas fa-chevron-down';
                                if (strpos($lowerText, 'universitas') !== false) {
                                    $selectId = 'question-univ';
                                    $placeholder = '-- Pilih Universitas --';
                                    $icon = 'fas fa-university';
                                } elseif (strpos($lowerText, 'fakultas') !== false) {
                                    $selectId = 'question-fakultas';
                                    $placeholder = '-- Pilih Fakultas --';
                                    $icon = 'fas fa-building';
                                } elseif (strpos($lowerText, 'program studi') !== false || strpos($lowerText, 'prodi') !== false) {
                                    $selectId = 'question-prodi';
                                    $placeholder = '-- Pilih Program Studi --';
                                    $icon = 'fas fa-book';
                                } elseif (strpos($lowerText, 'nama') !== false || strpos($lowerText, 'alumni') !== false) {
                                    $selectId = 'question-alumni';
                                    $placeholder = '-- Pilih Nama Alumni --';
                                    $icon = 'fas fa-user-graduate';
                                }
                            @endphp
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold text-dark small mb-1">
                                    <i class="{{ $icon }} mr-1 text-danger" style="font-size:0.8rem;"></i>
                                    {{ $sq->question_text }}
                                    @if($sq->is_required)<span class="text-danger">*</span>@endif
                                </label>
                                <select name="answers[{{ $sq->id }}]"
                                    @if($selectId) id="{{ $selectId }}" @endif
                                    class="form-control option-input select-form-input"
                                    data-question-id="{{ $sq->id }}"
                                    {{ $sq->is_required ? 'required' : '' }}
                                    @if($selectId !== 'question-univ') disabled @endif>
                                    <option value="">{{ $placeholder }}</option>
                                </select>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                {{-- ====== QUESTION CARDS ====== --}}
                @php $qNum = 0; @endphp
                @foreach($regularQuestions as $question)
                @php $qNum++; @endphp
                <div class="card question-card mb-3 bg-white">
                    <div class="card-body p-4">
                        <!-- Question title -->
                        <div class="d-flex align-items-start mb-3">
                            <span class="badge badge-danger p-2 mr-3 font-weight-bold d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; border-radius: 6px; font-size: 0.85rem;">
                                {{ $qNum }}
                            </span>
                            <label class="font-weight-bold text-dark h6 mb-0 leading-normal" style="font-size: 0.95rem; cursor: default;">
                                {{ $question->question_text }}
                                @if($question->is_required)
                                    <span class="text-danger font-weight-bold ml-1">*</span>
                                @endif
                            </label>
                        </div>

                        <!-- Question inputs body -->
                        <div class="pl-0 pl-sm-5 mt-2">
                            @switch($question->question_type)
 
                                 @case('text')
                                     <input type="text" name="answers[{{ $question->id }}]"
                                         value="{{ old('answers.' . $question->id) }}"
                                         class="form-control option-input"
                                         placeholder="Ketik jawaban Anda..."
                                         data-question-id="{{ $question->id }}"
                                         {{ $question->is_required ? 'required' : '' }}
                                         style="border-radius: 8px;">
                                     @break
 
                                 @case('number')
                                     <input type="number" name="answers[{{ $question->id }}]"
                                         value="{{ old('answers.' . $question->id) }}"
                                         class="form-control option-input"
                                         placeholder="0"
                                         data-question-id="{{ $question->id }}"
                                         {{ $question->is_required ? 'required' : '' }}
                                         style="border-radius: 8px; max-width: 200px;">
                                     @break
 
                                 @case('textarea')
                                     <textarea name="answers[{{ $question->id }}]" rows="3"
                                         class="form-control option-input"
                                         placeholder="Tulis jawaban Anda di sini..."
                                         data-question-id="{{ $question->id }}"
                                         {{ $question->is_required ? 'required' : '' }}
                                         style="border-radius: 8px;">{{ old('answers.' . $question->id) }}</textarea>
                                     @break
 
                                 @case('radio')
                                     <div class="row">
                                         @foreach($question->options as $option)
                                         <div class="col-sm-6 mb-2">
                                             <label class="list-group-item list-group-item-action d-flex align-items-center option-item border option-item-radio" style="border-radius: 8px; cursor: pointer; margin-bottom: 0;">
                                                 <input type="radio" name="answers[{{ $question->id }}]"
                                                        value="{{ $option->option_text }}" class="mr-3 option-input"
                                                        data-question-id="{{ $question->id }}"
                                                        {{ $question->is_required ? 'required' : '' }}
                                                        {{ old('answers.' . $question->id) == $option->option_text ? 'checked' : '' }}>
                                                 <span class="small font-weight-bold text-dark">{{ $option->option_text }}</span>
                                             </label>
                                         </div>
                                         @endforeach
                                     </div>
                                     @break
 
                                 @case('select')
                                     <select name="answers[{{ $question->id }}]"
                                         class="form-control browser-default custom-select option-input"
                                         data-question-id="{{ $question->id }}"
                                         {{ $question->is_required ? 'required' : '' }}
                                         style="border-radius: 8px;">
                                         <option value="">— Pilih salah satu —</option>
                                         @foreach($question->options as $option)
                                             <option value="{{ $option->option_text }}" {{ old('answers.' . $question->id) == $option->option_text ? 'selected' : '' }}>
                                                 {{ $option->option_text }}
                                             </option>
                                         @endforeach
                                     </select>
                                     @break
 
                                 @case('checkbox')
                                     @php $oldCheckbox = old('answers.' . $question->id, []); @endphp
                                     <div class="row">
                                         @foreach($question->options as $option)
                                         <div class="col-sm-6 mb-2">
                                             <label class="list-group-item list-group-item-action d-flex align-items-center option-item border option-item-check" style="border-radius: 8px; cursor: pointer; margin-bottom: 0;">
                                                 <input type="checkbox" name="answers[{{ $question->id }}][]"
                                                        value="{{ $option->option_text }}" class="mr-3 option-input option-checkbox"
                                                        data-question-id="{{ $question->id }}"
                                                        {{ is_array($oldCheckbox) && in_array($option->option_text, $oldCheckbox) ? 'checked' : '' }}>
                                                 <span class="small font-weight-bold text-dark">{{ $option->option_text }}</span>
                                             </label>
                                         </div>
                                         @endforeach
                                     </div>
                                     @break
 
                                 @case('file')
                                     <div class="form-group mb-0">
                                         <input type="file" name="answers[{{ $question->id }}]"
                                             class="form-control option-input"
                                             data-question-id="{{ $question->id }}"
                                             {{ $question->is_required ? 'required' : '' }}
                                             style="border-radius: 8px; padding-top: 6px;">
                                         <small class="text-muted d-block mt-1">Pilih berkas dari komputer Anda.</small>
                                     </div>
                                     @break
 
                                 @case('linear_scale')
                                     @php
                                         $minVal = (int) ($question->options->where('sort_order', 0)->first()->option_text ?? 1);
                                         $maxVal = (int) ($question->options->where('sort_order', 1)->first()->option_text ?? 5);
                                         $minLabel = $question->options->where('sort_order', 2)->first()->option_text ?? '';
                                         $maxLabel = $question->options->where('sort_order', 3)->first()->option_text ?? '';
                                     @endphp
                                     <div class="d-flex align-items-center flex-wrap" style="gap: 15px;">
                                         @if($minLabel)
                                             <span class="text-muted small font-weight-bold">{{ $minLabel }}</span>
                                         @endif
                                         <div class="btn-group btn-group-toggle py-1" data-toggle="buttons">
                                             @for($i = $minVal; $i <= $maxVal; $i++)
                                                 <label class="btn btn-outline-danger px-3 py-2 m-0 {{ old('answers.' . $question->id) == $i ? 'active' : '' }}" style="border-radius: 6px; font-weight: bold;">
                                                     <input type="radio" name="answers[{{ $question->id }}]" value="{{ $i }}" class="option-input option-radio-scale" data-question-id="{{ $question->id }}" {{ $question->is_required ? 'required' : '' }} {{ old('answers.' . $question->id) == $i ? 'checked' : '' }}> {{ $i }}
                                                 </label>
                                             @endfor
                                         </div>
                                         @if($maxLabel)
                                             <span class="text-muted small font-weight-bold">{{ $maxLabel }}</span>
                                         @endif
                                     </div>
                                     @break
 
                                 @case('rating')
                                     @php
                                         $maxStars = (int) ($question->options->where('sort_order', 0)->first()->option_text ?? 5);
                                         $currentRating = old('answers.' . $question->id) ?: 0;
                                     @endphp
                                     <div class="rating-stars" data-question-id="{{ $question->id }}">
                                         <input type="hidden" name="answers[{{ $question->id }}]" value="{{ $currentRating }}" class="rating-value option-input" data-question-id="{{ $question->id }}" {{ $question->is_required ? 'required' : '' }}>
                                         @for($star = 1; $star <= $maxStars; $star++)
                                             <button type="button" class="btn btn-link p-1 m-0 star-btn" data-value="{{ $star }}" style="font-size: 1.8rem; text-decoration: none;">
                                                 @if($star <= $currentRating)
                                                     <i class="fas fa-star text-warning"></i>
                                                 @else
                                                     <i class="far fa-star text-muted"></i>
                                                 @endif
                                             </button>
                                         @endfor
                                     </div>
                                     @break
 
                                 @case('date')
                                     <input type="date" name="answers[{{ $question->id }}]"
                                         value="{{ old('answers.' . $question->id) }}"
                                         class="form-control option-input"
                                         data-question-id="{{ $question->id }}"
                                         {{ $question->is_required ? 'required' : '' }}
                                         style="border-radius: 8px; max-width: 250px;">
                                     @break
 
                                 @case('time')
                                     <input type="time" name="answers[{{ $question->id }}]"
                                         value="{{ old('answers.' . $question->id) }}"
                                         class="form-control option-input"
                                         data-question-id="{{ $question->id }}"
                                         {{ $question->is_required ? 'required' : '' }}
                                         style="border-radius: 8px; max-width: 200px;">
                                     @break

                                 @case('select')
                                     @php
                                         $lowerText = trim(strtolower($question->question_text));
                                         $selectId = '';
                                         $placeholder = '-- Pilih Pilihan --';
                                         if (strpos($lowerText, 'universitas') !== false) {
                                             $selectId = 'question-univ';
                                             $placeholder = '-- Pilih Universitas --';
                                         } elseif (strpos($lowerText, 'fakultas') !== false) {
                                             $selectId = 'question-fakultas';
                                             $placeholder = '-- Pilih Fakultas --';
                                         } elseif (strpos($lowerText, 'program studi') !== false || strpos($lowerText, 'prodi') !== false) {
                                             $selectId = 'question-prodi';
                                             $placeholder = '-- Pilih Program Studi --';
                                         } elseif (strpos($lowerText, 'nama') !== false || strpos($lowerText, 'alumni') !== false || strpos($lowerText, 'student') !== false) {
                                             $selectId = 'question-alumni';
                                             $placeholder = '-- Pilih Nama Alumni --';
                                         }
                                     @endphp
                                     
                                     <select name="answers[{{ $question->id }}]"
                                             @if($selectId) id="{{ $selectId }}" @endif
                                             class="form-control option-input select-form-input"
                                             data-question-id="{{ $question->id }}"
                                             {{ $question->is_required ? 'required' : '' }}
                                             style="border-radius: 8px;">
                                         <option value="">{{ $placeholder }}</option>
                                         @if(!$selectId)
                                             @foreach($question->options as $option)
                                                 <option value="{{ $option->option_text }}">{{ $option->option_text }}</option>
                                             @endforeach
                                         @endif
                                     </select>
                                     @break
                            @endswitch
                        </div>
                    </div>
                </div>
                @endforeach

                {{-- ====== SUBMIT AREA ====== --}}
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center pt-4" style="gap: 20px;">
                    <button type="button" class="btn btn-link p-0 text-muted font-weight-bold text-capitalize order-2 order-sm-1" onclick="window.scrollTo({top: 0, behavior: 'smooth'})" style="text-decoration: none; font-size: 0.9rem;">
                        <i class="fas fa-arrow-up mr-2"></i> Kembali ke atas
                    </button>
                    <button type="submit" class="btn btn-danger btn-md font-weight-bold m-0 w-100 w-sm-auto order-1 order-sm-2 d-flex align-items-center justify-content-center" style="border-radius: 10px;">
                        <i class="fas fa-paper-plane mr-2"></i> Kirim Jawaban
                    </button>
                </div>
            </form>
        @endif
    @endif

</div>
@endsection
@push('styles')
<!-- TomSelect CSS for form dropdowns -->
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap4.min.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
    $(document).ready(function() {
        // Cascading dynamic selects for questionnaire
        const univSelect = document.getElementById('question-univ');
        const fakultasSelect = document.getElementById('question-fakultas');
        const prodiSelect = document.getElementById('question-prodi');
        const alumniSelect = document.getElementById('question-alumni');

        if (prodiSelect && alumniSelect) {
            let alumniTomSelect = null;

            function resetAlumniSelect() {
                if (alumniTomSelect) {
                    alumniTomSelect.destroy();
                    alumniTomSelect = null;
                }
                $(alumniSelect).html('<option value="">-- Pilih Nama Alumni --</option>').prop('disabled', true);
            }

            // Case A: University dropdown is present
            if (univSelect) {
                // Load universities
                $.getJSON('/api/univs', function(data) {
                    $.each(data, function(i, u) {
                        const opt = $('<option></option>')
                            .val(u.nama_univ)
                            .attr('data-id', u.id)
                            .text(u.nama_univ);
                        $(univSelect).append(opt);
                    });
                });

                // Univ change
                $(univSelect).on('change', function() {
                    const selectedOpt = this.options[this.selectedIndex];
                    const univId = $(selectedOpt).attr('data-id');

                    $(fakultasSelect).html('<option value="">-- Pilih Fakultas --</option>').prop('disabled', true);
                    $(prodiSelect).html('<option value="">-- Pilih Program Studi --</option>').prop('disabled', true);
                    resetAlumniSelect();

                    if (!univId) return;

                    $.getJSON('/api/fakultas', { univ_id: univId }, function(data) {
                        $.each(data, function(i, f) {
                            const opt = $('<option></option>')
                                .val(f.nama_fakultas)
                                .attr('data-id', f.id)
                                .text(f.nama_fakultas);
                            $(fakultasSelect).append(opt);
                        });
                        $(fakultasSelect).prop('disabled', false);
                    });
                });

                // Fakultas change
                $(fakultasSelect).on('change', function() {
                    const selectedOpt = this.options[this.selectedIndex];
                    const fakultasId = $(selectedOpt).attr('data-id');

                    $(prodiSelect).html('<option value="">-- Pilih Program Studi --</option>').prop('disabled', true);
                    resetAlumniSelect();

                    if (!fakultasId) return;

                    $.getJSON('/api/prodis', { fakultas_id: fakultasId }, function(data) {
                        $.each(data, function(i, p) {
                            const opt = $('<option></option>')
                                .val(p.nama_prodi)
                                .attr('data-id', p.id)
                                .text(p.nama_prodi);
                            $(prodiSelect).append(opt);
                        });
                        $(prodiSelect).prop('disabled', false);
                    });
                });
            } else {
                // Case B: University dropdown is NOT present, load all prodis on page load
                $(prodiSelect).html('<option value="">-- Pilih Program Studi --</option>').prop('disabled', true);
                $.getJSON('/api/prodis', function(data) {
                    $.each(data, function(i, p) {
                        const opt = $('<option></option>')
                            .val(p.nama_prodi)
                            .attr('data-id', p.id)
                            .text(p.nama_prodi);
                        $(prodiSelect).append(opt);
                    });
                    $(prodiSelect).prop('disabled', false);
                });
            }

            // Both cases share the prodi change listener to load alumni
            $(prodiSelect).on('change', function() {
                const selectedOpt = this.options[this.selectedIndex];
                const prodiId = $(selectedOpt).attr('data-id');

                resetAlumniSelect();

                if (!prodiId) return;

                $.getJSON('/api/students', { prodi_id: prodiId, form_id: '{{ $activeForm->id ?? "" }}' }, function(data) {
                    $(alumniSelect).html('<option value="">-- Pilih Nama Alumni --</option>');
                    $.each(data, function(i, s) {
                        const label = s.nama_student + ' (' + s.nim + ')' + (s.has_submitted ? ' - Sudah Mengisi' : '');
                        const opt = $('<option></option>')
                            .val(s.nama_student)
                            .attr('data-id', s.id)
                            .text(label);
                        if (s.has_submitted) {
                            opt.prop('disabled', true).attr('disabled', 'disabled');
                        }
                        $(alumniSelect).append(opt);
                    });
                    $(alumniSelect).prop('disabled', false);

                    if (alumniTomSelect) {
                        alumniTomSelect.destroy();
                    }
                    alumniTomSelect = new TomSelect('#question-alumni', {
                        create: false,
                        sortField: {
                            field: "text",
                            direction: "asc"
                        },
                        placeholder: 'Ketik nama alumni...',
                    });
                });
            });
        }

        // Star rating click logic
        $('.star-btn').on('click', function() {
            const rating = $(this).data('value');
            const wrapper = $(this).closest('.rating-stars');
            wrapper.find('.rating-value').val(rating).trigger('change');
            
            // Highlight stars
            wrapper.find('.star-btn').each(function() {
                const val = $(this).data('value');
                const icon = $(this).find('i');
                if (val <= rating) {
                    icon.removeClass('far fa-star text-muted').addClass('fas fa-star text-warning');
                } else {
                    icon.removeClass('fas fa-star text-warning').addClass('far fa-star text-muted');
                }
            });
        });

        // Initialize click classes on load for check/radios
        $('input[type="radio"]:checked').closest('.option-item-radio').addClass('active-option');
        $('input[type="checkbox"]:checked').closest('.option-item-check').addClass('active-option');

        // Handles class active triggers on input change
        $(document).on('change', 'input[type="radio"].option-input', function() {
            const name = $(this).attr('name');
            $('input[name="' + name + '"]').closest('.option-item-radio').removeClass('active-option');
            if ($(this).is(':checked')) {
                $(this).closest('.option-item-radio').addClass('active-option');
            }
        });

        $(document).on('change', 'input[type="checkbox"].option-input', function() {
            if ($(this).is(':checked')) {
                $(this).closest('.option-item-check').addClass('active-option');
            } else {
                $(this).closest('.option-item-check').removeClass('active-option');
            }
        });

        // Answer progress tracker
        function updateProgress() {
            const answered = {};
            
            // Check text inputs, numbers, textareas, selects, dates, times, file inputs
            $('input[type="text"], input[type="number"], input[type="date"], input[type="time"], input[type="file"], textarea, select').each(function() {
                const val = $(this).val();
                const name = $(this).attr('name');
                if (val && val.trim() !== '' && name) {
                    answered[name] = true;
                }
            });
            
            // Check radios (general & scale)
            $('input[type="radio"]:checked').each(function() {
                const name = $(this).attr('name');
                if (name) {
                    answered[name] = true;
                }
            });
            
            // Check checkboxes
            $('.option-checkbox:checked').each(function() {
                const name = $(this).attr('name');
                if (name) {
                    answered[name] = true;
                }
            });
            
            // Check rating inputs
            $('.rating-value').each(function() {
                const val = $(this).val();
                const name = $(this).attr('name');
                if (val && val !== '0' && name) {
                    answered[name] = true;
                }
            });
            
            const totalQuestions = {{ isset($activeForm) && !($hasFilledActiveForm ?? false) ? $activeForm->questions->filter(fn($q) => $q->question_type !== 'select')->count() : 0 }};
            const answeredCount = Object.keys(answered).length;
            const percent = totalQuestions > 0 ? Math.round((answeredCount / totalQuestions) * 100) : 0;
            
            $('#answered-count-text').text(answeredCount + '/' + totalQuestions);
            $('#progress-fill-bar').css('width', percent + '%');
            $('#progress-percent-text').text(percent + '%');
        }

        // Bind events for real-time progress update
        $(document).on('change input', 'input, textarea, select', function() {
            updateProgress();
        });

        // Initialize progress bar on load
        if ($('#questionnaireForm').length > 0) {
            updateProgress();
        }
    });
</script>
@endpush
