<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TracerResponseController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AlumniController;
use App\Http\Controllers\QuestionnaireController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\MasterFormController;
use App\Http\Controllers\PublicFormController;

Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ===== PUBLIC QUESTIONNAIRE (no login required) =====
Route::get('/kuesioner', [PublicFormController::class, 'show'])->name('public.form');
Route::post('/kuesioner', [PublicFormController::class, 'store'])->name('public.form.store');

// Public cascading dropdown APIs (no auth)
Route::get('/api/univs', function() {
    return response()->json(\App\Models\Univ::orderBy('nama_univ', 'asc')->get(['id', 'nama_univ']));
});
Route::get('/api/fakultas', function(\Illuminate\Http\Request $request) {
    return response()->json(\App\Models\Fakultas::where('id_univs', $request->query('univ_id'))->orderBy('nama_fakultas', 'asc')->get(['id', 'nama_fakultas']));
});
Route::get('/api/prodis', function(\Illuminate\Http\Request $request) {
    $query = \App\Models\Prodi::query();
    if ($request->has('fakultas_id') && $request->query('fakultas_id') !== '') {
        $query->where('fakultas_id', $request->query('fakultas_id'));
    }
    return response()->json($query->orderBy('nama_prodi', 'asc')->get(['id', 'nama_prodi']));
});
Route::get('/api/students', function(\Illuminate\Http\Request $request) {
    $prodiId = $request->query('prodi_id');
    $formId = $request->query('form_id');

    $students = \App\Models\Student::where('prodi_id', $prodiId)
        ->orderBy('nama_student', 'asc')
        ->get(['id', 'nama_student', 'nim']);

    if ($formId) {
        $form = \App\Models\QuestionnaireForm::find($formId);
        if ($form) {
            $formIds = [];
            if ($form->form_group) {
                $formIds = \App\Models\QuestionnaireForm::where('form_group', $form->form_group)
                    ->pluck('id')
                    ->toArray();
            } else {
                $formIds = [$form->id];
            }

            $submittedStudentIds = \App\Models\FormResponse::whereIn('form_responses.form_id', $formIds)
                ->leftJoin('students', 'form_responses.user_id', '=', 'students.user_id')
                ->selectRaw('COALESCE(form_responses.guest_student_id, students.id) as student_id')
                ->pluck('student_id')
                ->filter()
                ->unique()
                ->toArray();

            foreach ($students as $student) {
                $student->has_submitted = in_array($student->id, $submittedStudentIds);
            }
        }
    }

    return response()->json($students);
});

