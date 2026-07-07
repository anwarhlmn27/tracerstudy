<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class QuestionnaireSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $forms = [
            [
                'id' => Str::uuid(),
                'title' => 'Tracer Study (1 Year After Yudisium)',
                'target_role' => 'alumni',
                'angkatan' => null,
                'form_group' => 'Tracer Study (1 Year After Yudisium)',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'title' => 'Exit Survey (After Yudisium)',
                'target_role' => 'alumni',
                'angkatan' => null,
                'form_group' => 'Exit Survey (After Yudisium)',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'title' => 'User Survey (After Tracer Study)',
                'target_role' => 'atasan',
                'angkatan' => null,
                'form_group' => 'User Survey (After Tracer Study)',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        $formIds = [];

        foreach ($forms as $form) {
            $existing = DB::table('questionnaire_forms')->where('title', $form['title'])->first();
            if (!$existing) {
                DB::table('questionnaire_forms')->insert($form);
                $formIds[$form['title']] = $form['id'];
            } else {
                $formIds[$form['title']] = $existing->id;
            }
        }

        // Define form questions structure
        $formQuestionsData = [
            'Tracer Study (1 Year After Yudisium)' => [
                [
                    'text' => 'Pilih Universitas',
                    'type' => 'select',
                    'options' => []
                ],
                [
                    'text' => 'Pilih Fakultas',
                    'type' => 'select',
                    'options' => []
                ],
                [
                    'text' => 'Pilih Program Studi',
                    'type' => 'select',
                    'options' => []
                ],
                [
                    'text' => 'Pilih Nama Alumni',
                    'type' => 'select',
                    'options' => []
                ],
                [
                    'text' => 'Status Anda saat ini?',
                    'type' => 'radio',
                    'options' => [
                        'bekerja',
                        'wiraswasta',
                        'studi_lanjut',
                        'mencari_kerja',
                        'belum_memungkinkan'
                    ]
                ],
                [
                    'text' => 'Berapa bulan waktu tunggu Anda untuk mendapatkan pekerjaan pertama setelah lulus?',
                    'type' => 'number',
                    'options' => []
                ],
                [
                    'text' => 'Apa nama perusahaan tempat Anda bekerja saat ini?',
                    'type' => 'text',
                    'options' => []
                ],
                [
                    'text' => 'Apa jabatan Anda di perusahaan saat ini?',
                    'type' => 'text',
                    'options' => []
                ],
                [
                    'text' => 'Apa skala tempat kerja Anda saat ini?',
                    'type' => 'radio',
                    'options' => [
                        'lokal',
                        'nasional',
                        'multinasional'
                    ]
                ],
                [
                    'text' => 'Berapa perkiraan pendapatan rata-rata per bulan? (Dalam Rupiah)',
                    'type' => 'radio',
                    'options' => [
                        '< 1.000.000',
                        '1.000.000 - 5.000.000',
                        '5.000.000 - 10.000.000',
                        '10.000.000 - 20.000.000',
                        '> 20.000.000',
                    ]
                ],
                [
                    'text' => 'Apakah pekerjaan Anda saat ini sesuai dengan program studi Anda?',
                    'type' => 'radio',
                    'options' => [
                        'Sangat Sesuai',
                        'Sesuai',
                        'Kurang Sesuai',
                        'Tidak Sesuai'
                    ]
                ],
                [
                    'text' => 'Saran atau masukan untuk perbaikan kurikulum?',
                    'type' => 'textarea',
                    'options' => []
                ]
            ],
            'Exit Survey (After Yudisium)' => [
                [
                    'text' => 'Pilih Universitas',
                    'type' => 'select',
                    'options' => []
                ],
                [
                    'text' => 'Pilih Fakultas',
                    'type' => 'select',
                    'options' => []
                ],
                [
                    'text' => 'Pilih Program Studi',
                    'type' => 'select',
                    'options' => []
                ],
                [
                    'text' => 'Pilih Nama Alumni',
                    'type' => 'select',
                    'options' => []
                ],
                [
                    'text' => 'Menurut Anda, bagaimana relevansi materi perkuliahan dengan kebutuhan dunia kerja?',
                    'type' => 'radio',
                    'options' => [
                        'Sangat Relevan',
                        'Relevan',
                        'Kurang Relevan',
                        'Tidak Relevan'
                    ]
                ],
                [
                    'text' => 'Secara umum, bagaimana tingkat kepuasan Anda terhadap fasilitas pembelajaran di kampus?',
                    'type' => 'radio',
                    'options' => [
                        'Sangat Puas',
                        'Puas',
                        'Cukup Puas',
                        'Tidak Puas'
                    ]
                ],
                [
                    'text' => 'Berikan saran untuk pengembangan program studi di masa depan:',
                    'type' => 'textarea',
                    'options' => []
                ]
            ],
            'User Survey (After Tracer Study)' => [
                [
                    'text' => 'Pilih Program Studi',
                    'type' => 'select',
                    'options' => []
                ],
                [
                    'text' => 'Pilih Nama Alumni',
                    'type' => 'select',
                    'options' => []
                ],
                [
                    'text' => 'Bagaimana etika dan integritas alumni kami yang bekerja di instansi Anda?',
                    'type' => 'radio',
                    'options' => [
                        'Sangat Baik',
                        'Baik',
                        'Cukup',
                        'Kurang'
                    ]
                ],
                [
                    'text' => 'Bagaimana keahlian profesional (kompetensi utama) alumni kami dalam menyelesaikan pekerjaan?',
                    'type' => 'radio',
                    'options' => [
                        'Sangat Baik',
                        'Baik',
                        'Cukup',
                        'Kurang'
                    ]
                ],
                [
                    'text' => 'Bagaimana kemampuan komunikasi alumni kami?',
                    'type' => 'radio',
                    'options' => [
                        'Sangat Baik',
                        'Baik',
                        'Cukup',
                        'Kurang'
                    ]
                ],
                [
                    'text' => 'Bagaimana kemampuan kerja sama tim alumni kami?',
                    'type' => 'radio',
                    'options' => [
                        'Sangat Baik',
                        'Baik',
                        'Cukup',
                        'Kurang'
                    ]
                ],
                [
                    'text' => 'Bagaimana kemampuan adaptasi dan pengembangan diri alumni kami?',
                    'type' => 'radio',
                    'options' => [
                        'Sangat Baik',
                        'Baik',
                        'Cukup',
                        'Kurang'
                    ]
                ],
                [
                    'text' => 'Saran atau masukan untuk peningkatan kualitas lulusan kami:',
                    'type' => 'textarea',
                    'options' => []
                ]
            ]
        ];

        // Seed questions for each form
        foreach ($formQuestionsData as $formTitle => $questions) {
            if (isset($formIds[$formTitle])) {
                $fId = $formIds[$formTitle];
                // Hapus pertanyaan lama jika seeder dijalankan ulang untuk mencegah duplikasi
                DB::table('form_questions')->where('form_id', $fId)->delete();

                foreach ($questions as $index => $q) {
                    $questionId = Str::uuid();

                    DB::table('form_questions')->insert([
                        'id' => $questionId,
                        'form_id' => $fId,
                        'question_text' => $q['text'],
                        'question_type' => $q['type'],
                        'is_required' => true,
                        'sort_order' => $index + 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    if (!empty($q['options'])) {
                        foreach ($q['options'] as $optIndex => $optionText) {
                            DB::table('form_question_options')->insert([
                                'id' => Str::uuid(),
                                'question_id' => $questionId,
                                'option_text' => $optionText,
                                'sort_order' => $optIndex + 1,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }
                }
            }
        }
    }
}
