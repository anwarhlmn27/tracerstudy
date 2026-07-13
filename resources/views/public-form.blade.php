@extends('layouts.public')

@section('title', 'Kuesioner Tracer Study')

@section('content')


<div class="pb-5">

    {{-- Flash / Validation Errors --}}
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

    @if(!$activeForm)
        {{-- No active form --}}
        <div class="card form-container-card py-5 text-center bg-white">
            <div class="card-body">
                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 70px; height: 70px;">
                    <i class="fas fa-file-excel fa-2x text-muted"></i>
                </div>
                <h4 class="font-weight-bold text-dark mb-2">Belum Ada Kuesioner Aktif</h4>
                <p class="text-muted small">Saat ini belum ada kuesioner yang tersedia. Silakan coba kembali nanti.</p>
            </div>
        </div>

    @elseif($hasFilledActiveForm)
        {{-- Already filled --}}
        <div class="card form-container-card py-5 text-center bg-white" style="position: relative; overflow: hidden;">
            <div class="bg-success" style="height: 6px; position: absolute; top: 0; left: 0; right: 0;"></div>
            <div class="card-body">
                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 70px; height: 70px; background-color: rgba(40, 167, 69, 0.1) !important;">
                    <i class="fas fa-check fa-2x text-success"></i>
                </div>
                <h3 class="font-weight-bold text-dark mb-2">Terima Kasih!</h3>
                <p class="text-muted small mx-auto mb-0" style="max-width: 420px;">Jawaban Anda untuk kuesioner <strong>"{{ $activeForm->title }}"</strong> telah berhasil disimpan. Data Anda sangat berharga bagi kami.</p>
            </div>
        </div>

    @else
        {{-- Main Questionnaire Form --}}
        <form action="{{ route('public.form.store') }}" method="POST" id="questionnaireForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="form_id" value="{{ $activeForm->id }}">

            {{-- ====== HEADER CARD ====== --}}
            <div class="card form-container-card mb-4">
                <div class="bg-danger" style="height: 6px;"></div>
                <div class="card-body p-4 p-md-5">
                    <h3 class="font-weight-bold text-dark mb-2">{{ $activeForm->title }}</h3>
                    <p class="text-muted small mb-4">
                        Isi kuesioner ini dengan sebenar-benarnya. Data Anda sangat berharga bagi peningkatan mutu layanan dan kurikulum institusi kami.
                    </p>
                    <hr class="my-4">
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center" style="gap: 15px;">
                        <p class="text-danger small font-weight-bold mb-0 d-flex align-items-center">
                            <span class="rounded-circle bg-danger mr-2 d-inline-block" style="width: 6px; height: 6px;"></span>
                            Tanda <span class="text-danger font-weight-bold mx-1">*</span> menunjukkan pertanyaan wajib
                        </p>
                        <div class="d-flex align-items-center" style="gap: 10px;">
                            <small class="text-muted font-weight-bold text-nowrap" id="answered-count-text">
                                0/{{ $activeForm->questions->filter(fn($q) => $q->question_type !== 'select')->count() }}
                            </small>
                            <div class="progress" style="width: 100px; height: 6px; border-radius: 5px;">
                                <div class="progress-bar progress-bar-danger" id="progress-fill-bar" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <small class="text-muted font-weight-bold text-nowrap" id="progress-percent-text">0%</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ====== IDENTITAS ALUMNI CARD ====== --}}
            @php
                $selectQuestions = $activeForm->questions->filter(function($q) {
                    if ($q->question_type !== 'select') return false;
                    $lower = strtolower(trim($q->question_text));
                    return strpos($lower, 'universitas') !== false
                        || strpos($lower, 'fakultas') !== false
                        || strpos($lower, 'program studi') !== false
                        || strpos($lower, 'prodi') !== false
                        || (strpos($lower, 'nama') !== false && (strpos($lower, 'alumni') !== false || strpos($lower, 'mahasiswa') !== false));
                });
                $regularQuestions = $activeForm->questions->reject(function($q) use ($selectQuestions) {
                    return $selectQuestions->contains('id', $q->id);
                });
            @endphp

            @if($selectQuestions->count() > 0)
            <div class="card identitas-card mb-4 bg-white">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="rounded d-flex align-items-center justify-content-center mr-3" style="width: 40px; height: 40px; background: rgba(128,0,0,0.1); flex-shrink: 0;">
                            <i class="fas fa-id-card text-danger"></i>
                        </div>
                        <div>
                            <h6 class="font-weight-bold text-dark mb-0">Identitas Alumni</h6>
                            @if($selectQuestions->contains(fn($q) => strpos(strtolower($q->question_text), 'universitas') !== false))
                                <small class="text-muted">Pilih universitas, fakultas, prodi, lalu nama Anda</small>
                            @else
                                <small class="text-muted">Pilih program studi, lalu nama alumni</small>
                            @endif
                        </div>
                    </div>

                    <div class="row">
                        @foreach($selectQuestions as $sq)
                        @php
                            $lt = strtolower(trim($sq->question_text));
                            $selectId = $placeholder = $icon = '';
                            if (strpos($lt, 'universitas') !== false) {
                                $selectId = 'question-univ'; $placeholder = '-- Pilih Universitas --'; $icon = 'fas fa-university';
                            } elseif (strpos($lt, 'fakultas') !== false) {
                                $selectId = 'question-fakultas'; $placeholder = '-- Pilih Fakultas --'; $icon = 'fas fa-building';
                            } elseif (strpos($lt, 'program studi') !== false || strpos($lt, 'prodi') !== false) {
                                $selectId = 'question-prodi'; $placeholder = '-- Pilih Program Studi --'; $icon = 'fas fa-book';
                            } elseif (strpos($lt, 'nama') !== false || strpos($lt, 'alumni') !== false) {
                                $selectId = 'question-alumni'; $placeholder = '-- Pilih Nama Anda --'; $icon = 'fas fa-user-graduate';
                            }
                        @endphp
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold text-dark small mb-1">
                                @if($icon)<i class="{{ $icon }} mr-1 text-danger" style="font-size: 0.8rem;"></i>@endif
                                {{ $sq->question_text }}
                                @if($sq->is_required)<span class="text-danger ml-1">*</span>@endif
                            </label>
                            <select name="answers[{{ $sq->id }}]"
                                @if($selectId) id="{{ $selectId }}" @endif
                                class="form-control option-input"
                                data-question-id="{{ $sq->id }}"
                                {{ $sq->is_required ? 'required' : '' }}
                                @if($selectId && $selectId !== 'question-univ') disabled @endif>
                                <option value="">{{ $placeholder ?: '-- Pilih --' }}</option>
                            </select>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            {{-- ====== QUESTION CARDS (with section support) ====== --}}
            @php
                $qNum = 0;
                // Determine if form uses sections (any question has section_id > 1)
                $useSections = $regularQuestions->where('section_id', '>', 1)->count() > 0 || $regularQuestions->where('section_id', 1)->count() !== $regularQuestions->count();
                $sectionIds = $regularQuestions->pluck('section_id')->filter()->unique()->sort()->values();
                $numSections = $sectionIds->count();
            @endphp

            @foreach($regularQuestions as $question)
            @php
                $qNum++;
                $isFirstInSection = $regularQuestions->first(fn($q) => $q->section_id == $question->section_id) && $regularQuestions->firstWhere('section_id', $question->section_id)?->id === $question->id;
                $sectionNum = $sectionIds->search($question->section_id) + 1;
            @endphp

            {{-- Section Header --}}
            @if($useSections && $isFirstInSection && $numSections > 1)
            <div class="section-header-public mb-3 mt-2"
                data-section-id="{{ $question->section_id }}"
                @if($sectionNum > 1) style="display:none;" @endif>
                <div class="d-flex align-items-center" style="gap:12px;">
                    <div class="d-flex align-items-center justify-content-center" style="width:36px;height:36px;background:rgba(128,0,0,0.1);border-radius:8px;flex-shrink:0;">
                        <i class="fas fa-layer-group text-danger" style="font-size:14px;"></i>
                    </div>
                    <div>
                        <div class="font-weight-bold text-dark" style="font-size:0.9rem;">Section {{ $sectionNum }}
                            @if($question->section_title)
                                — <span class="text-muted">{{ $question->section_title }}</span>
                            @endif
                        </div>
                        <small class="text-muted">{{ $regularQuestions->where('section_id', $question->section_id)->count() }} pertanyaan</small>
                    </div>
                    @if($numSections > 1)
                    <div class="ml-auto">
                        <span class="badge badge-light font-weight-bold" style="font-size:10px;">{{ $sectionNum }}/{{ $numSections }}</span>
                    </div>
                    @endif
                </div>
                <hr class="mt-2 mb-0">
            </div>
            @endif

            <div class="card question-card mb-3 bg-white"
                data-question-section="{{ $question->section_id ?? 1 }}"
                @if($useSections && $numSections > 1 && $sectionNum > 1) style="display:none;" @endif>
                <div class="card-body p-4">
                    <div class="d-flex align-items-start mb-3">
                        <span class="badge badge-danger p-2 mr-3 font-weight-bold d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; border-radius: 6px; font-size: 0.85rem; flex-shrink: 0;">
                            {{ $qNum }}
                        </span>
                        <label class="font-weight-bold text-dark h6 mb-0" style="font-size: 0.95rem; cursor: default; line-height: 1.5;">
                            {{ $question->question_text }}
                            @if($question->is_required)
                                <span class="text-danger font-weight-bold ml-1">*</span>
                            @endif
                        </label>
                    </div>
                    @if(!empty($question->question_description))
                    <p class="text-muted small mb-3 pl-0 pl-sm-5" style="font-style: italic; line-height: 1.5; margin-top: -4px;">
                        {{ $question->question_description }}
                    </p>
                    @endif

                    <div class="pl-0 pl-sm-5 mt-2">
                        @switch($question->question_type)

                            @case('text')
                                <input type="text" name="answers[{{ $question->id }}]"
                                    value="{{ old('answers.' . $question->id) }}"
                                    class="form-control option-input"
                                    placeholder="Ketik jawaban Anda..."
                                    data-question-id="{{ $question->id }}"
                                    {{ $question->is_required ? 'required' : '' }}>
                                @break

                            @case('number')
                                <input type="number" name="answers[{{ $question->id }}]"
                                    value="{{ old('answers.' . $question->id) }}"
                                    class="form-control option-input"
                                    placeholder="0"
                                    data-question-id="{{ $question->id }}"
                                    {{ $question->is_required ? 'required' : '' }}
                                    style="max-width: 200px;">
                                @break

                            @case('textarea')
                                <textarea name="answers[{{ $question->id }}]" rows="3"
                                    class="form-control option-input"
                                    placeholder="Tulis jawaban Anda di sini..."
                                    data-question-id="{{ $question->id }}"
                                    {{ $question->is_required ? 'required' : '' }}>{{ old('answers.' . $question->id) }}</textarea>
                                @break

                            @case('radio')
                                <div class="row">
                                    @foreach($question->options as $option)
                                    <div class="col-sm-6 mb-2">
                                        <label class="list-group-item list-group-item-action d-flex align-items-center option-item border option-item-radio" style="border-radius: 8px; cursor: pointer; margin-bottom: 0;">
                                            <input type="radio" name="answers[{{ $question->id }}]"
                                                value="{{ $option->option_text }}" class="mr-3 option-input"
                                                data-question-id="{{ $question->id }}"
                                                data-go-to-section="{{ $option->go_to_section ?? '' }}"
                                                {{ $question->is_required ? 'required' : '' }}
                                                {{ old('answers.' . $question->id) == $option->option_text ? 'checked' : '' }}>
                                            <span class="small font-weight-bold text-dark">{{ $option->option_text }}</span>
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                                @break

                            @case('checkbox')
                                @php $oldCheckbox = old('answers.' . $question->id, []); @endphp
                                <div class="row">
                                    @foreach($question->options as $option)
                                    <div class="col-sm-6 mb-2">
                                        <label class="list-group-item list-group-item-action d-flex align-items-center option-item border option-item-check" style="border-radius: 8px; cursor: pointer; margin-bottom: 0;">
                                            <input type="checkbox" name="answers[{{ $question->id }}][]"
                                                value="{{ $option->option_text }}" class="mr-3 option-checkbox option-input"
                                                data-question-id="{{ $question->id }}"
                                                {{ is_array($oldCheckbox) && in_array($option->option_text, $oldCheckbox) ? 'checked' : '' }}>
                                            <span class="small font-weight-bold text-dark">{{ $option->option_text }}</span>
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                                @break

                            @case('select')
                                <select name="answers[{{ $question->id }}]"
                                    class="form-control option-input"
                                    data-question-id="{{ $question->id }}"
                                    {{ $question->is_required ? 'required' : '' }}>
                                    <option value="">-- Pilih --</option>
                                    @foreach($question->options as $option)
                                        <option value="{{ $option->option_text }}"
                                            data-go-to-section="{{ $option->go_to_section ?? '' }}"
                                            {{ old('answers.' . $question->id) == $option->option_text ? 'selected' : '' }}>
                                            {{ $option->option_text }}
                                        </option>
                                    @endforeach
                                </select>
                                @break

                            @case('date')
                                <input type="date" name="answers[{{ $question->id }}]"
                                    value="{{ old('answers.' . $question->id) }}"
                                    class="form-control option-input"
                                    data-question-id="{{ $question->id }}"
                                    {{ $question->is_required ? 'required' : '' }}
                                    style="max-width: 250px;">
                                @break

                            @case('time')
                                <input type="time" name="answers[{{ $question->id }}]"
                                    value="{{ old('answers.' . $question->id) }}"
                                    class="form-control option-input"
                                    data-question-id="{{ $question->id }}"
                                    {{ $question->is_required ? 'required' : '' }}
                                    style="max-width: 200px;">
                                @break

                            @default
                                <input type="text" name="answers[{{ $question->id }}]"
                                    value="{{ old('answers.' . $question->id) }}"
                                    class="form-control option-input"
                                    placeholder="Ketik jawaban Anda..."
                                    data-question-id="{{ $question->id }}"
                                    {{ $question->is_required ? 'required' : '' }}>
                        @endswitch
                    </div>
                </div>
            </div>
            @endforeach

            {{-- Section Navigation Buttons (shown when numSections > 1) --}}
            @if($useSections && $numSections > 1)
            @php
                // Build section metadata for JS
                $sectionMeta = [];
                foreach($sectionIds as $sid) {
                    $sectionMeta[] = ['id' => $sid];
                }
            @endphp
            <div id="section-nav-area" class="d-flex justify-content-between align-items-center pt-4" style="gap: 20px;">
                <button type="button" id="prev-section-btn"
                    class="btn btn-outline-secondary font-weight-bold m-0" style="border-radius: 10px; display:none;">
                    <i class="fas fa-arrow-left mr-2"></i> Section Sebelumnya
                </button>
                <button type="button" id="next-section-btn"
                    class="btn btn-danger font-weight-bold m-0 ml-auto" style="border-radius: 10px;">
                    Lanjutkan <i class="fas fa-arrow-right ml-2"></i>
                </button>
            </div>
            @endif

            {{-- ====== SUBMIT ====== --}}
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center pt-4"
                id="submit-area"
                @if($useSections && $numSections > 1) style="display:none;" @endif
                style="gap: 20px;">
                <button type="button" class="btn btn-link p-0 text-muted font-weight-bold text-capitalize order-2 order-sm-1"
                    onclick="window.scrollTo({top: 0, behavior: 'smooth'})" style="text-decoration: none; font-size: 0.9rem;">
                    <i class="fas fa-arrow-up mr-2"></i> Kembali ke atas
                </button>
                <button type="button" id="submit-form-btn" class="btn btn-danger btn-md font-weight-bold m-0 w-100 w-sm-auto order-1 order-sm-2 d-flex align-items-center justify-content-center" style="border-radius: 10px;">
                    <i class="fas fa-paper-plane mr-2"></i> Kirim Jawaban
                </button>
            </div>
        </form>
    @endif

