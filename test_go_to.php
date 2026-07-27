<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Test creating an option with go_to_section = 2
$form = App\Models\QuestionnaireForm::first();
$q = $form->questions()->whereIn('question_type', ['radio', 'select', 'checkbox'])->first();
if ($q) {
    $opt = current($q->options()->get()->all());
    if ($opt) {
        $opt->update(['go_to_section' => 2]);
        echo "Successfully updated to: " . $opt->go_to_section . "\n";
        
        $opt->refresh();
        echo "After refresh: " . $opt->go_to_section . "\n";
    } else {
        echo "No options found.\n";
    }
} else {
    echo "No matching questions found.\n";
}
