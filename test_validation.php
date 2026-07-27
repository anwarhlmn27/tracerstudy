<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\QuestionnaireForm;
use Illuminate\Http\Request;

$form = QuestionnaireForm::first();
$jsonPayload = json_encode([
    [
        'text' => 'Test Question',
        'type' => 'radio',
        'required' => 1,
        'section_id' => 1,
        'options' => ['Option A', 'Option B'],
        'go_to_sections' => ['0' => '2', '1' => '']
    ]
]);

$request = Request::create('/master-form/' . $form->id, 'PUT', [
    'title' => 'Test Form',
    'target_role' => 'alumni',
    'questions_json' => $jsonPayload
]);

$decoded = json_decode($request->input('questions_json'), true);
foreach ($decoded as &$qData) {
    if (isset($qData['go_to_sections']) && is_array($qData['go_to_sections'])) {
        foreach ($qData['go_to_sections'] as $k => $v) {
            if ($v === '') {
                $qData['go_to_sections'][$k] = null;
            }
        }
    }
}
$request->merge(['questions' => $decoded]);

$validator = Validator::make($request->all(), [
    'title' => ['required', 'string', 'max:255'],
    'target_role' => ['required'],
    'questions' => ['required', 'array'],
    'questions.*.go_to_sections' => ['nullable', 'array'],
    'questions.*.go_to_sections.*' => ['nullable', 'integer'],
]);

if ($validator->fails()) {
    echo "Validation failed!\n";
    print_r($validator->errors()->all());
} else {
    echo "Validation passed!\n";
    $q = $request->input('questions')[0];
    print_r($q['go_to_sections']);
}
