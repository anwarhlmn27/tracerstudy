<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class QuestionnaireController extends Controller
{
    /**
     * Display a listing of all questionnaire responses.
     */
    public function index(Request $request)
    {
        $selectedAngkatan = $request->query('angkatan');
        
        $query = \App\Models\FormResponse::with(['user.student.prodi', 'form', 'answers.question', 'evaluatedStudent']);
        
        if ($selectedAngkatan) {
            $query->where(function($q) use ($selectedAngkatan) {
                $q->whereHas('user.student', function($sq) use ($selectedAngkatan) {
                    $sq->where('angkatan', $selectedAngkatan);
                })->orWhereHas('evaluatedStudent', function($sq) use ($selectedAngkatan) {
                    $sq->where('angkatan', $selectedAngkatan);
                });
            });
        }
        
        $responses = $query->orderBy('created_at', 'desc')->get();
        
        $dbYears = \App\Models\Student::whereNotNull('angkatan')
            ->where('angkatan', '!=', '')
            ->pluck('angkatan')
            ->toArray();
            
        $formYears = \App\Models\QuestionnaireForm::whereNotNull('angkatan')
            ->where('angkatan', '!=', '')
            ->pluck('angkatan')
            ->toArray();
            
        $staticYears = range(date('Y') + 1, 2015);
        
        $angkatanList = collect(array_merge($staticYears, $dbYears, $formYears))
            ->unique()
            ->sortDesc()
            ->values();

        $totalAlumniQuery = \App\Models\User::where('role', 'alumni');
        if ($selectedAngkatan) {
            $totalAlumniQuery->whereHas('student', function($sq) use ($selectedAngkatan) {
                $sq->where('angkatan', $selectedAngkatan);
            });
        }
        $totalAlumni = $totalAlumniQuery->count();

        $alumniFilledQuery = \App\Models\FormResponse::whereHas('form', function($q) {
            $q->where('target_role', 'alumni');
        });
        if ($selectedAngkatan) {
            $alumniFilledQuery->whereHas('user.student', function($sq) use ($selectedAngkatan) {
                $sq->where('angkatan', $selectedAngkatan);
            });
        }
        $alumniFilled = $alumniFilledQuery->distinct('user_id')->count('user_id');

        $totalAtasan = \App\Models\User::where('role', 'atasan')->count();
        
        $atasanFilledQuery = \App\Models\FormResponse::whereHas('form', function($q) {
            $q->where('target_role', 'atasan');
        });
        if ($selectedAngkatan) {
            $atasanFilledQuery->whereHas('evaluatedStudent', function($sq) use ($selectedAngkatan) {
                $sq->where('angkatan', $selectedAngkatan);
            });
        }
        $atasanFilled = $atasanFilledQuery->distinct('user_id')->count('user_id');

        return view('questionnaires', compact('responses', 'totalAlumni', 'alumniFilled', 'totalAtasan', 'atasanFilled', 'angkatanList', 'selectedAngkatan'));
    }

    /**
     * Export questionnaire responses to CSV (Excel-compatible).
     */
    public function export(Request $request): StreamedResponse
    {
        $selectedAngkatan = $request->query('angkatan');
        
        $query = \App\Models\FormResponse::with(['user.student.prodi', 'form', 'answers.question', 'evaluatedStudent']);
        
        if ($selectedAngkatan) {
            $query->where(function($q) use ($selectedAngkatan) {
                $q->whereHas('user.student', function($sq) use ($selectedAngkatan) {
                    $sq->where('angkatan', $selectedAngkatan);
                })->orWhereHas('evaluatedStudent', function($sq) use ($selectedAngkatan) {
                    $sq->where('angkatan', $selectedAngkatan);
                });
            });
        }
        
        $responses = $query->orderBy('created_at', 'desc')->get();

        $filename = 'tracer_responses_' . date('Y-m-d_His') . '.xlsx';

        // 1. Collect all unique questions across these responses to form columns
        $questionTexts = [];
        foreach ($responses as $response) {
            foreach ($response->answers as $answer) {
                if ($answer->question) {
                    $questionTexts[$answer->question->id] = $answer->question->question_text;
                }
            }
        }

        // 2. Build headers
        $headers = ['No', 'Timestamp', 'Role Target', 'Nama Responden', 'Judul Form'];
        foreach ($questionTexts as $qId => $qText) {
            $headers[] = $qText;
        }

        $data = [$headers];
        $rowNo = 1;

        // 3. Build data rows (one row per response)
        foreach ($responses as $response) {
            $roleTarget = $response->form->target_role ?? '-';
            $namaResponden = $response->user->name ?? '-';
            if ($roleTarget === 'alumni' && $response->user->student) {
                $namaResponden = $response->user->student->nama_student;
            }
            $judulForm = $response->form->title ?? '-';
            $timestamp = $response->created_at->format('Y-m-d H:i:s');

            // Map answers by question ID
            $answersMap = [];
            foreach ($response->answers as $answer) {
                if ($answer->question) {
                    $answersMap[$answer->question->id] = $answer->answer_text ?? '-';
                }
            }

            // Build row
            $row = [
                $rowNo++,
                $timestamp,
                $roleTarget,
                $namaResponden,
                $judulForm,
            ];

            // Fill question columns
            foreach ($questionTexts as $qId => $qText) {
                $row[] = $answersMap[$qId] ?? '-';
            }

            $data[] = $row;
        }

        return response()->streamDownload(function () use ($data) {
            $xlsx = \Shuchkin\SimpleXLSXGen::fromArray($data);
            $xlsx->saveAs('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