</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap4.min.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
$(document).ready(function() {
    // ---- Cascading Selects ----
    const univSelect    = document.getElementById('question-univ');
    const fakultasSelect = document.getElementById('question-fakultas');
    const prodiSelect   = document.getElementById('question-prodi');
    const alumniSelect  = document.getElementById('question-alumni');

    if (prodiSelect && alumniSelect) {
        let alumniTomSelect = null;

        function resetAlumniSelect() {
            if (alumniTomSelect) { alumniTomSelect.destroy(); alumniTomSelect = null; }
            $(alumniSelect).html('<option value="">-- Pilih Nama Anda --</option>').prop('disabled', true);
        }

        // Case A: University dropdown is present
        if (univSelect) {
            // Load universities on page load
            $.getJSON('{{ url("/api/univs") }}', function(data) {
                $.each(data, function(i, u) {
                    $(univSelect).append($('<option>').val(u.nama_univ).attr('data-id', u.id).text(u.nama_univ));
                });
            });

            $(univSelect).on('change', function() {
                const univId = $(this.options[this.selectedIndex]).attr('data-id');
                $(fakultasSelect).html('<option value="">-- Pilih Fakultas --</option>').prop('disabled', true);
                $(prodiSelect).html('<option value="">-- Pilih Program Studi --</option>').prop('disabled', true);
                resetAlumniSelect();
                if (!univId) return;
                $.getJSON('{{ url("/api/fakultas") }}', { univ_id: univId }, function(data) {
                    $.each(data, function(i, f) {
                        $(fakultasSelect).append($('<option>').val(f.nama_fakultas).attr('data-id', f.id).text(f.nama_fakultas));
                    });
                    $(fakultasSelect).prop('disabled', false);
                });
            });

            $(fakultasSelect).on('change', function() {
                const fId = $(this.options[this.selectedIndex]).attr('data-id');
                $(prodiSelect).html('<option value="">-- Pilih Program Studi --</option>').prop('disabled', true);
                resetAlumniSelect();
                if (!fId) return;
                $.getJSON('{{ url("/api/prodis") }}', { fakultas_id: fId }, function(data) {
                    $.each(data, function(i, p) {
                        $(prodiSelect).append($('<option>').val(p.nama_prodi).attr('data-id', p.id).text(p.nama_prodi));
                    });
                    $(prodiSelect).prop('disabled', false);
                });
            });
        } else {
            // Case B: University dropdown is NOT present, load all prodis on page load
            $(prodiSelect).html('<option value="">-- Pilih Program Studi --</option>').prop('disabled', true);
            $.getJSON('{{ url("/api/prodis") }}', function(data) {
                $.each(data, function(i, p) {
                    $(prodiSelect).append($('<option>').val(p.nama_prodi).attr('data-id', p.id).text(p.nama_prodi));
                });
                $(prodiSelect).prop('disabled', false);
            });
        }

        // Both cases share the prodi change listener to load alumni
        $(prodiSelect).on('change', function() {
            const pId = $(this.options[this.selectedIndex]).attr('data-id');
            resetAlumniSelect();
            if (!pId) return;
            $.getJSON('{{ url("/api/students") }}', { prodi_id: pId, form_id: '{{ $activeForm->id ?? "" }}' }, function(data) {
                $(alumniSelect).html('<option value="">-- Pilih Nama Anda --</option>');
                $.each(data, function(i, s) {
                    const label = s.nama_student + ' (' + s.nim + ')' + (s.has_submitted ? ' - Sudah Mengisi' : '');
                    const opt = $('<option>').val(s.nama_student).attr('data-id', s.id).text(label);
                    if (s.has_submitted) {
                        opt.prop('disabled', true).attr('disabled', 'disabled');
                    }
                    $(alumniSelect).append(opt);
                });
                $(alumniSelect).prop('disabled', false);
                if (alumniTomSelect) alumniTomSelect.destroy();
                alumniTomSelect = new TomSelect('#question-alumni', {
                    create: false,
                    sortField: { field: 'text', direction: 'asc' },
                    placeholder: 'Ketik nama untuk mencari...',
                });
            });
        });
    }

    // ---- Radio/Checkbox active classes ----
    $('input[type="radio"]:checked').closest('.option-item-radio').addClass('active-option');
    $('input[type="checkbox"]:checked').closest('.option-item-check').addClass('active-option');

    $(document).on('change', 'input[type="radio"].option-input', function() {
        const name = $(this).attr('name');
        $('input[name="' + name + '"]').closest('.option-item-radio').removeClass('active-option');
        if ($(this).is(':checked')) $(this).closest('.option-item-radio').addClass('active-option');
    });
    $(document).on('change', 'input[type="checkbox"].option-input', function() {
        $(this).closest('.option-item-check').toggleClass('active-option', $(this).is(':checked'));
    });

    // ---- Progress Tracker ----
    const totalQuestions = {{ $activeForm ? $activeForm->questions->filter(fn($q) => $q->question_type !== 'select')->count() : 0 }};

    function updateProgress() {
        const answered = {};
        $('input[type="text"], input[type="number"], input[type="date"], input[type="time"], textarea').each(function() {
            const val = $(this).val(), name = $(this).attr('name');
            if (val && val.trim() !== '' && name) answered[name] = true;
        });
        $('input[type="radio"]:checked').each(function() {
            const name = $(this).attr('name');
            if (name) answered[name] = true;
        });
        $('.option-checkbox:checked').each(function() {
            const name = $(this).attr('name');
            if (name) answered[name.replace('[]', '')] = true;
        });
        const count = Object.keys(answered).length;
        const pct   = totalQuestions > 0 ? Math.round((count / totalQuestions) * 100) : 0;
        $('#answered-count-text').text(count + '/' + totalQuestions);
        $('#progress-fill-bar').css('width', pct + '%');
        $('#progress-percent-text').text(pct + '%');
    }

    $(document).on('change input', 'input, textarea', function() { updateProgress(); });
    if ($('#questionnaireForm').length) updateProgress();

    // ---- Section Navigation (Branching) ----
    @if($activeForm)
    @php
        $hasMultipleSections = isset($useSections) && $useSections && isset($numSections) && $numSections > 1;
    @endphp
    @if($hasMultipleSections)
    (function() {
        const sectionIds = @json($sectionIds->values());
        let sectionHistory = [sectionIds[0]]; // breadcrumb for back navigation
        let currentSectionId = sectionIds[0];

        function showSection(sid) {
            // Hide all sections
            $('.question-card, .section-header-public').hide();
            // Show cards/headers for current section
            $('[data-question-section="' + sid + '"]').show();
            $('[data-section-id="' + sid + '"]').show();
            currentSectionId = sid;

            // Determine if this is the last section
            const isLast = (sectionIds.indexOf(Number(sid)) === sectionIds.length - 1);
            const isFirst = (sectionHistory.length <= 1);

            $('#prev-section-btn').toggle(!isFirst);
            if (isLast) {
                $('#next-section-btn').hide();
                $('#submit-area').show();
            } else {
                $('#next-section-btn').show();
                $('#submit-area').hide();
            }
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function getNextSectionFromBranching() {
            // Check if any visible radio/select in current section has a go_to_section set
            let goTo = null;
            $('[data-question-section="' + currentSectionId + '"] input[type="radio"]:checked[data-go-to-section]').each(function() {
                const val = $(this).data('go-to-section');
                if (val !== '' && val !== null && val !== undefined) {
                    goTo = parseInt(val);
                }
            });
            // Check select dropdowns
            $('[data-question-section="' + currentSectionId + '"] select.option-input').each(function() {
                const selectedOpt = $(this).find('option:selected');
                const val = selectedOpt.data('go-to-section');
                if (val !== '' && val !== null && val !== undefined) {
                    goTo = parseInt(val);
                }
            });
            return goTo;
        }

        function getNextSectionDefault() {
            const idx = sectionIds.indexOf(Number(currentSectionId));
            return idx < sectionIds.length - 1 ? sectionIds[idx + 1] : null;
        }

        $('#next-section-btn').on('click', function() {
            // Validate required fields in current section before continuing
            let isValid = true;
            $('[data-question-section="' + currentSectionId + '"] [required]').each(function() {
                const el = $(this);
                const type = el.attr('type');
                if (type === 'radio') {
                    const name = el.attr('name');
                    if ($('input[name="' + name + '"]:checked').length === 0) {
                        isValid = false;
                        el.closest('.card').addClass('border-danger');
                    } else {
                        el.closest('.card').removeClass('border-danger');
                    }
                } else {
                    if (!el.val() || el.val().trim() === '') {
                        isValid = false;
                        el.addClass('is-invalid');
                    } else {
                        el.removeClass('is-invalid');
                    }
                }
            });

            if (!isValid) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Lengkapi Jawaban',
                    text: 'Harap isi semua pertanyaan yang wajib (*) di section ini terlebih dahulu.',
                    confirmButtonColor: '#800000'
                });
                return;
            }

            // Determine where to go
            const branchTarget = getNextSectionFromBranching();

            if (branchTarget === -1) {
                // End of form
                $('#next-section-btn').hide();
                $('#section-nav-area').hide();
                $('#submit-area').show();
                window.scrollTo({ top: 0, behavior: 'smooth' });
                return;
            }

            const nextSid = branchTarget !== null ? branchTarget : getNextSectionDefault();
            if (nextSid) {
                sectionHistory.push(nextSid);
                showSection(nextSid);
            }
        });

        $('#prev-section-btn').on('click', function() {
            if (sectionHistory.length > 1) {
                sectionHistory.pop();
                showSection(sectionHistory[sectionHistory.length - 1]);
            }
        });

        // Initialize: show first section
        showSection(sectionIds[0]);
    })();
    @endif
    @endif

    // ---- Form Submit Validation ----
    $('#submit-form-btn').on('click', function() {
        const totalQ = {{ $activeForm ? $activeForm->questions->count() : 0 }};

        // 1. Cek jika form tidak punya pertanyaan sama sekali
        if (totalQ === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Form Belum Siap',
                text: 'Form ini belum memiliki pertanyaan. Hubungi administrator untuk menambahkan pertanyaan terlebih dahulu.',
                confirmButtonColor: '#800000',
                confirmButtonText: 'Mengerti'
            });
            return;
        }

        // 2. Cek field required yang belum diisi (only visible cards)
        let missingFields = [];
        $('#questionnaireForm .question-card:visible [required]').each(function() {
            const el = $(this);
            const type = el.attr('type') || el.prop('tagName').toLowerCase();
            const name = el.attr('name');
            let isEmpty = false;

            if (type === 'radio') {
                const groupName = name.replace(/\[/g, '\\[').replace(/\]/g, '\\]');
                if ($('input[name="' + name + '"]:checked').length === 0) {
                    const label = el.closest('.card').find('label h6, label.font-weight-bold').first().text().trim();
                    if (!missingFields.includes(label) && label) missingFields.push(label);
                }
            } else if (type === 'checkbox') {
                // handled by required
            } else {
                if (!el.val() || el.val().trim() === '') {
                    isEmpty = true;
                }
            }

            if (isEmpty) {
                const label = el.closest('.card').find('label.font-weight-bold').first().text().trim()
                    || el.closest('.col-md-6').find('label').first().text().trim();
                if (label && !missingFields.includes(label)) missingFields.push(label);
                el.addClass('is-invalid');
            } else {
                el.removeClass('is-invalid');
            }
        });

        if (missingFields.length > 0) {
            Swal.fire({
                icon: 'error',
                title: 'Lengkapi Jawaban',
                html: '<p class="mb-2">Harap isi semua pertanyaan yang wajib (<span style="color:red">*</span>) sebelum mengirim.</p>',
                confirmButtonColor: '#800000',
                confirmButtonText: 'Isi Sekarang'
            });
            const firstInvalid = $('#questionnaireForm .is-invalid').first();
            if (firstInvalid.length) {
                $('html, body').animate({ scrollTop: firstInvalid.offset().top - 120 }, 400);
            }
            return;
        }

        // 3. Konfirmasi sebelum submit
        Swal.fire({
            icon: 'question',
            title: 'Kirim Jawaban?',
            text: 'Pastikan semua jawaban sudah benar sebelum dikirim. Jawaban tidak dapat diubah setelah dikirim.',
            showCancelButton: true,
            confirmButtonColor: '#800000',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-paper-plane"></i> Ya, Kirim!',
            cancelButtonText: 'Periksa Lagi'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#questionnaireForm').submit();
            }
        });
    });
});
</script>
@endpush
