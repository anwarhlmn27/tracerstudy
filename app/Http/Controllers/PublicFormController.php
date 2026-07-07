<?php

namespace App\Http\Controllers;

use App\Models\QuestionnaireForm;
use App\Models\FormResponse;
use App\Models\FormResponseAnswer;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PublicFormController extends Controller
{
    /**
     * Show the public questionnaire form (no login required).
     */
    public function show(Request $request)
    {
        // Get the active alumni form (public forms target alumni role)
        // Check if a form slug was passed in query string (?form=slug)
        $formSlug = $request->query('form');

        $query = QuestionnaireForm::with(['questions.options'])
            ->where('is_active', true);
            
        if ($formSlug) {
            // Because we don't have a slug column, we fetch active forms and filter in collection
            $forms = $query->get();
            $activeForm = $forms->first(function ($f) use ($formSlug) {
                return \Illuminate\Support\Str::slug($f->title) === $formSlug;
            });
            
            if (!$activeForm) {
                abort(404, 'Form kuesioner tidak ditemukan atau tidak aktif.');
            }
        } else {
            $activeForm = $query->latest()->first();
        }

        // Check if this browser session already submitted
        $sessionKey = 'guest_submitted_form_' . ($activeForm->id ?? 'none');
        $hasFilledActiveForm = session()->has($sessionKey);

        // Also check by student_id if provided in session
        $guestStudentId = session('guest_student_id_' . ($activeForm->id ?? 'none'));
        if ($guestStudentId && $activeForm) {
            $hasFilledActiveForm = FormResponse::where('form_id', $activeForm->id)
                ->where('guest_student_id', $guestStudentId)
                ->exists();
        }

        return view('public-form', compact('activeForm', 'hasFilledActiveForm'));
    }

    /**
     * Store a guest form response.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'form_id'   => ['required', 'exists:questionnaire_forms,id'],
            'answers'   => ['nullable', 'array'],
        ]);

        $form = QuestionnaireForm::with('questions')->findOrFail($validated['form_id']);

        // Guard: reject if form has no questions
        if ($form->questions->isEmpty()) {
            return back()->withErrors(['form_id' => 'Form ini belum memiliki pertanyaan. Tidak dapat mengirim jawaban.']);
        }

        // Identify the student from the cascading dropdown answers
        $guestStudentId = null;
        $alumniNameAnswer = null;
        foreach ($form->questions as $q) {
            $lower = strtolower(trim($q->question_text));
            if (strpos($lower, 'nama') !== false || strpos($lower, 'alumni') !== false) {
                $alumniNameAnswer = $validated['answers'][$q->id] ?? null;
                break;
            }
        }

        // Try to find student by name
        if ($alumniNameAnswer) {
            $student = Student::where('nama_student', $alumniNameAnswer)->first();
            if ($student) {
                $guestStudentId = $student->id;
            }
        }

        // Check duplicate by student if found, or by session
        if ($guestStudentId) {
            $alreadySubmitted = FormResponse::where('form_id', $form->id)
                ->where('guest_student_id', $guestStudentId)
                ->exists();
            if ($alreadySubmitted) {
                return back()->withErrors(['form_id' => 'Alumni ini sudah pernah mengisi kuesioner ini.']);
            }
        }

        // Validate required questions
        foreach ($form->questions as $question) {
            if ($question->is_required) {
                if ($question->question_type === 'file') {
                    if (!$request->hasFile("answers.{$question->id}")) {
                        return back()->withErrors(['answers.' . $question->id => 'Pertanyaan "' . $question->question_text . '" wajib mengunggah file.'])->withInput();
                    }
                } else {
                    $answer = $validated['answers'][$question->id] ?? null;
                    // Skip required check for cascading select questions (they identify the user)
                    $lower = strtolower(trim($question->question_text));
                    $isCascadingIdentity = (strpos($lower, 'universitas') !== false
                        || strpos($lower, 'fakultas') !== false
                        || strpos($lower, 'program studi') !== false
                        || strpos($lower, 'prodi') !== false);
                    if (!$isCascadingIdentity && empty($answer) && $answer !== '0') {
                        return back()->withErrors(['answers.' . $question->id => 'Pertanyaan "' . $question->question_text . '" wajib diisi.'])->withInput();
                    }
                }
            }
        }

        // Create guest form response (no user_id)
        $response = FormResponse::create([
            'id'                  => Str::uuid(),
            'form_id'             => $form->id,
            'user_id'             => null,
            'evaluated_student_id' => null,
            'guest_student_id'    => $guestStudentId,
        ]);

        // Save answers
        foreach ($form->questions as $question) {
            $answerValue = null;

            if ($question->question_type === 'file') {
                if ($request->hasFile("answers.{$question->id}")) {
                    $file = $request->file("answers.{$question->id}");
                    $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                    $destinationPath = public_path('uploads/questions');
                    if (!file_exists($destinationPath)) {
                        mkdir($destinationPath, 0755, true);
                    }
                    $file->move($destinationPath, $fileName);
                    $answerValue = 'uploads/questions/' . $fileName;
                }
            } else {
                $answerValue = $validated['answers'][$question->id] ?? null;
                if ($question->question_type === 'checkbox' && is_array($answerValue)) {
                    $answerValue = implode(', ', $answerValue);
                }
            }

            FormResponseAnswer::create([
                'id'          => Str::uuid(),
                'response_id' => $response->id,
                'question_id' => $question->id,
                'answer_text' => $answerValue,
            ]);
        }

        // Mark session as filled
        $sessionKey = 'guest_submitted_form_' . $form->id;
        session([$sessionKey => true]);
        if ($guestStudentId) {
            session(['guest_student_id_' . $form->id => $guestStudentId]);
        }

        return redirect()->route('public.form')->with('success', 'Terima kasih! Jawaban Anda telah berhasil disimpan.');
    }
}
