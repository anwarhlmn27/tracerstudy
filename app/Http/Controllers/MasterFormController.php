<?php

namespace App\Http\Controllers;

use App\Models\QuestionnaireForm;
use App\Models\FormQuestion;
use App\Models\FormQuestionOption;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class MasterFormController extends Controller
{
    /**
     * Display a listing of all questionnaire forms.
     */
    public function index()
    {
        $forms = QuestionnaireForm::withCount(['questions', 'responses'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('master-form.index', compact('forms'));
    }

    /**
     * Show the form for creating a new questionnaire form.
     */
    public function create()
    {
        return view('master-form.create');
    }

    /**
     * Store a newly created questionnaire form in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'target_role' => ['required', Rule::in(['alumni', 'atasan'])],
            'angkatan' => ['nullable', 'string', 'max:50'],
            'form_group' => ['nullable', 'string', 'max:100'],
            'questions' => ['required', 'array', 'min:1'],
            'questions.*.text' => ['required', 'string'],
            'questions.*.description' => ['nullable', 'string', 'max:1000'],
            'questions.*.type' => ['required', Rule::in(['text', 'number', 'textarea', 'radio', 'select', 'select_db', 'checkbox', 'file', 'linear_scale', 'rating', 'date', 'time'])],
            'questions.*.db_source' => ['nullable', Rule::in(['universitas', 'fakultas', 'prodi', 'mahasiswa'])],
            'questions.*.required' => ['sometimes', 'boolean'],
            'questions.*.section_id' => ['nullable', 'integer', 'min:1'],
            'questions.*.section_title' => ['nullable', 'string', 'max:255'],
            'questions.*.options' => ['nullable', 'array'],
            'questions.*.options.*' => ['nullable', 'string'],
            'questions.*.go_to_sections' => ['nullable', 'array'],
            'questions.*.go_to_sections.*' => ['nullable', 'integer'],
            'questions.*.has_others' => ['sometimes', 'boolean'],
        ]);

        $form = QuestionnaireForm::create([
            'id' => Str::uuid(),
            'title' => $validated['title'],
            'target_role' => $validated['target_role'],
            'angkatan' => $validated['angkatan'] ?? null,
            'form_group' => $validated['form_group'] ?? null,
            'is_active' => true,
        ]);

        $sortOrder = 0;
        if ($validated['target_role'] === 'alumni') {
            $identityQuestions = [
                ['text' => 'Pilih Universitas', 'type' => 'select', 'required' => true],
                ['text' => 'Pilih Fakultas', 'type' => 'select', 'required' => true],
                ['text' => 'Pilih Program Studi', 'type' => 'select', 'required' => true],
                ['text' => 'Pilih Nama Alumni', 'type' => 'select', 'required' => true],
            ];
            foreach ($identityQuestions as $iq) {
                FormQuestion::create([
                    'id' => (string) Str::uuid(),
                    'form_id' => $form->id,
                    'question_text' => $iq['text'],
                    'question_type' => $iq['type'],
                    'is_required' => $iq['required'],
                    'sort_order' => $sortOrder++,
                    'section_id' => 1,
                    'section_title' => null,
                ]);
            }
        } elseif ($validated['target_role'] === 'atasan') {
            $identityQuestions = [
                ['text' => 'Pilih Program Studi', 'type' => 'select', 'required' => true],
                ['text' => 'Pilih Nama Alumni', 'type' => 'select', 'required' => true],
            ];
            foreach ($identityQuestions as $iq) {
                FormQuestion::create([
                    'id' => (string) Str::uuid(),
                    'form_id' => $form->id,
                    'question_text' => $iq['text'],
                    'question_type' => $iq['type'],
                    'is_required' => $iq['required'],
                    'sort_order' => $sortOrder++,
                    'section_id' => 1,
                    'section_title' => null,
                ]);
            }
        }

        foreach ($validated['questions'] as $index => $questionData) {
            // Prevent duplicate identity questions if sent from UI
            $lowerText = strtolower(trim($questionData['text']));
            if ($validated['target_role'] === 'alumni' && (
                strpos($lowerText, 'universitas') !== false ||
                strpos($lowerText, 'fakultas') !== false ||
                strpos($lowerText, 'program studi') !== false ||
                strpos($lowerText, 'prodi') !== false ||
                (strpos($lowerText, 'nama') !== false && (strpos($lowerText, 'alumni') !== false || strpos($lowerText, 'mahasiswa') !== false))
            )) {
                continue;
            }

            if ($validated['target_role'] === 'atasan' && (
                strpos($lowerText, 'program studi') !== false ||
                strpos($lowerText, 'prodi') !== false ||
                (strpos($lowerText, 'nama') !== false && (strpos($lowerText, 'alumni') !== false || strpos($lowerText, 'mahasiswa') !== false))
            )) {
                continue;
            }

            $question = FormQuestion::create([
                'id' => (string) Str::uuid(),
                'form_id' => $form->id,
                'question_text' => $questionData['text'],
                'question_description' => $questionData['description'] ?? null,
                'question_type' => $questionData['type'],
                'is_required' => $questionData['required'] ?? true,
                'sort_order' => $sortOrder++,
                'section_id' => $questionData['section_id'] ?? 1,
                'section_title' => $questionData['section_title'] ?? null,
                'has_others' => $questionData['has_others'] ?? false,
            ]);

            // Create options for radio/select/checkbox/linear_scale/rating
            if (in_array($questionData['type'], ['radio', 'select', 'checkbox', 'linear_scale', 'rating']) && !empty($questionData['options'])) {
                $goToSections = $questionData['go_to_sections'] ?? [];
                foreach ($questionData['options'] as $optIndex => $optionText) {
                    if (isset($optionText) && $optionText !== '') {
                        $goToVal = isset($goToSections[$optIndex]) && $goToSections[$optIndex] !== '' ? (int)$goToSections[$optIndex] : null;
                        FormQuestionOption::create([
                            'id' => (string) Str::uuid(),
                            'question_id' => $question->id,
                            'option_text' => trim($optionText),
                            'sort_order' => $optIndex,
                            'go_to_section' => $goToVal,
                        ]);
                    }
                }
            }
        }

        return redirect()->route('master-form.index')->with('success', 'Form kuesioner berhasil dibuat!');
    }

    /**
     * Show the form for editing the specified questionnaire form.
     */
    public function edit(string $id)
    {
        $form = QuestionnaireForm::with(['questions.options'])->findOrFail($id);

        return view('master-form.edit', compact('form'));
    }

    /**
     * Update the specified questionnaire form in storage.
     */
    public function update(Request $request, string $id)
    {
        $form = QuestionnaireForm::findOrFail($id);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'target_role' => ['required', Rule::in(['alumni', 'atasan'])],
            'angkatan' => ['nullable', 'string', 'max:50'],
            'form_group' => ['nullable', 'string', 'max:100'],
            'questions' => ['required', 'array', 'min:1'],
            'questions.*.text' => ['required', 'string'],
            'questions.*.description' => ['nullable', 'string', 'max:1000'],
            'questions.*.type' => ['required', Rule::in(['text', 'number', 'textarea', 'radio', 'select', 'select_db', 'checkbox', 'file', 'linear_scale', 'rating', 'date', 'time'])],
            'questions.*.db_source' => ['nullable', Rule::in(['universitas', 'fakultas', 'prodi', 'mahasiswa'])],
            'questions.*.required' => ['sometimes', 'boolean'],
            'questions.*.section_id' => ['nullable', 'integer', 'min:1'],
            'questions.*.section_title' => ['nullable', 'string', 'max:255'],
            'questions.*.options' => ['nullable', 'array'],
            'questions.*.options.*' => ['nullable', 'string'],
            'questions.*.go_to_sections' => ['nullable', 'array'],
            'questions.*.go_to_sections.*' => ['nullable', 'integer'],
            'questions.*.has_others' => ['sometimes', 'boolean'],
        ]);

        $form->update([
            'title' => $validated['title'],
            'target_role' => $validated['target_role'],
            'angkatan' => $validated['angkatan'] ?? null,
            'form_group' => $validated['form_group'] ?? null,
        ]);

        // Delete old questions (cascade deletes options too)
        $form->questions()->delete();

        $sortOrder = 0;
        if ($validated['target_role'] === 'alumni') {
            $identityQuestions = [
                ['text' => 'Pilih Universitas', 'type' => 'select', 'required' => true],
                ['text' => 'Pilih Fakultas', 'type' => 'select', 'required' => true],
                ['text' => 'Pilih Program Studi', 'type' => 'select', 'required' => true],
                ['text' => 'Pilih Nama Alumni', 'type' => 'select', 'required' => true],
            ];
            foreach ($identityQuestions as $iq) {
                FormQuestion::create([
                    'id' => (string) Str::uuid(),
                    'form_id' => $form->id,
                    'question_text' => $iq['text'],
                    'question_type' => $iq['type'],
                    'is_required' => $iq['required'],
                    'sort_order' => $sortOrder++,
                    'section_id' => 1,
                    'section_title' => null,
                ]);
            }
        } elseif ($validated['target_role'] === 'atasan') {
            $identityQuestions = [
                ['text' => 'Pilih Program Studi', 'type' => 'select', 'required' => true],
                ['text' => 'Pilih Nama Alumni', 'type' => 'select', 'required' => true],
            ];
            foreach ($identityQuestions as $iq) {
                FormQuestion::create([
                    'id' => (string) Str::uuid(),
                    'form_id' => $form->id,
                    'question_text' => $iq['text'],
                    'question_type' => $iq['type'],
                    'is_required' => $iq['required'],
                    'sort_order' => $sortOrder++,
                    'section_id' => 1,
                    'section_title' => null,
                ]);
            }
        }

        // Re-create questions
        foreach ($validated['questions'] as $index => $questionData) {
            // Prevent duplicate identity questions if sent from UI
            $lowerText = strtolower(trim($questionData['text']));
            if ($validated['target_role'] === 'alumni' && (
                strpos($lowerText, 'universitas') !== false ||
                strpos($lowerText, 'fakultas') !== false ||
                strpos($lowerText, 'program studi') !== false ||
                strpos($lowerText, 'prodi') !== false ||
                (strpos($lowerText, 'nama') !== false && (strpos($lowerText, 'alumni') !== false || strpos($lowerText, 'mahasiswa') !== false))
            )) {
                continue;
            }

            if ($validated['target_role'] === 'atasan' && (
                strpos($lowerText, 'program studi') !== false ||
                strpos($lowerText, 'prodi') !== false ||
                (strpos($lowerText, 'nama') !== false && (strpos($lowerText, 'alumni') !== false || strpos($lowerText, 'mahasiswa') !== false))
            )) {
                continue;
            }

            $question = FormQuestion::create([
                'id' => (string) Str::uuid(),
                'form_id' => $form->id,
                'question_text' => $questionData['text'],
                'question_description' => $questionData['description'] ?? null,
                'question_type' => $questionData['type'],
                'is_required' => $questionData['required'] ?? true,
                'sort_order' => $sortOrder++,
                'section_id' => $questionData['section_id'] ?? 1,
                'section_title' => $questionData['section_title'] ?? null,
                'has_others' => $questionData['has_others'] ?? false,
            ]);

            if (in_array($questionData['type'], ['radio', 'select', 'checkbox', 'linear_scale', 'rating']) && !empty($questionData['options'])) {
                $goToSections = $questionData['go_to_sections'] ?? [];
                foreach ($questionData['options'] as $optIndex => $optionText) {
                    if (isset($optionText) && $optionText !== '') {
                        $goToVal = isset($goToSections[$optIndex]) && $goToSections[$optIndex] !== '' ? (int)$goToSections[$optIndex] : null;
                        FormQuestionOption::create([
                            'id' => (string) Str::uuid(),
                            'question_id' => $question->id,
                            'option_text' => trim($optionText),
                            'sort_order' => $optIndex,
                            'go_to_section' => $goToVal,
                        ]);
                    }
                }
            }
        }

        return redirect()->route('master-form.index')->with('success', 'Form kuesioner berhasil diperbarui!');
    }

    /**
     * Remove the specified questionnaire form from storage.
     */
    public function destroy(string $id)
    {
        $form = QuestionnaireForm::findOrFail($id);
        $form->delete();

        return back()->with('success', 'Form kuesioner berhasil dihapus!');
    }

    /**
     * Toggle active status of a form.
     */
    public function toggleActive(string $id)
    {
        $form = QuestionnaireForm::findOrFail($id);
        $form->update(['is_active' => !$form->is_active]);

        $status = $form->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Form \"{$form->title}\" berhasil {$status}!");
    }
}
