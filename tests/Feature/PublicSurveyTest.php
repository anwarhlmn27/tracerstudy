<?php

namespace Tests\Feature;

use App\Models\Prodi;
use App\Models\Student;
use App\Models\User;
use App\Models\QuestionnaireForm;
use App\Models\FormQuestion;
use App\Models\FormResponse;
use App\Models\FormResponseAnswer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class PublicSurveyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed basic data needed for cascading selects
        $univ = \App\Models\Univ::create([
            'kode_univ' => 'UI',
            'nama_univ' => 'Universitas Indonesia',
            'address' => 'Depok',
            'email' => 'info@ui.ac.id',
            'website' => 'https://ui.ac.id'
        ]);
        $fakultas = \App\Models\Fakultas::create([
            'id_univs' => $univ->id,
            'kode_fakultas' => 'Fasilkom',
            'nama_fakultas' => 'Fakultas Ilmu Komputer',
            'short_name' => 'Fasilkom'
        ]);
        $this->prodi = Prodi::create([
            'fakultas_id' => $fakultas->id,
            'kode_prodi' => 'IF',
            'nama_prodi' => 'Informatika',
            'short_name' => 'IF'
        ]);
        $user = User::factory()->create(['role' => 'alumni']);
        $this->student = Student::create([
            'user_id' => $user->id,
            'prodi_id' => $this->prodi->id,
            'nim' => '120220001',
            'nama_student' => 'Budi Santoso',
            'angkatan' => 2022,
            'status' => 'lulus',
            'status_alumni' => 'Bekerja'
        ]);
    }

    public function test_exit_survey_renders_all_four_identity_fields(): void
    {
        $form = QuestionnaireForm::create([
            'id' => Str::uuid(),
            'title' => 'Exit Survey (After Yudisium)',
            'target_role' => 'alumni',
            'is_active' => true
        ]);

        // Create identity fields for Exit Survey
        $fields = ['Pilih Universitas', 'Pilih Fakultas', 'Pilih Program Studi', 'Pilih Nama Alumni'];
        foreach ($fields as $index => $field) {
            FormQuestion::create([
                'id' => Str::uuid(),
                'form_id' => $form->id,
                'question_text' => $field,
                'question_type' => 'select',
                'is_required' => true,
                'sort_order' => $index + 1
            ]);
        }

        $response = $this->get('/kuesioner?form=exit-survey-after-yudisium');

        $response->assertStatus(200);
        $response->assertSee('id="question-univ"', false);
        $response->assertSee('id="question-fakultas"', false);
        $response->assertSee('id="question-prodi"', false);
        $response->assertSee('id="question-alumni"', false);
    }

    public function test_user_survey_renders_only_prodi_and_student_identity_fields(): void
    {
        $form = QuestionnaireForm::create([
            'id' => Str::uuid(),
            'title' => 'User Survey (After Tracer Study)',
            'target_role' => 'atasan',
            'is_active' => true
        ]);

        // Create identity fields for User Survey
        $fields = ['Pilih Program Studi', 'Pilih Nama Alumni'];
        foreach ($fields as $index => $field) {
            FormQuestion::create([
                'id' => Str::uuid(),
                'form_id' => $form->id,
                'question_text' => $field,
                'question_type' => 'select',
                'is_required' => true,
                'sort_order' => $index + 1
            ]);
        }

        $response = $this->get('/kuesioner?form=user-survey-after-tracer-study');

        $response->assertStatus(200);
        $response->assertDontSee('id="question-univ"', false);
        $response->assertDontSee('id="question-fakultas"', false);
        $response->assertSee('id="question-prodi"', false);
        $response->assertSee('id="question-alumni"', false);
    }

    public function test_can_submit_public_user_survey_response(): void
    {
        $form = QuestionnaireForm::create([
            'id' => Str::uuid(),
            'title' => 'User Survey (After Tracer Study)',
            'target_role' => 'atasan',
            'is_active' => true
        ]);

        $qProdi = FormQuestion::create([
            'id' => Str::uuid(),
            'form_id' => $form->id,
            'question_text' => 'Pilih Program Studi',
            'question_type' => 'select',
            'is_required' => true,
            'sort_order' => 1
        ]);

        $qAlumni = FormQuestion::create([
            'id' => Str::uuid(),
            'form_id' => $form->id,
            'question_text' => 'Pilih Nama Alumni',
            'question_type' => 'select',
            'is_required' => true,
            'sort_order' => 2
        ]);

        $qRating = FormQuestion::create([
            'id' => Str::uuid(),
            'form_id' => $form->id,
            'question_text' => 'Bagaimana etika alumni?',
            'question_type' => 'radio',
            'is_required' => true,
            'sort_order' => 3
        ]);

        $response = $this->post('/kuesioner', [
            'form_id' => $form->id,
            'answers' => [
                $qProdi->id => 'Informatika',
                $qAlumni->id => 'Budi Santoso',
                $qRating->id => 'Sangat Baik'
            ]
        ]);

        $response->assertRedirect(route('public.form'));
        $response->assertSessionHas('success', 'Terima kasih! Jawaban Anda telah berhasil disimpan.');

        $this->assertDatabaseHas('form_responses', [
            'form_id' => $form->id,
            'guest_student_id' => $this->student->id,
        ]);
    }

    public function test_cannot_submit_duplicate_form_in_same_form_group(): void
    {
        $form1 = QuestionnaireForm::create([
            'id' => Str::uuid(),
            'title' => 'Tracer Study Form A',
            'target_role' => 'alumni',
            'form_group' => 'Tracer Study',
            'is_active' => true
        ]);

        $form2 = QuestionnaireForm::create([
            'id' => Str::uuid(),
            'title' => 'Tracer Study Form B',
            'target_role' => 'alumni',
            'form_group' => 'Tracer Study',
            'is_active' => true
        ]);

        $qAlumni = FormQuestion::create([
            'id' => Str::uuid(),
            'form_id' => $form1->id,
            'question_text' => 'Pilih Nama Alumni',
            'question_type' => 'select',
            'is_required' => true,
            'sort_order' => 1
        ]);

        $qAlumni2 = FormQuestion::create([
            'id' => Str::uuid(),
            'form_id' => $form2->id,
            'question_text' => 'Pilih Nama Alumni',
            'question_type' => 'select',
            'is_required' => true,
            'sort_order' => 1
        ]);

        // Submit form 1 first
        $response1 = $this->post('/kuesioner', [
            'form_id' => $form1->id,
            'answers' => [
                $qAlumni->id => 'Budi Santoso'
            ]
        ]);
        $response1->assertRedirect(route('public.form'));

        // Now try to submit form 2 which is in the same form_group 'Tracer Study'
        $response2 = $this->post('/kuesioner', [
            'form_id' => $form2->id,
            'answers' => [
                $qAlumni2->id => 'Budi Santoso'
            ]
        ]);
        
        $response2->assertSessionHasErrors(['form_id']);
        $this->assertEquals(1, FormResponse::count());
    }

    public function test_student_api_returns_has_submitted_flag(): void
    {
        $form = QuestionnaireForm::create([
            'id' => Str::uuid(),
            'title' => 'Tracer Study Form A',
            'target_role' => 'alumni',
            'form_group' => 'Tracer Study',
            'is_active' => true
        ]);

        // Submit once
        FormResponse::create([
            'id' => Str::uuid(),
            'form_id' => $form->id,
            'guest_student_id' => $this->student->id
        ]);

        // Call the API
        $response = $this->getJson("/api/students?prodi_id={$this->prodi->id}&form_id={$form->id}");

        $response->assertJsonFragment([
            'id' => $this->student->id,
            'nama_student' => 'Budi Santoso',
            'has_submitted' => true
        ]);
    }

    public function test_can_submit_public_survey_with_others_option(): void
    {
        $form = QuestionnaireForm::create([
            'id' => Str::uuid(),
            'title' => 'Exit Survey (After Yudisium)',
            'target_role' => 'alumni',
            'is_active' => true
        ]);

        $qAlumni = FormQuestion::create([
            'id' => Str::uuid(),
            'form_id' => $form->id,
            'question_text' => 'Pilih Nama Alumni',
            'question_type' => 'select',
            'is_required' => true,
            'sort_order' => 1
        ]);

        $qRadio = FormQuestion::create([
            'id' => Str::uuid(),
            'form_id' => $form->id,
            'question_text' => 'Mengapa memilih kampus ini?',
            'question_type' => 'radio',
            'is_required' => true,
            'has_others' => true,
            'sort_order' => 2
        ]);

        // Submit with Others selected
        $response = $this->post('/kuesioner', [
            'form_id' => $form->id,
            'answers' => [
                $qAlumni->id => 'Budi Santoso',
                $qRadio->id => 'Others'
            ],
            'answers_others' => [
                $qRadio->id => 'Hobi Saya Sendiri'
            ]
        ]);

        $response->assertRedirect(route('public.form'));
        
        $this->assertDatabaseHas('form_response_answers', [
            'question_id' => $qRadio->id,
            'answer_text' => 'Hobi Saya Sendiri'
        ]);
    }

    public function test_fails_when_others_is_selected_but_empty_on_required_question(): void
    {
        $form = QuestionnaireForm::create([
            'id' => Str::uuid(),
            'title' => 'Exit Survey (After Yudisium)',
            'target_role' => 'alumni',
            'is_active' => true
        ]);

        $qAlumni = FormQuestion::create([
            'id' => Str::uuid(),
            'form_id' => $form->id,
            'question_text' => 'Pilih Nama Alumni',
            'question_type' => 'select',
            'is_required' => true,
            'sort_order' => 1
        ]);

        $qRadio = FormQuestion::create([
            'id' => Str::uuid(),
            'form_id' => $form->id,
            'question_text' => 'Mengapa memilih kampus ini?',
            'question_type' => 'radio',
            'is_required' => true,
            'has_others' => true,
            'sort_order' => 2
        ]);

        // Submit with Others selected but empty text input
        $response = $this->post('/kuesioner', [
            'form_id' => $form->id,
            'answers' => [
                $qAlumni->id => 'Budi Santoso',
                $qRadio->id => 'Others'
            ],
            'answers_others' => [
                $qRadio->id => ''
            ]
        ]);

        $response->assertSessionHasErrors(['answers.' . $qRadio->id]);
    }
}
