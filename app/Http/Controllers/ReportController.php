<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Prodi;
use App\Models\Fakultas;
use App\Models\Univ;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Display the report & analytics page.
     */
    public function index(Request $request)
    {
        $univId     = $request->input('univ_id', 0);
        $fakultasId = $request->input('fakultas_id', 0);
        $prodiId    = $request->input('prodi_id', 0);

        $formGroup  = $request->input('form_group');
        $angkatan   = $request->input('angkatan');

        // Helper: scope student query by active filters
        $studentScope = function ($query) use ($univId, $fakultasId, $prodiId, $angkatan) {
            if ($prodiId) {
                $query->where('prodi_id', $prodiId);
            } elseif ($fakultasId) {
                $query->whereHas('prodi', fn($q) => $q->where('fakultas_id', $fakultasId));
            } elseif ($univId) {
                $query->whereHas('prodi.fakultas', fn($q) => $q->where('id_univs', $univId));
            }
            if ($angkatan) {
                $query->where('angkatan', $angkatan);
            }
        };

        // Helper: scope formResponse query by active filters (via user -> student OR guestStudent)
        $responseScope = function ($query) use ($univId, $fakultasId, $prodiId, $angkatan, $formGroup) {
            if ($formGroup) {
                $query->whereHas('form', fn($q) => $q->where('form_group', $formGroup));
            }
            if ($prodiId || $fakultasId || $univId || $angkatan) {
                $query->where(function ($q) use ($univId, $fakultasId, $prodiId, $angkatan) {
                    // Match authenticated user -> student
                    $q->whereHas('user.student', function ($sq) use ($univId, $fakultasId, $prodiId, $angkatan) {
                        if ($prodiId) {
                            $sq->where('prodi_id', $prodiId);
                        } elseif ($fakultasId) {
                            $sq->whereHas('prodi', fn($q2) => $q2->where('fakultas_id', $fakultasId));
                        } elseif ($univId) {
                            $sq->whereHas('prodi.fakultas', fn($q2) => $q2->where('id_univs', $univId));
                        }
                        if ($angkatan) {
                            $sq->where('angkatan', $angkatan);
                        }
                    });
                    // OR match guest student
                    $q->orWhereHas('guestStudent', function ($sq) use ($univId, $fakultasId, $prodiId, $angkatan) {
                        if ($prodiId) {
                            $sq->where('prodi_id', $prodiId);
                        } elseif ($fakultasId) {
                            $sq->whereHas('prodi', fn($q2) => $q2->where('fakultas_id', $fakultasId));
                        } elseif ($univId) {
                            $sq->whereHas('prodi.fakultas', fn($q2) => $q2->where('id_univs', $univId));
                        }
                        if ($angkatan) {
                            $sq->where('angkatan', $angkatan);
                        }
                    });
                });
            }
        };

        // Helper: scope formResponseAnswer by active filters
        $answerScope = function ($query) use ($responseScope) {
            $query->whereHas('response', function($q) use ($responseScope) {
                $responseScope($q);
            });
        };

        // 1. Overview stats
        $totalStudents = Student::when(true, $studentScope)->count();
        $totalResponses = \App\Models\FormResponse::when(true, $responseScope)->count();

        // Count alumni who submitted (distinct by either user_id or guest_student_id)
        $alumniResponses = \App\Models\FormResponse::whereHas('form', function ($q) {
            $q->where('target_role', 'alumni');
        })->when(true, $responseScope)->get();
        
        $alumniSubmittedCount = $alumniResponses->map(function($r) {
            return $r->user_id ? 'user_'.$r->user_id : 'guest_'.$r->guest_student_id;
        })->unique()->count();

        $alumniResponseCount = $alumniSubmittedCount;
        $responseRate = $totalStudents > 0 ? round(($alumniResponseCount / $totalStudents) * 100, 1) : 0;

        $atasanResponses = \App\Models\FormResponse::whereHas('form', function ($q) {
            $q->where('target_role', 'atasan');
        })->when(true, $responseScope)->get();
        
        $atasanResponseCount = $atasanResponses->map(function($r) {
            return $r->user_id ? 'user_'.$r->user_id : 'guest_'.$r->guest_student_id;
        })->unique()->count();

        // 2. Perbandingan yang sudah mengisi vs belum per Prodi (Bar chart)
        $prodisQuery = Prodi::withCount(['students' => function ($q) use ($studentScope) {
            $studentScope($q);
        }]);

        if ($prodiId) {
            $prodisQuery->where('id', $prodiId);
        } elseif ($fakultasId) {
            $prodisQuery->where('fakultas_id', $fakultasId);
        } elseif ($univId) {
            $prodisQuery->whereHas('fakultas', fn($q) => $q->where('id_univs', $univId));
        }

        $prodis = $prodisQuery->get();
        $prodiLabels = [];
        $sudahMengisi = [];
        $belumMengisi = [];

        $alumniUserIdsWithResponses = $alumniResponses->whereNotNull('user_id')->pluck('user_id')->toArray();
        $alumniStudentIdsWithResponses = $alumniResponses->whereNotNull('guest_student_id')->pluck('guest_student_id')->toArray();

        foreach ($prodis as $prodi) {
            $prodiLabels[] = $prodi->nama_prodi;
            $filledCount = Student::where('prodi_id', $prodi->id)
                ->where(function($q) use ($alumniUserIdsWithResponses, $alumniStudentIdsWithResponses) {
                    if (!empty($alumniUserIdsWithResponses)) {
                        $q->whereIn('user_id', $alumniUserIdsWithResponses);
                    }
                    if (!empty($alumniStudentIdsWithResponses)) {
                        $q->orWhereIn('id', $alumniStudentIdsWithResponses);
                    }
                    if (empty($alumniUserIdsWithResponses) && empty($alumniStudentIdsWithResponses)) {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->when(true, function($q) use ($angkatan) {
                    if ($angkatan) $q->where('angkatan', $angkatan);
                })
                ->count();
            $sudahMengisi[] = $filledCount;
            $belumMengisi[] = $prodi->students_count - $filledCount;
        }

        // 3. Trend response per bulan (Line chart) — last 12 months
        $monthlyData = \App\Models\FormResponse::select(
            DB::raw("DATE_FORMAT(form_responses.created_at, '%Y-%m') as month"),
            DB::raw('COUNT(*) as total')
        )
            ->when(true, $responseScope)
            ->where('form_responses.created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $monthLabels = [];
        $monthCounts = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i)->format('Y-m');
            $monthLabels[] = now()->subMonths($i)->format('M Y');
            $found = $monthlyData->firstWhere('month', $month);
            $monthCounts[] = $found ? $found->total : 0;
        }

        // 4. Dynamic Charts for Multiple Choice Questions (from Active Forms)
        $dynamicQuestions = \App\Models\FormQuestion::with(['form'])
            ->whereHas('form', function ($q) {
                $q->where('is_active', true);
            })
            ->whereIn('question_type', ['radio', 'select', 'checkbox', 'linear_scale', 'rating'])
            ->get();

        $dynamicCharts = [];
        foreach ($dynamicQuestions as $q) {
            $answersQuery = \App\Models\FormResponseAnswer::where('question_id', $q->id)
                ->whereNotNull('answer_text')
                ->where('answer_text', '!=', '')
                ->when(true, $answerScope);

            $answers = $answersQuery->get();
            $counts = [];
            foreach ($answers as $ans) {
                if (empty($ans->answer_text)) continue;
                if ($q->question_type === 'checkbox') {
                    $parts = explode(', ', $ans->answer_text);
                    foreach ($parts as $p) {
                        $p = trim($p);
                        if (!empty($p)) {
                            $counts[$p] = ($counts[$p] ?? 0) + 1;
                        }
                    }
                } else {
                    $val = trim($ans->answer_text);
                    $counts[$val] = ($counts[$val] ?? 0) + 1;
                }
            }

            if (count($counts) > 0) {
                $dynamicCharts[] = [
                    'id'            => 'chart_' . $q->id,
                    'question_text' => $q->question_text,
                    'form_title'    => $q->form->title,
                    'labels'        => array_keys($counts),
                    'data'          => array_values($counts),
                ];
            }
        }

        // 5. Student per angkatan
        $angkatanData = Student::when(true, $studentScope)
            ->select('angkatan', DB::raw('COUNT(*) as total'))
            ->groupBy('angkatan')
            ->orderBy('angkatan')
            ->get();

        // Helper closure: query answer counts for a question by keyword (with filter)
        $answerCounts = function (string $keyword) use ($answerScope) {
            return \App\Models\FormResponseAnswer::whereHas('question', function ($q) use ($keyword) {
                $q->whereRaw('LOWER(question_text) LIKE ?', ['%' . strtolower($keyword) . '%']);
            })
                ->when(true, $answerScope)
                ->whereNotNull('answer_text')
                ->where('answer_text', '!=', '')
                ->selectRaw('answer_text, COUNT(*) as total')
                ->groupBy('answer_text')
                ->orderByDesc('total')
                ->pluck('total', 'answer_text')
                ->toArray();
        };

        // 6. Waktu Tunggu
        $rawWaktuTunggu = \App\Models\FormResponseAnswer::whereHas('question', function ($q) {
            $q->whereRaw('LOWER(question_text) LIKE ?', ['%waktu tunggu%']);
        })
            ->when(true, $answerScope)
            ->whereNotNull('answer_text')
            ->where('answer_text', '!=', '')
            ->pluck('answer_text');

        $waktuTungguBuckets = [
            '0 bulan' => 0, '1-3 bulan' => 0, '4-6 bulan' => 0,
            '7-12 bulan' => 0, '> 12 bulan' => 0,
        ];
        foreach ($rawWaktuTunggu as $val) {
            $num = (int) filter_var($val, FILTER_SANITIZE_NUMBER_INT);
            if ($num <= 0)      $waktuTungguBuckets['0 bulan']++;
            elseif ($num <= 3)  $waktuTungguBuckets['1-3 bulan']++;
            elseif ($num <= 6)  $waktuTungguBuckets['4-6 bulan']++;
            elseif ($num <= 12) $waktuTungguBuckets['7-12 bulan']++;
            else                $waktuTungguBuckets['> 12 bulan']++;
        }
        $waktuTungguLabels = array_keys($waktuTungguBuckets);
        $waktuTungguData   = array_values($waktuTungguBuckets);

        // 7. Skala Tempat Kerja
        $skalaTempat = $answerCounts('skala tempat kerja');

        // 8. Distribusi Pendapatan
        $pendapatanData = $answerCounts('pendapatan');

        // 9. Kesesuaian Pekerjaan dengan Prodi
        $kesesuaianData = $answerCounts('sesuai dengan program studi');

        // 10. Data for filter dropdowns
        $univs          = Univ::orderBy('nama_univ')->get(['id', 'nama_univ']);
        $fakultasList   = $univId
            ? Fakultas::where('id_univs', $univId)->orderBy('nama_fakultas')->get(['id', 'nama_fakultas'])
            : collect();
        $prodiList      = $fakultasId
            ? Prodi::where('fakultas_id', $fakultasId)->orderBy('nama_prodi')->get(['id', 'nama_prodi'])
            : collect();

        // Active filter labels (for badge display)
        $activeUniv     = $univId     ? Univ::find($univId)     : null;
        $activeFakultas = $fakultasId ? Fakultas::find($fakultasId) : null;
        $activeProdi    = $prodiId    ? Prodi::find($prodiId)   : null;

        $formGroups = \App\Models\QuestionnaireForm::whereNotNull('form_group')
            ->distinct()
            ->pluck('form_group');

        $angkatans = \App\Models\Student::whereNotNull('angkatan')
            ->distinct()
            ->orderBy('angkatan', 'desc')
            ->pluck('angkatan');

        return view('reports', compact(
            'totalStudents',
            'totalResponses',
            'alumniResponseCount',
            'atasanResponseCount',
            'responseRate',
            'prodiLabels',
            'sudahMengisi',
            'belumMengisi',
            'monthLabels',
            'monthCounts',
            'dynamicCharts',
            'angkatanData',
            'waktuTungguLabels',
            'waktuTungguData',
            'skalaTempat',
            'pendapatanData',
            'kesesuaianData',
            // Filter data
            'univs',
            'fakultasList',
            'prodiList',
            'formGroups',
            'angkatans',
            'univId',
            'fakultasId',
            'prodiId',
            'formGroup',
            'angkatan',
            'activeUniv',
            'activeFakultas',
            'activeProdi',
        ));
    }

    /**
     * Return JSON options for cascade dropdowns (AJAX).
     */
    public function filterOptions(Request $request)
    {
        $type       = $request->input('type');
        $parentId   = $request->input('parent_id');

        if ($type === 'fakultas' && $parentId) {
            $items = Fakultas::where('id_univs', $parentId)
                ->orderBy('nama_fakultas')
                ->get(['id', 'nama_fakultas as label']);
            return response()->json($items);
        }

        if ($type === 'prodi' && $parentId) {
            $items = Prodi::where('fakultas_id', $parentId)
                ->orderBy('nama_prodi')
                ->get(['id', 'nama_prodi as label']);
            return response()->json($items);
        }

        return response()->json([]);
    }
}
