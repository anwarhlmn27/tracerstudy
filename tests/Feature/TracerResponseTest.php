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

class TracerResponseTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_form(): void
    {
        $response = $this->get('/form');
        $response->assertRedirect('/');
    }

    public function test_alumni_with_incomplete_profile_is_redirected(): void
    {
        $user = User::factory()->create(['role' => 'alumni']);

        // Since student profile is incomplete, it should redirect to profile.edit
        $response = $this->actingAs($user)->get('/form');

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('warning', 'Harap lengkapi data diri Anda (NIM, Program Studi, Angkatan, dan Status Alumni) terlebih dahulu sebelum mengisi form.');
    }

    public function test_alumni_with_complete_profile_can_view_form_no_active_form(): void
    {
        $user = User::factory()->create(['role' => 'alumni']);
        $prodi = Prodi::create([
            'kode_prodi' => 'TI',
            'nama_prodi' => 'Teknik Informatika',
            'short_name' => 'TI'
        ]);
        $student = Student::create([
            'user_id' => $user->id,
            'prodi_id' => $prodi->id,
            'nim' => '12345678',
            'nama_student' => 'Test Student',
            'angkatan' => 2022,
            'status' => 'lulus',
            'status_alumni' => 'Bekerja (full time / part time)'
        ]);

        $response = $this->actingAs($user)->get('/form');

        $response->assertStatus(200);
        $response->assertViewHas('activeForm', null);
        $response->assertSee('Tidak Ada Kuesioner');
    }

    public function test_alumni_with_complete_profile_can_view_active_form(): void
    {
        $user = User::factory()->create(['role' => 'alumni']);
        $prodi = Prodi::create([
            'kode_prodi' => 'TI',
            'nama_prodi' => 'Teknik Informatika',
            'short_name' => 'TI'
        ]);
        $student = Student::create([
            'user_id' => $user->id,
            'prodi_id' => $prodi->id,
            'nim' => '12345678',
            'nama_student' => 'Test Student',
            'angkatan' => 2022,
            'status' => 'lulus',
            'status_alumni' => 'Bekerja (full time / part time)'
        ]);

        $form = QuestionnaireForm::create([
            'id' => Str::uuid(),
            'title' => 'Form Tracer 2022',
            'target_role' => 'alumni',
            'angkatan' => '2022',
            'is_active' => true
        ]);

        $response = $this->actingAs($user)->get('/form');

        $response->assertStatus(200);
        $response->assertViewHas('activeForm');
        $response->assertSee('Form Tracer 2022');
    }

    public function test_alumni_can_submit_valid_tracer_response(): void
    {
        $user = User::factory()->create(['role' => 'alumni']);
        $prodi = Prodi::create([
            'kode_prodi' => 'TI',
            'nama_prodi' => 'Teknik Informatika',
            'short_name' => 'TI'
        ]);
        $student = Student::create([
            'user_id' => $user->id,
            'prodi_id' => $prodi->id,
            'nim' => '12345678',
            'nama_student' => 'Test Student',
            'angkatan' => 2022,
            'status' => 'lulus',
            'status_alumni' => 'Bekerja (full time / part time)'
        ]);

        $form = QuestionnaireForm::create([
            'id' => Str::uuid(),
            'title' => 'Form Tracer 2022',
            'target_role' => 'alumni',
            'angkatan' => '2022',
            'is_active' => true
        ]);

        $question = FormQuestion::create([
            'id' => Str::uuid(),
            'form_id' => $form->id,
            'question_text' => 'Berapa lama waktu tunggu kerja?',
            'question_type' => 'text',
            'is_required' => true,
            'sort_order' => 1
        ]);

        $response = $this->actingAs($user)->post('/form', [
            'form_id' => $form->id,
            'answers' => [
                $question->id => '3 bulan'
            ]
        ]);

        $response->assertRedirect(route('form.create'));
        $response->assertSessionHas('success', 'Terima kasih telah berpartisipasi mengisi kuesioner ini!');

        $this->assertDatabaseHas('form_responses', [
            'form_id' => $form->id,
            'user_id' => $user->id,
        ]);

        $this->assertDatabaseHas('form_response_answers', [
            'question_id' => $question->id,
            'answer_text' => '3 bulan',
        ]);
    }

    public function test_alumni_cannot_submit_duplicate_tracer_response(): void
    {
        $user = User::factory()->create(['role' => 'alumni']);
        $prodi = Prodi::create([
            'kode_prodi' => 'TI',
            'nama_prodi' => 'Teknik Informatika',
            'short_name' => 'TI'
        ]);
        $student = Student::create([
            'user_id' => $user->id,
            'prodi_id' => $prodi->id,
            'nim' => '12345678',
            'nama_student' => 'Test Student',
            'angkatan' => 2022,
            'status' => 'lulus',
            'status_alumni' => 'Bekerja (full time / part time)'
        ]);

        $form = QuestionnaireForm::create([
            'id' => Str::uuid(),
            'title' => 'Form Tracer 2022',
            'target_role' => 'alumni',
            'angkatan' => '2022',
            'is_active' => true
        ]);

        $question = FormQuestion::create([
            'id' => Str::uuid(),
            'form_id' => $form->id,
            'question_text' => 'Berapa lama waktu tunggu kerja?',
            'question_type' => 'text',
            'is_required' => true,
            'sort_order' => 1
        ]);

        // Create first response
        $formResponse = FormResponse::create([
            'id' => Str::uuid(),
            'form_id' => $form->id,
            'user_id' => $user->id,
        ]);

        FormResponseAnswer::create([
            'id' => Str::uuid(),
            'response_id' => $formResponse->id,
            'question_id' => $question->id,
            'answer_text' => '3 bulan'
        ]);

        // Try to submit second response
        $response = $this->actingAs($user)->post('/form', [
            'form_id' => $form->id,
            'answers' => [
                $question->id => '6 bulan'
            ]
        ]);

        $response->assertSessionHasErrors(['form_id' => 'Anda sudah mengisi kuesioner dalam kategori ini sebelumnya.']);
        $this->assertEquals(1, FormResponse::where('form_id', $form->id)->where('user_id', $user->id)->count());
    }

    public function test_atasan_needs_to_select_student_first(): void
    {
        $user = User::factory()->create(['role' => 'atasan']);

        $response = $this->actingAs($user)->get('/form');

        $response->assertStatus(200);
        $response->assertViewHas('needsToSelectStudent', true);
        $response->assertSee('Evaluasi Alumni');
    }
}
