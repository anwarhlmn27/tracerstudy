<?php

namespace App\Http\Controllers;

use App\Models\QuestionnaireForm;
use App\Models\FormResponse;
use App\Models\FormResponseAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TracerResponseController extends Controller
{
    /**
     * Show the form for creating a new tracer response.
     */
    public function create(Request $request)
    {
        $user = $request->user();
        $role = $user->role;

        $student = $user->student;
        $evaluatedStudentId = null;
        $angkatan = null;

        if ($role === 'atasan') {
            $evaluatedStudentId = $request->query('student_id');
            if (!$evaluatedStudentId) {
                $needsToSelectStudent = true;
                return view('form', compact('user', 'needsToSelectStudent'));
            }
            $studentToEvaluate = \App\Models\Student::findOrFail($evaluatedStudentId);
            $angkatan = $studentToEvaluate->angkatan;
        } else {
            $angkatan = $student ? $student->angkatan : null;
        }

        // Get active form for this user's role and angkatan
        $activeFormQuery = QuestionnaireForm::with(['questions.options'])
            ->where('target_role', $role)
            ->where('is_active', true);

        if ($angkatan) {
            $activeFormQuery->where(function ($q) use ($angkatan) {
                $q->whereNull('angkatan')
                  ->orWhere('angkatan', '')
                  ->orWhere('angkatan', $angkatan);
            });
        }

        $activeForm = $activeFormQuery->latest()->first();

        // Get previous responses for this user
        $previousResponses = FormResponse::with(['form', 'answers.question'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Check if user has already filled the active form
        $hasFilledActiveForm = false;
        if ($activeForm) {
            if ($role === 'alumni' && $student) {
                $formIds = [];
                if ($activeForm->form_group) {
                    $formIds = QuestionnaireForm::where('form_group', $activeForm->form_group)
                        ->pluck('id')
                        ->toArray();
                } else {
                    $formIds = [$activeForm->id];
                }

                $hasFilledActiveForm = FormResponse::whereIn('form_id', $formIds)
                    ->where(function($q) use ($user, $student) {
                        $q->where('user_id', $user->id)
                          ->orWhere('guest_student_id', $student->id);
                    })
                    ->exists();
            } else {
                $query = FormResponse::where('form_id', $activeForm->id)->where('user_id', $user->id);
                if ($role === 'atasan') {
                    $query->where('evaluated_student_id', $evaluatedStudentId);
                }
                $hasFilledActiveForm = $query->exists();
            }
        }

        $needsToSelectStudent = false;

        return view('form', compact('activeForm', 'hasFilledActiveForm', 'previousResponses', 'student', 'user', 'needsToSelectStudent', 'evaluatedStudentId'));
    }

    /**
     * Store a newly created response in storage.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'form_id' => ['required', 'exists:questionnaire_forms,id'],
            'answers' => ['nullable', 'array'],
        ]);

        $form = QuestionnaireForm::with('questions')->findOrFail($validated['form_id']);

        // Check if already submitted
        $student = $user->student;
        if ($user->role === 'alumni' && $student) {
            $formIds = [];
            if ($form->form_group) {
                $formIds = QuestionnaireForm::where('form_group', $form->form_group)
                    ->pluck('id')
                    ->toArray();
            } else {
                $formIds = [$form->id];
            }

            $alreadySubmitted = FormResponse::whereIn('form_id', $formIds)
                ->where(function($q) use ($user, $student) {
                    $q->where('user_id', $user->id)
                      ->orWhere('guest_student_id', $student->id);
                })
                ->exists();
        } else {
            $query = FormResponse::where('form_id', $form->id)->where('user_id', $user->id);
            if ($user->role === 'atasan') {
                $query->where('evaluated_student_id', $request->input('evaluated_student_id'));
            }
            $alreadySubmitted = $query->exists();
        }

        if ($alreadySubmitted) {
            return back()->withErrors(['form_id' => 'Anda sudah mengisi kuesioner dalam kategori ini sebelumnya.']);
        }

        // Validate required questions have answers
        foreach ($form->questions as $question) {
            if ($question->is_required) {
                if ($question->question_type === 'file') {
                    if (!$request->hasFile("answers.{$question->id}")) {
                        return back()->withErrors(['answers.' . $question->id => 'Pertanyaan "' . $question->question_text . '" wajib mengunggah file.'])->withInput();
                    }
                } else {
                    $answer = $validated['answers'][$question->id] ?? null;
                    if ($question->question_type === 'radio' && $answer === 'Others') {
                        $answer = $request->input("answers_others.{$question->id}");
                    } elseif ($question->question_type === 'checkbox' && is_array($answer)) {
                        if (in_array('Others', $answer)) {
                            $otherVal = $request->input("answers_others.{$question->id}");
                            if (!empty($otherVal)) {
                                $idx = array_search('Others', $answer);
                                $answer[$idx] = $otherVal;
                            } else {
                                $answer = array_filter($answer, function($val) { return $val !== 'Others'; });
                            }
                        }
                    }
                    if (empty($answer) && $answer !== '0') {
                        return back()->withErrors(['answers.' . $question->id => 'Pertanyaan "' . $question->question_text . '" wajib diisi.'])->withInput();
                    }
                }
            }
        }

        // Create form response
        $response = FormResponse::create([
            'id' => Str::uuid(),
            'form_id' => $form->id,
            'user_id' => $user->id,
            'evaluated_student_id' => $user->role === 'atasan' ? $request->input('evaluated_student_id') : null,
        ]);

        // Save answers
        foreach ($form->questions as $question) {
            $answerValue = null;

            if ($question->question_type === 'file') {
                if ($request->hasFile("answers.{$question->id}")) {
                    $file = $request->file("answers.{$question->id}");
                    $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                    
                    // Create directory if not exists
                    $destinationPath = public_path('uploads/questions');
                    if (!file_exists($destinationPath)) {
                        mkdir($destinationPath, 0755, true);
                    }
                    
                    $file->move($destinationPath, $fileName);
                    $answerValue = 'uploads/questions/' . $fileName;
                }
            } else {
                $answerValue = $validated['answers'][$question->id] ?? null;
                if ($question->question_type === 'radio' && $answerValue === 'Others') {
                    $answerValue = $request->input("answers_others.{$question->id}") ?: 'Lainnya';
                }

                // For checkbox, join multiple values
                if ($question->question_type === 'checkbox' && is_array($answerValue)) {
                    if (in_array('Others', $answerValue)) {
                        $otherVal = $request->input("answers_others.{$question->id}");
                        if (!empty($otherVal)) {
                            $idx = array_search('Others', $answerValue);
                            $answerValue[$idx] = $otherVal;
                        } else {
                            $answerValue = array_filter($answerValue, function($val) { return $val !== 'Others'; });
                        }
                    }
                    $answerValue = implode(', ', $answerValue);
                }
            }

            FormResponseAnswer::create([
                'id' => Str::uuid(),
                'response_id' => $response->id,
                'question_id' => $question->id,
                'answer_text' => $answerValue,
            ]);
        }

        return redirect()->route('form.create')->with('success', 'Terima kasih telah berpartisipasi mengisi kuesioner ini!');
    }

    /**
     * Search alumni for atasan form selection.
     */
    public function searchAlumni(Request $request)
    {
        $search = $request->query('q');
        if (strlen($search) < 3) {
            return response()->json(['results' => []]);
        }

        $students = \App\Models\Student::where('nama_student', 'like', "%{$search}%")
            ->whereNotIn('id', function($q) {
                $q->select('evaluated_student_id')
                  ->from('form_responses')
                  ->whereNotNull('evaluated_student_id');
            })
            ->select('id', 'nama_student', 'nim', 'angkatan')
            ->take(20)
            ->get();

        $results = $students->map(function($student) {
            return [
                'id' => $student->id,
                'text' => $student->nama_student . ' (' . $student->nim . ') - ' . ($student->angkatan ?? 'Tidak ada angkatan')
            ];
        });

        return response()->json(['results' => $results]);
    }
}
