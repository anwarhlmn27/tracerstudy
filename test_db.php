<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Dump the actual saved form questions and options
$form = App\Models\QuestionnaireForm::first();
foreach ($form->questions as $q) {
    if (in_array($q->question_type, ['radio', 'select', 'checkbox'])) {
        echo "Q: " . $q->question_text . " (Section " . $q->section_id . ")\n";
        foreach ($q->options as $opt) {
            echo "  - " . $opt->option_text . " -> go_to_section: " . var_export($opt->go_to_section, true) . "\n";
        }
    }
}
