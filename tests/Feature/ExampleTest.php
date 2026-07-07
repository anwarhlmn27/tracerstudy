<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_export(): void
    {
        $user = \App\Models\User::factory()->create(['role' => 'admin']);
        
        // Let's create some responses to test mapping
        $form = \App\Models\QuestionnaireForm::create([
            'id' => \Illuminate\Support\Str::uuid(),
            'title' => 'Test Form',
            'target_role' => 'alumni',
            'angkatan' => '2022',
            'is_active' => true
        ]);
        
        $question = \App\Models\FormQuestion::create([
            'id' => \Illuminate\Support\Str::uuid(),
            'form_id' => $form->id,
            'question_text' => 'What is your job?',
            'question_type' => 'text',
            'is_required' => true,
            'sort_order' => 1
        ]);
        
        $response = \App\Models\FormResponse::create([
            'id' => \Illuminate\Support\Str::uuid(),
            'form_id' => $form->id,
            'user_id' => $user->id,
        ]);
        
        \App\Models\FormResponseAnswer::create([
            'id' => \Illuminate\Support\Str::uuid(),
            'response_id' => $response->id,
            'question_id' => $question->id,
            'answer_text' => 'Software Engineer'
        ]);

        $exportResponse = $this->actingAs($user)->get('/questionnaires/export');
        $exportResponse->assertStatus(200);
        $this->assertNotEmpty($exportResponse->streamedContent());
    }
}
