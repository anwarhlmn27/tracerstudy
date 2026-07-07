@extends('layouts.app')

@section('title', 'Edit Form - Tracer Study')
@section('header', 'Edit Form Kuesioner')

@section('content')


<div class="container-fluid" style="max-width: 900px; margin: 0 auto;" x-data="formBuilder()">
    <form action="{{ route('master-form.update', $form->id) }}" method="POST" @submit="prepareSubmit" onkeydown="return event.key != 'Enter';">
        @csrf
        @method('PUT')

        <!-- Form Info -->
        <div class="card builder-card mb-4">
            <div class="card-body p-4">
                <h5 class="font-weight-bold text-dark mb-4 border-bottom pb-2">Informasi Form</h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold text-dark small mb-1">Judul Form <span class="text-danger">*</span></label>
                        <input type="text" name="title" x-model="formTitle" required class="form-control" placeholder="Contoh: Kuesioner Tracer Study Alumni 2024">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold text-dark small mb-1">Target Role <span class="text-danger">*</span></label>
                        <select name="target_role" class="form-control browser-default custom-select">
                            <option value="alumni" {{ (old('target_role') ?? $form->target_role) == 'alumni' ? 'selected' : '' }}>Alumni (Student)</option>
                            <option value="atasan" {{ (old('target_role') ?? $form->target_role) == 'atasan' ? 'selected' : '' }}>Atasan</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold text-dark small mb-1">Tahun Angkatan</label>
                        <select name="angkatan" class="form-control browser-default custom-select">
                            <option value="">Semua Angkatan</option>
                            @for($year = date('Y'); $year >= 2000; $year--)
                                <option value="{{ $year }}" {{ (old('angkatan') ?? $form->angkatan) == $year ? 'selected' : '' }}>{{ $year }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold text-dark small mb-1">Grup Form</label>
                        <select name="form_group" class="form-control browser-default custom-select">
                            <option value="">Tidak ada grup</option>
                            <option value="Tracer Study (1 Year After Yudisium)" {{ (old('form_group') ?? $form->form_group) == 'Tracer Study (1 Year After Yudisium)' ? 'selected' : '' }}>Tracer Study (1 Year After Yudisium)</option>
                            <option value="Exit Survey (After Yudisium)" {{ (old('form_group') ?? $form->form_group) == 'Exit Survey (After Yudisium)' ? 'selected' : '' }}>Exit Survey (After Yudisium)</option>
                            <option value="User Survey (After Tracer Study)" {{ (old('form_group') ?? $form->form_group) == 'User Survey (After Tracer Study)' ? 'selected' : '' }}>User Survey (After Tracer Study)</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Questions Builder -->
        <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="font-weight-bold text-dark mb-0">Daftar Pertanyaan</h5>
                <div class="d-flex align-items-center" style="gap: 8px;">
                    <span class="badge badge-info px-2 py-1 font-weight-bold" x-text="sections.length + ' section'"></span>
                    <span class="badge badge-danger px-2 py-1 font-weight-bold" x-text="questions.length + ' pertanyaan'"></span>
                </div>
            </div>

            <div class="d-flex flex-column" style="gap: 15px;">
                <template x-for="(section, sIndex) in sections" :key="'sec-' + section.id">
                    <div>
                        <!-- Section Header Card -->
                        <div class="card mb-2" style="border-radius: 10px; border: 2px solid #800000; background: linear-gradient(135deg, #800000 0%, #a00000 100%);">
                            <div class="card-body py-3 px-4 d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center flex-grow-1 mr-3">
                                    <div class="d-flex align-items-center justify-content-center mr-3" style="width:32px; height:32px; background:rgba(255,255,255,0.2); border-radius:8px; flex-shrink:0;">
                                        <i class="fas fa-layer-group text-white" style="font-size:13px;"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center" style="gap:8px;">
                                            <span class="text-white font-weight-bold small" x-text="'Section ' + section.id"></span>
                                        </div>
                                        <input type="text" x-model="section.title"
                                            class="form-control form-control-sm mt-1 font-weight-bold"
                                            :placeholder="'Judul Section ' + section.id + ' (opsional)'"
                                            style="background:rgba(255,255,255,0.15); border:1px solid rgba(255,255,255,0.3); color:#fff; border-radius:6px; max-width:350px;">
                                    </div>
                                </div>
                                <div class="d-flex align-items-center" style="gap:6px;">
                                    <button type="button" class="btn btn-sm font-weight-bold"
                                        style="background:rgba(255,255,255,0.2); color:#fff; border:none; border-radius:6px; padding: 4px 10px; font-size:11px;"
                                        @click="addQuestionToSection(section.id)">
                                        <i class="fas fa-plus mr-1"></i> Pertanyaan
                                    </button>
                                    <button type="button" class="btn btn-sm"
                                        style="background:rgba(255,255,255,0.15); color:#ffcdd2; border:none; border-radius:6px; padding:4px 8px;"
                                        @click="removeSection(section.id)"
                                        x-show="sections.length > 1"
                                        title="Hapus section">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Questions in this section -->
                        <div class="d-flex flex-column pl-3" style="gap: 12px; border-left: 3px solid #800000; margin-left:12px; padding-left:16px;">
                            <template x-for="(question, qIndex) in questionsInSection(section.id)" :key="question.id">
                                <div class="card question-builder-card bg-white">
                                    <!-- Question Header -->
                                    <div class="card-header bg-light d-flex align-items-center justify-content-between py-2 px-3">
                                        <div class="d-flex align-items-center">
                                            <span class="badge badge-danger p-2 mr-2 font-weight-bold d-flex align-items-center justify-content-center" style="width: 26px; height: 26px; border-radius: 6px;" x-text="globalIndex(question.id) + 1"></span>
                                            <span class="font-weight-bold text-dark small">Pertanyaan</span>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <button type="button" class="btn btn-link p-1 text-muted mr-1" @click="moveUp(globalIndex(question.id))" x-show="globalIndex(question.id) > 0" title="Pindah ke atas">
                                                <i class="fas fa-arrow-up"></i>
                                            </button>
                                            <button type="button" class="btn btn-link p-1 text-muted mr-1" @click="moveDown(globalIndex(question.id))" x-show="globalIndex(question.id) < questions.length - 1" title="Pindah ke bawah">
                                                <i class="fas fa-arrow-down"></i>
                                            </button>
                                            <button type="button" class="btn btn-link p-1 text-danger" @click="removeQuestion(globalIndex(question.id))" title="Hapus pertanyaan">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Question Body -->
                                    <div class="card-body p-4">
                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold text-dark small mb-1">Teks Pertanyaan <span class="text-danger">*</span></label>
                                            <input type="text" x-model="question.text" required class="form-control" placeholder="Tulis pertanyaan di sini...">
                                        </div>

                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold text-dark small mb-1">
                                                Deskripsi <span class="text-muted" style="font-weight:400;">(opsional)</span>
                                            </label>
                                            <textarea x-model="question.description" rows="2"
                                                class="form-control form-control-sm"
                                                placeholder="Tambahkan penjelasan atau petunjuk pengisian untuk pertanyaan ini..."
                                                style="resize:vertical;"></textarea>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 form-group mb-3">
                                                <label class="font-weight-bold text-dark small mb-1">Tipe Pertanyaan</label>
                                                <select x-model="question.type" @change="onTypeChange(globalIndex(question.id))" class="form-control browser-default custom-select">
                                                    <option value="text">Short answer (Teks Singkat)</option>
                                                    <option value="textarea">Paragraph (Paragraf)</option>
                                                    <option value="radio">Multiple choice (Pilihan Ganda)</option>
                                                    <option value="checkbox">Checkboxes (Kotak Centang)</option>
                                                    <option value="select">Dropdown (Pilihan Turun)</option>
                                                    <option value="file">File upload (Unggah File)</option>
                                                    <option value="linear_scale">Linear scale (Skala Linear)</option>
                                                    <option value="rating">Rating (Bintang)</option>
                                                    <option value="date">Date (Tanggal)</option>
                                                    <option value="time">Time (Waktu)</option>
                                                    <option value="number">Number (Angka)</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6 d-flex align-items-center mb-3">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" :id="'required_' + question.id" x-model="question.required">
                                                    <label class="custom-control-label font-weight-bold text-dark small" :for="'required_' + question.id" style="cursor: pointer;">Wajib diisi</label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Options (for radio/select/checkbox) with Go To Section -->
                                        <div x-show="['radio', 'select', 'checkbox'].includes(question.type)" class="mt-3 p-3 bg-light border rounded">
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <label class="font-weight-bold text-dark small mb-0">Opsi Jawaban</label>
                                                <span x-show="['radio', 'select'].includes(question.type) && sections.length > 1" class="badge badge-warning small" style="font-size:9px;">
                                                    <i class="fas fa-code-branch mr-1"></i> Branching aktif
                                                </span>
                                            </div>
                                            <div class="d-flex flex-column" style="gap: 10px;">
                                                <template x-for="(option, oIndex) in question.options" :key="oIndex">
                                                    <div class="d-flex align-items-center flex-wrap" style="gap:6px;">
                                                        <span class="badge badge-light p-2 font-weight-bold" style="font-size: 10px; min-width:26px; text-align:center;" x-text="String.fromCharCode(65 + oIndex)"></span>
                                                        <input type="text" x-model="question.options[oIndex]" class="form-control form-control-sm" style="flex:1; min-width:120px;" :placeholder="'Opsi ' + (oIndex + 1)">
                                                        <template x-if="['radio', 'select'].includes(question.type) && sections.length > 1">
                                                            <select x-model="question.goToSections[oIndex]"
                                                                class="form-control form-control-sm browser-default custom-select"
                                                                style="min-width:160px; max-width:200px; font-size:11px;"
                                                                :id="'go_to_' + question.id + '_' + oIndex">
                                                                <option value="">Lanjut ke berikutnya</option>
                                                                <template x-for="sec in sections" :key="sec.id">
                                                                    <option :value="sec.id" x-text="'→ Section ' + sec.id + (sec.title ? ': ' + sec.title : '')"></option>
                                                                </template>
                                                                <option value="-1">⏹ Selesaikan form</option>
                                                            </select>
                                                        </template>
                                                        <button type="button" class="btn btn-link p-1 text-danger m-0" @click="removeOption(globalIndex(question.id), oIndex)" x-show="question.options.length > 1">
                                                            <i class="fas fa-times-circle fa-lg"></i>
                                                        </button>
                                                    </div>
                                                </template>
                                            </div>
                                            <button type="button" class="btn btn-link p-0 mt-3 text-danger font-weight-bold small text-capitalize" @click="addOption(globalIndex(question.id))">
                                                <i class="fas fa-plus mr-1"></i> Tambah Opsi
                                            </button>
                                        </div>

                                        <!-- Options for linear scale -->
                                        <div x-show="question.type === 'linear_scale'" class="mt-3 p-3 bg-light border rounded">
                                            <div class="d-flex align-items-center mb-3 flex-wrap" style="gap: 15px;">
                                                <div>
                                                    <label class="font-weight-bold text-dark small mb-1 d-block">Mulai Dari</label>
                                                    <select x-model="question.options[0]" class="form-control form-control-sm browser-default custom-select custom-select-sm" style="width: 80px;">
                                                        <option value="0">0</option>
                                                        <option value="1">1</option>
                                                    </select>
                                                </div>
                                                <span class="text-muted small mt-3">sampai</span>
                                                <div>
                                                    <label class="font-weight-bold text-dark small mb-1 d-block">Hingga</label>
                                                    <select x-model="question.options[1]" class="form-control form-control-sm browser-default custom-select custom-select-sm" style="width: 80px;">
                                                        <option value="2">2</option>
                                                        <option value="3">3</option>
                                                        <option value="4">4</option>
                                                        <option value="5">5</option>
                                                        <option value="6">6</option>
                                                        <option value="7">7</option>
                                                        <option value="8">8</option>
                                                        <option value="9">9</option>
                                                        <option value="10">10</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-6 mb-2">
                                                    <label class="font-weight-bold text-dark small mb-1">Label Nilai Terendah (Opsional)</label>
                                                    <input type="text" x-model="question.options[2]" placeholder="Contoh: Sangat Buruk" class="form-control form-control-sm">
                                                </div>
                                                <div class="col-sm-6 mb-2">
                                                    <label class="font-weight-bold text-dark small mb-1">Label Nilai Tertinggi (Opsional)</label>
                                                    <input type="text" x-model="question.options[3]" placeholder="Contoh: Sangat Baik" class="form-control form-control-sm">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Options for rating -->
                                        <div x-show="question.type === 'rating'" class="mt-3 p-3 bg-light border rounded">
                                            <label class="font-weight-bold text-dark small mb-1">Jumlah Bintang / Skala Rating</label>
                                            <select x-model="question.options[0]" class="form-control form-control-sm browser-default custom-select custom-select-sm" style="width: 150px;">
                                                <option value="5">5 Bintang</option>
                                                <option value="10">10 Bintang</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <!-- Empty section placeholder -->
                            <div x-show="questionsInSection(section.id).length === 0"
                                class="text-center py-3 text-muted border rounded bg-white"
                                style="border-style: dashed !important; border-radius: 10px;">
                                <i class="fas fa-question-circle mb-1"></i>
                                <p class="small mb-0">Belum ada pertanyaan di section ini.</p>
                            </div>
                        </div>
                    </div>
                </template>

                <div x-show="questions.length === 0 && sections.length <= 1" class="card text-center py-5 text-muted bg-white border mt-3" style="border-style: dashed !important; border-width: 2px !important; border-radius: 15px;">
                    <div class="card-body">
                        <i class="fas fa-question-circle fa-3x mb-2 text-muted"></i>
                        <p class="small mb-0">Belum ada pertanyaan. Klik tombol di bawah untuk menambahkan.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Buttons -->
        <div class="d-flex mb-4" style="gap:10px;">
            <button type="button" @click="addQuestion()" class="btn btn-outline-danger flex-grow-1 py-3 font-weight-bold" style="border-style: dashed !important; border-radius: 12px;">
                <i class="fas fa-plus mr-2"></i> Tambah Pertanyaan
            </button>
            <button type="button" @click="addSection()" class="btn btn-outline-dark py-3 font-weight-bold" style="border-style: dashed !important; border-radius: 12px; min-width:180px;">
                <i class="fas fa-layer-group mr-2"></i> Tambah Section
            </button>
        </div>

        <!-- Hidden inputs populated on submit -->
        <template x-for="(question, qIndex) in questions" :key="'hidden-' + question.id">
            <div>
                <input type="hidden" :name="'questions[' + qIndex + '][text]'" :value="question.text">
                <input type="hidden" :name="'questions[' + qIndex + '][description]'" :value="question.description">
                <input type="hidden" :name="'questions[' + qIndex + '][type]'" :value="question.type">
                <input type="hidden" :name="'questions[' + qIndex + '][required]'" :value="question.required ? 1 : 0">
                <input type="hidden" :name="'questions[' + qIndex + '][section_id]'" :value="question.sectionId">
                <input type="hidden" :name="'questions[' + qIndex + '][section_title]'" :value="getSectionTitle(question.sectionId, qIndex)">
                <template x-if="['radio', 'select', 'checkbox', 'linear_scale', 'rating'].includes(question.type)">
                    <template x-for="(opt, oIndex) in question.options" :key="'opt-' + oIndex">
                        <div>
                            <input type="hidden" :name="'questions[' + qIndex + '][options][' + oIndex + ']'" :value="opt">
                            <template x-if="['radio', 'select'].includes(question.type)">
                                <input type="hidden" :name="'questions[' + qIndex + '][go_to_sections][' + oIndex + ']'" :value="question.goToSections[oIndex] ?? ''">
                            </template>
                        </div>
                    </template>
                </template>
            </div>
        </template>

        <!-- Actions -->
        <div class="d-flex justify-content-between align-items-center mb-5 mt-4">
            <a href="{{ route('master-form.index') }}" class="btn btn-outline-danger btn-md font-weight-bold m-0" style="border-radius: 8px;">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
            <button type="submit" :disabled="questions.length === 0" class="btn btn-danger btn-md font-weight-bold m-0 d-flex align-items-center" style="border-radius: 8px;">
                <i class="fas fa-save mr-2"></i> Update Form
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<!-- Alpine JS (CDN) -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@php
    $formattedQuestions = $form->questions->filter(function($q) use ($form) {
        if ($form->target_role === 'alumni') {
            $lowerText = strtolower(trim($q->question_text));
            if (
                strpos($lowerText, 'universitas') !== false ||
                strpos($lowerText, 'fakultas') !== false ||
                strpos($lowerText, 'program studi') !== false ||
                strpos($lowerText, 'prodi') !== false ||
                (strpos($lowerText, 'nama') !== false && (strpos($lowerText, 'alumni') !== false || strpos($lowerText, 'mahasiswa') !== false))
            ) {
                return false;
            }
        }
        return true;
    })->map(function($q) {
        // Build goToSections map from options
        $goToSections = [];
        foreach ($q->options as $idx => $opt) {
            $goToSections[$idx] = $opt->go_to_section !== null ? (string)$opt->go_to_section : '';
        }
        return [
            'id' => $q->id,
            'text' => $q->question_text,
            'description' => $q->question_description ?? '',
            'type' => $q->question_type,
            'required' => (bool) $q->is_required,
            'sectionId' => $q->section_id ?? 1,
            'sectionTitle' => $q->section_title ?? '',
            'options' => $q->options->pluck('option_text')->toArray(),
            'goToSections' => $goToSections,
        ];
    })->values()->all();

    // Build sections from questions
    $sectionsMap = [];
    foreach ($form->questions as $q) {
        $sid = $q->section_id ?? 1;
        if (!isset($sectionsMap[$sid])) {
            $sectionsMap[$sid] = ['id' => $sid, 'title' => $q->section_title ?? ''];
        }
    }
    ksort($sectionsMap);
    $formattedSections = array_values($sectionsMap) ?: [['id' => 1, 'title' => '']];
    $maxSectionId = max(array_column($formattedSections, 'id'));
@endphp
<script>
function formBuilder() {
    const existingQuestions = @json($formattedQuestions);
    const existingSections = @json($formattedSections);
    const maxSectionId = @json($maxSectionId);

    return {
        formTitle: @json($form->title),
        questions: existingQuestions.map((q, i) => {
            let options = q.options;
            if (['radio', 'select', 'checkbox'].includes(q.type)) {
                options = q.options.length > 0 ? q.options : ['', ''];
            } else if (q.type === 'linear_scale') {
                options = q.options.length >= 2 ? q.options : ['1', '5', '', ''];
            } else if (q.type === 'rating') {
                options = q.options.length >= 1 ? q.options : ['5'];
            } else {
                options = [];
            }
            return {
                ...q,
                id: i + 1,
                sectionId: q.sectionId || 1,
                description: q.description || '',
                options: options,
                goToSections: q.goToSections || {},
            };
        }),
        sections: existingSections,
        nextId: existingQuestions.length + 1,
        nextSectionId: maxSectionId + 1,

        questionsInSection(sectionId) {
            return this.questions.filter(q => q.sectionId === sectionId);
        },

        globalIndex(questionId) {
            return this.questions.findIndex(q => q.id === questionId);
        },

        getSectionTitle(sectionId, qIndex) {
            const firstInSection = this.questions.findIndex(q => q.sectionId === sectionId);
            if (firstInSection === qIndex) {
                const sec = this.sections.find(s => s.id === sectionId);
                return sec ? sec.title : '';
            }
            return '';
        },

        addQuestion() {
            const currentSectionId = this.sections[this.sections.length - 1].id;
            this.questions.push({
                id: this.nextId++,
                sectionId: currentSectionId,
                text: '',
                description: '',
                type: 'text',
                required: true,
                options: ['', ''],
                goToSections: {},
            });
        },

        addQuestionToSection(sectionId) {
            this.questions.push({
                id: this.nextId++,
                sectionId: sectionId,
                text: '',
                description: '',
                type: 'text',
                required: true,
                options: ['', ''],
                goToSections: {},
            });
        },

        addSection() {
            const newId = this.nextSectionId++;
            this.sections.push({ id: newId, title: '' });
            this.questions.push({
                id: this.nextId++,
                sectionId: newId,
                text: '',
                description: '',
                type: 'text',
                required: true,
                options: ['', ''],
                goToSections: {},
            });
        },

        removeSection(sectionId) {
            if (this.sections.length <= 1) return;
            const idx = this.sections.findIndex(s => s.id === sectionId);
            const fallbackSection = this.sections[Math.max(0, idx - 1)];
            this.questions = this.questions.map(q => {
                if (q.sectionId === sectionId) q.sectionId = fallbackSection.id;
                return q;
            });
            this.sections = this.sections.filter(s => s.id !== sectionId);
            this.questions.forEach(q => {
                Object.keys(q.goToSections).forEach(k => {
                    if (parseInt(q.goToSections[k]) === sectionId) q.goToSections[k] = '';
                });
            });
        },

        removeQuestion(index) {
            this.questions.splice(index, 1);
        },

        moveUp(index) {
            if (index > 0) {
                [this.questions[index - 1], this.questions[index]] = [this.questions[index], this.questions[index - 1]];
            }
        },

        moveDown(index) {
            if (index < this.questions.length - 1) {
                [this.questions[index], this.questions[index + 1]] = [this.questions[index + 1], this.questions[index]];
            }
        },

        addOption(qIndex) {
            this.questions[qIndex].options.push('');
        },

        removeOption(qIndex, oIndex) {
            this.questions[qIndex].options.splice(oIndex, 1);
            const newGoTo = {};
            this.questions[qIndex].options.forEach((_, i) => {
                const oldVal = this.questions[qIndex].goToSections[i >= oIndex ? i + 1 : i];
                newGoTo[i] = oldVal !== undefined ? oldVal : '';
            });
            this.questions[qIndex].goToSections = newGoTo;
        },

        onTypeChange(qIndex) {
            const q = this.questions[qIndex];
            if (['radio', 'select', 'checkbox'].includes(q.type)) {
                if (!q.options || q.options.length < 2 || q.options.length > 10) q.options = ['', ''];
                q.goToSections = {};
            } else if (q.type === 'linear_scale') {
                q.options = ['1', '5', '', ''];
                q.goToSections = {};
            } else if (q.type === 'rating') {
                q.options = ['5'];
                q.goToSections = {};
            } else {
                q.options = [];
                q.goToSections = {};
            }
        },

        prepareSubmit(e) {
            if (this.questions.length === 0) {
                e.preventDefault();
                Swal.fire({ icon: 'warning', title: 'Form Kosong', text: 'Tambahkan minimal 1 pertanyaan.', confirmButtonColor: '#800000' });
                return;
            }
            for (let i = 0; i < this.questions.length; i++) {
                if (!this.questions[i].text.trim()) {
                    e.preventDefault();
                    Swal.fire({ icon: 'warning', title: 'Teks Kosong', text: 'Teks Pertanyaan #' + (i + 1) + ' belum diisi.', confirmButtonColor: '#800000' });
                    return;
                }
                if (['radio', 'select', 'checkbox'].includes(this.questions[i].type)) {
                    const filledOptions = this.questions[i].options.filter(o => o.trim() !== '');
                    if (filledOptions.length < 2) {
                        e.preventDefault();
                        Swal.fire({ icon: 'warning', title: 'Opsi Tidak Cukup', text: 'Pertanyaan #' + (i + 1) + ' membutuhkan minimal 2 opsi.', confirmButtonColor: '#800000' });
                        return;
                    }
                }
                if (this.questions[i].type === 'linear_scale') {
                    const start = parseInt(this.questions[i].options[0]);
                    const end = parseInt(this.questions[i].options[1]);
                    if (isNaN(start) || isNaN(end) || start >= end) {
                        e.preventDefault();
                        Swal.fire({ icon: 'warning', title: 'Skala Tidak Valid', text: 'Pertanyaan #' + (i + 1) + ': Nilai akhir skala linear harus lebih besar dari nilai awal.', confirmButtonColor: '#800000' });
                        return;
                    }
                }
            }
        },
    };
}
</script>
@endpush

