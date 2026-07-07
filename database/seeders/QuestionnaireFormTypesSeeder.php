<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class QuestionnaireFormTypesSeeder extends Seeder
{
    public function run(): void
    {
        $forms = [
            [
                'id' => Str::uuid(),
                'title' => 'Tracer Study (1 Year After Yudisium)',
                'target_role' => 'alumni',
                'angkatan' => null,
                'form_group' => 'Tracer Study',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'title' => 'Exit Survey (After Yudisium)',
                'target_role' => 'alumni',
                'angkatan' => null,
                'form_group' => 'Exit Survey',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'title' => 'User Survey (After Tracer Study)',
                'target_role' => 'atasan',
                'angkatan' => null,
                'form_group' => 'User Survey',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($forms as $form) {
            // Cek apakah sudah ada untuk menghindari duplikat jika dijalankan ulang
            $exists = DB::table('questionnaire_forms')->where('title', $form['title'])->exists();
            if (!$exists) {
                DB::table('questionnaire_forms')->insert($form);
            }
        }
    }
}