Route::middleware('auth')->group(function () {
    // Dashboard: admin & dosen
    Route::middleware('role:admin,dosen')->group(function () {
        Route::get('/dashboard', function () {
            $totalAlumni = \App\Models\Student::count();
            
            $alumniResponseCount = \App\Models\FormResponse::whereHas('form', function($q) {
                $q->where('target_role', 'alumni');
            })->distinct('user_id')->count('user_id');
            
            $responseRate = $totalAlumni > 0 ? round(($alumniResponseCount / $totalAlumni) * 100, 1) : 0;
            
            $recentResponses = \App\Models\FormResponse::with(['user.student.prodi', 'form'])
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
                
            $totalForms = \App\Models\QuestionnaireForm::count();
            $activeForms = \App\Models\QuestionnaireForm::where('is_active', true)->count();

            // Helper closure: query answer counts for a question by keyword
            $answerCounts = function (string $keyword) {
                return \App\Models\FormResponseAnswer::whereHas('question', function ($q) use ($keyword) {
                    $q->whereRaw('LOWER(question_text) LIKE ?', ['%' . strtolower($keyword) . '%']);
                })
                ->whereNotNull('answer_text')
                ->where('answer_text', '!=', '')
                ->selectRaw('answer_text, COUNT(*) as total')
                ->groupBy('answer_text')
                ->orderByDesc('total')
                ->pluck('total', 'answer_text')
                ->toArray();
            };

            // Waktu Tunggu
            $rawWaktuTunggu = \App\Models\FormResponseAnswer::whereHas('question', function ($q) {
                    $q->whereRaw('LOWER(question_text) LIKE ?', ['%waktu tunggu%']);
                })
                ->whereNotNull('answer_text')
                ->where('answer_text', '!=', '')
                ->pluck('answer_text');

            $waktuTungguBuckets = [
                '0 bulan' => 0, '1-3 bulan' => 0, '4-6 bulan' => 0,
                '7-12 bulan' => 0, '> 12 bulan' => 0,
            ];
            foreach ($rawWaktuTunggu as $val) {
                $num = (int) filter_var($val, FILTER_SANITIZE_NUMBER_INT);
                if ($num <= 0)       $waktuTungguBuckets['0 bulan']++;
                elseif ($num <= 3)   $waktuTungguBuckets['1-3 bulan']++;
                elseif ($num <= 6)   $waktuTungguBuckets['4-6 bulan']++;
                elseif ($num <= 12)  $waktuTungguBuckets['7-12 bulan']++;
                else                 $waktuTungguBuckets['> 12 bulan']++;
            }
            $waktuTungguLabels = array_keys($waktuTungguBuckets);
            $waktuTungguData   = array_values($waktuTungguBuckets);

            // Skala Tempat Kerja
            $skalaTempat = $answerCounts('skala tempat kerja');

            // Distribusi Pendapatan
            $pendapatanData = $answerCounts('pendapatan');

            // Kesesuaian Pekerjaan dengan Prodi
            $kesesuaianData = $answerCounts('sesuai dengan program studi');

            return view('dashboard', compact(
                'totalAlumni', 'responseRate', 'recentResponses', 'totalForms', 'activeForms',
                'waktuTungguLabels', 'waktuTungguData', 'skalaTempat', 'pendapatanData', 'kesesuaianData'
            ));
        })->name('dashboard');
    });

    // Form: alumni (student), atasan
    Route::middleware(['role:alumni,atasan', 'incomplete_profile'])->group(function () {
        Route::get('/form', [TracerResponseController::class, 'create'])->name('form.create');
        Route::post('/form', [TracerResponseController::class, 'store'])->name('form.store');
        Route::get('/form/search-alumni', [TracerResponseController::class, 'searchAlumni'])->name('form.search_alumni');
    });

    // Profile (all authenticated users)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Admin & Dosen only routes
    Route::middleware('role:admin,dosen')->group(function () {
        // Alumni Data CRUD
        Route::get('/alumni', [AlumniController::class, 'index'])->name('alumni.index');
        Route::post('/alumni', [AlumniController::class, 'store'])->name('alumni.store');
        Route::put('/alumni/{id}', [AlumniController::class, 'update'])->name('alumni.update');
        Route::delete('/alumni/{id}', [AlumniController::class, 'destroy'])->name('alumni.destroy');
        Route::post('/alumni/import', [AlumniController::class, 'import'])->name('alumni.import');
        Route::get('/alumni/template', [AlumniController::class, 'downloadTemplate'])->name('alumni.template');

        // Master Prodi CRUD
        Route::get('/prodi', [\App\Http\Controllers\ProdiController::class, 'index'])->name('prodi.index');
        Route::post('/prodi', [\App\Http\Controllers\ProdiController::class, 'store'])->name('prodi.store');
        Route::put('/prodi/{id}', [\App\Http\Controllers\ProdiController::class, 'update'])->name('prodi.update');
        Route::delete('/prodi/{id}', [\App\Http\Controllers\ProdiController::class, 'destroy'])->name('prodi.destroy');

        // Master Universitas CRUD
        Route::get('/univs', [\App\Http\Controllers\UnivController::class, 'index'])->name('univ.index');
        Route::post('/univs', [\App\Http\Controllers\UnivController::class, 'store'])->name('univ.store');
        Route::put('/univs/{id}', [\App\Http\Controllers\UnivController::class, 'update'])->name('univ.update');
        Route::delete('/univs/{id}', [\App\Http\Controllers\UnivController::class, 'destroy'])->name('univ.destroy');

        // Master Fakultas CRUD
        Route::get('/fakultas', [\App\Http\Controllers\FakultasController::class, 'index'])->name('fakultas.index');
        Route::post('/fakultas', [\App\Http\Controllers\FakultasController::class, 'store'])->name('fakultas.store');
        Route::put('/fakultas/{id}', [\App\Http\Controllers\FakultasController::class, 'update'])->name('fakultas.update');
        Route::delete('/fakultas/{id}', [\App\Http\Controllers\FakultasController::class, 'destroy'])->name('fakultas.destroy');

        // Questionnaires
        Route::get('/questionnaires', [QuestionnaireController::class, 'index'])->name('questionnaires.index');
        Route::get('/questionnaires/export', [QuestionnaireController::class, 'export'])->name('questionnaires.export');

        // Reports & Analytics
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/filter-options', [ReportController::class, 'filterOptions'])->name('reports.filterOptions');

        // Email Blast
        Route::get('/email', [\App\Http\Controllers\EmailBlastController::class, 'index'])->name('email.index');
        Route::post('/email/send', [\App\Http\Controllers\EmailBlastController::class, 'send'])->name('email.send');

        // Settings
        Route::get('/settings', [\App\Http\Controllers\SettingController::class, 'index'])->name('settings');
        Route::post('/settings', [\App\Http\Controllers\SettingController::class, 'update'])->name('settings.update');
    });

    // Admin only routes
    Route::middleware('role:admin')->group(function () {
        // Master Form
        Route::get('/master-form', [MasterFormController::class, 'index'])->name('master-form.index');
        Route::get('/master-form/create', [MasterFormController::class, 'create'])->name('master-form.create');
        Route::post('/master-form', [MasterFormController::class, 'store'])->name('master-form.store');
        Route::get('/master-form/{id}/edit', [MasterFormController::class, 'edit'])->name('master-form.edit');
        Route::get('/master-form/{id}/preview', [PublicFormController::class, 'preview'])->name('master-form.preview');
        Route::put('/master-form/{id}', [MasterFormController::class, 'update'])->name('master-form.update');
        Route::delete('/master-form/{id}', [MasterFormController::class, 'destroy'])->name('master-form.destroy');
        Route::patch('/master-form/{id}/toggle', [MasterFormController::class, 'toggleActive'])->name('master-form.toggle');
    });
});
