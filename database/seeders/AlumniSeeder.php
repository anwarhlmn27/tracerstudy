<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Student;
use App\Models\Prodi;
use App\Models\QuestionnaireForm;
use App\Models\FormQuestion;
use App\Models\FormResponse;
use App\Models\FormResponseAnswer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AlumniSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $univ = \App\Models\Univ::firstOrCreate(
            ['kode_univ' => 'KRW'],
            [
                'nama_univ' => 'Kalbis Institute',
                'address' => 'Jl. Pulomas Selatan Kav. 22, Jakarta Timur',
                'email' => 'info@kalbis.ac.id',
                'website' => 'https://kalbis.ac.id',
            ]
        );

        $fakultasFti = \App\Models\Fakultas::firstOrCreate(
            ['kode_fakultas' => 'FTI'],
            [
                'id_univs' => $univ->id,
                'nama_fakultas' => 'Fakultas Teknologi dan Infokom',
                'short_name' => 'FTI',
            ]
        );

        $fakultasFbdk = \App\Models\Fakultas::firstOrCreate(
            ['kode_fakultas' => 'FBDK'],
            [
                'id_univs' => $univ->id,
                'nama_fakultas' => 'Fakultas Bisnis dan Desain Kreatif',
                'short_name' => 'FBDK',
            ]
        );

        $prodiIf = Prodi::firstOrCreate(
            ['kode_prodi' => 'IF'],
            [
                'nama_prodi' => 'Informatika',
                'short_name' => 'IF',
                'fakultas_id' => $fakultasFti->id
            ]
        );
        $prodiIf->update(['fakultas_id' => $fakultasFti->id, 'short_name' => 'IF']);

        $prodiSi = Prodi::firstOrCreate(
            ['kode_prodi' => 'SI'],
            [
                'nama_prodi' => 'Sistem Informasi',
                'short_name' => 'SI',
                'fakultas_id' => $fakultasFti->id
            ]
        );
        $prodiSi->update(['fakultas_id' => $fakultasFti->id, 'short_name' => 'SI']);

        $prodiEk = Prodi::firstOrCreate(
            ['kode_prodi' => 'EK'],
            [
                'nama_prodi' => 'Ekonomi',
                'short_name' => 'EK',
                'fakultas_id' => $fakultasFbdk->id
            ]
        );
        $prodiEk->update(['fakultas_id' => $fakultasFbdk->id, 'short_name' => 'EK']);

        $students = [
            [
                'name' => 'Budi Santoso',
                'email' => 'alumni@tracer.ac.id',
                'nim' => '12345678',
                'prodi' => $prodiIf,
                'angkatan' => 2022,
                'status' => 'lulus',
                'status_alumni' => 'Bekerja (full time / part time)',
                'status_alumni_answer' => 'bekerja',
                'nama_perusahaan' => 'PT GoTo Gojek Tokopedia',
                'jabatan' => 'Software Engineer Backend',
                'tempat_kerja' => 'Nasional',
                'waktu_tunggu_kerja' => '2 bulan',
                'waktu_tunggu_number' => 2,
                'response_rate' => 100,
                'gaji' => "< 1.000.000",
                'kesesuaian' => 'Sesuai',
                'saran' => 'Perbanyak materi perkuliahan yang relevan dengan industri modern seperti Cloud Computing.'
            ],
            [
                'name' => 'Siti Aminah',
                'email' => 'alumni2@tracer.ac.id',
                'nim' => '87654321',
                'prodi' => $prodiSi,
                'angkatan' => 2023,
                'status' => 'aktif',
                'status_alumni' => 'Wiraswasta',
                'status_alumni_answer' => 'wiraswasta',
                'nama_perusahaan' => 'Siti Fashion Shop',
                'jabatan' => 'Owner & Founder',
                'tempat_kerja' => 'Lokal',
                'waktu_tunggu_kerja' => '1 bulan',
                'waktu_tunggu_number' => 1,
                'response_rate' => 100,
                'gaji' => "1.000.000 - 5.000.000",
                'kesesuaian' => 'Sangat Sesuai',
                'saran' => 'Tingkatkan pembelajaran manajemen bisnis dan e-commerce.'
            ],
            [
                'name' => 'Andi Wijaya',
                'email' => 'alumni3@tracer.ac.id',
                'nim' => '11223344',
                'prodi' => $prodiIf,
                'angkatan' => 2021,
                'status' => 'lulus',
                'status_alumni' => 'Melanjutkan Pendidikan',
                'status_alumni_answer' => 'studi_lanjut',
                'nama_perusahaan' => 'Universitas Indonesia',
                'jabatan' => 'Mahasiswa Magister Ilmu Komputer',
                'tempat_kerja' => 'Lokal',
                'waktu_tunggu_kerja' => '0 bulan',
                'waktu_tunggu_number' => 0,
                'response_rate' => 100,
                'gaji' => 0,
                'kesesuaian' => 'Sangat Sesuai',
                'saran' => 'Perkuat pemahaman dasar riset dan penulisan ilmiah.'
            ],
            [
                'name' => 'Rina Permata',
                'email' => 'alumni4@tracer.ac.id',
                'nim' => '55667788',
                'prodi' => $prodiEk,
                'angkatan' => 2024,
                'status' => 'aktif',
                'status_alumni' => 'Tidak kerja tetapi sedang mencari kerja',
                'status_alumni_answer' => 'mencari_kerja',
                'nama_perusahaan' => null,
                'jabatan' => null,
                'tempat_kerja' => null,
                'waktu_tunggu_kerja' => null,
                'waktu_tunggu_number' => null,
                'response_rate' => 100,
                'gaji' => 0,
                'kesesuaian' => 'Kurang Sesuai',
                'saran' => 'Perbanyak bimbingan karir dan info lowongan kerja.'
            ],
            [
                'name' => 'Ahmad Fauzi',
                'email' => 'alumni5@tracer.ac.id',
                'nim' => '99887766',
                'prodi' => $prodiIf,
                'angkatan' => 2022,
                'status' => 'lulus',
                'status_alumni' => 'Bekerja (full time / part time)',
                'status_alumni_answer' => 'bekerja',
                'nama_perusahaan' => 'PT Shopee International Indonesia',
                'jabatan' => 'Backend Developer',
                'tempat_kerja' => 'Multinasional',
                'waktu_tunggu_kerja' => '3 bulan',
                'waktu_tunggu_number' => 3,
                'response_rate' => 100,
                'gaji' => "10.000.000 - 20.000.000",
                'kesesuaian' => 'Sangat Sesuai',
                'saran' => 'Tingkatkan praktikum pemrograman web dan basis data.'
            ],
            [
                'name' => 'Dewi Lestari',
                'email' => 'alumni6@tracer.ac.id',
                'nim' => '44332211',
                'prodi' => $prodiSi,
                'angkatan' => 2022,
                'status' => 'lulus',
                'status_alumni' => 'Bekerja (full time / part time)',
                'status_alumni_answer' => 'bekerja',
                'nama_perusahaan' => 'PT Bukalapak.com',
                'jabatan' => 'Product Manager',
                'tempat_kerja' => 'Nasional',
                'waktu_tunggu_kerja' => '4 bulan',
                'waktu_tunggu_number' => 4,
                'response_rate' => 100,
                'gaji' => "5.000.000 - 10.000.000",
                'kesesuaian' => 'Sesuai',
                'saran' => 'Mata kuliah manajemen proyek IT sangat berguna di dunia kerja.'
            ],
            [
                'name' => 'Eko Prasetyo',
                'email' => 'alumni7@tracer.ac.id',
                'nim' => '12123434',
                'prodi' => $prodiEk,
                'angkatan' => 2023,
                'status' => 'lulus',
                'status_alumni' => 'Wiraswasta',
                'status_alumni_answer' => 'wiraswasta',
                'nama_perusahaan' => 'CV Kopi Nusantara',
                'jabatan' => 'Founder & Head Barista',
                'tempat_kerja' => 'Lokal',
                'waktu_tunggu_kerja' => '2-bulan',
                'waktu_tunggu_number' => 2,
                'response_rate' => 100,
                'gaji' => "5.000.000 - 10.000.000",
                'kesesuaian' => 'Tidak Sesuai',
                'saran' => 'Kurikulum sudah cukup baik, perlu pengembangan soft skills mahasiswa.'
            ],
            [
                'name' => 'Fitriani',
                'email' => 'alumni8@tracer.ac.id',
                'nim' => '56567878',
                'prodi' => $prodiIf,
                'angkatan' => 2023,
                'status' => 'lulus',
                'status_alumni' => 'Belum memungkinkan bekerja',
                'status_alumni_answer' => 'belum_memungkinkan',
                'nama_perusahaan' => null,
                'jabatan' => null,
                'tempat_kerja' => null,
                'waktu_tunggu_kerja' => null,
                'waktu_tunggu_number' => null,
                'response_rate' => 100,
                'gaji' => 0,
                'kesesuaian' => 'Tidak Sesuai',
                'saran' => 'Beri fleksibilitas waktu studi bagi yang memiliki keterbatasan.'
            ],
            [
                'name' => 'Hendra Wijaya',
                'email' => 'alumni9@tracer.ac.id',
                'nim' => '78789090',
                'prodi' => $prodiSi,
                'angkatan' => 2021,
                'status' => 'lulus',
                'status_alumni' => 'Bekerja (full time / part time)',
                'status_alumni_answer' => 'bekerja',
                'nama_perusahaan' => 'PT Grab Teknologi Indonesia',
                'jabatan' => 'Frontend Engineer',
                'tempat_kerja' => 'Multinasional',
                'waktu_tunggu_kerja' => '1 bulan',
                'waktu_tunggu_number' => 1,
                'response_rate' => 100,
                'gaji' => "10.000.000 - 20.000.000",
                'kesesuaian' => 'Sangat Sesuai',
                'saran' => 'Perbanyak materi UI/UX modern.'
            ],
            [
                'name' => 'Indah Cahyani',
                'email' => 'alumni10@tracer.ac.id',
                'nim' => '90901212',
                'prodi' => $prodiEk,
                'angkatan' => 2022,
                'status' => 'lulus',
                'status_alumni' => 'Bekerja (full time / part time)',
                'status_alumni_answer' => 'bekerja',
                'nama_perusahaan' => 'PT Bank Rakyat Indonesia',
                'jabatan' => 'Relationship Manager',
                'tempat_kerja' => 'Nasional',
                'waktu_tunggu_kerja' => '3 bulan',
                'waktu_tunggu_number' => 3,
                'response_rate' => 100,
                'gaji' => "5.000.000 - 10.000.000",
                'kesesuaian' => 'Sesuai',
                'saran' => 'Materi akuntansi keuangan dan analisis risiko sangat membantu.'
            ],
        ];

        // Cari form kuesioner Tracer Study untuk alumni
        $form = QuestionnaireForm::where('form_group', 'Tracer Study (1 Year After Yudisium)')
            ->where('is_active', true)
            ->first();

        // Cari pertanyaan-pertanyaannya
        $questions = [];
        if ($form) {
            $questions = FormQuestion::where('form_id', $form->id)
                ->orderBy('sort_order', 'asc')
                ->get();
        }

        foreach ($students as $studentData) {
            $user = User::firstOrCreate(
                ['email' => $studentData['email']],
                [
                    'id' => Str::uuid(),
                    'name' => $studentData['name'],
                    'password' => Hash::make('password123'),
                    'role' => 'alumni',
                ]
            );

            $student = Student::updateOrCreate(
                ['nim' => $studentData['nim']],
                [
                    'id' => Str::uuid(),
                    'user_id' => $user->id,
                    'prodi_id' => $studentData['prodi']->id,
                    'nama_student' => $studentData['name'],
                    'angkatan' => $studentData['angkatan'],
                    'status' => $studentData['status'],
                    'status_alumni' => $studentData['status_alumni'],
                    'nama_perusahaan' => $studentData['nama_perusahaan'],
                    'jabatan' => $studentData['jabatan'],
                    'tempat_kerja' => $studentData['tempat_kerja'],
                    'response_rate' => $studentData['response_rate'],
                    'waktu_tunggu_kerja' => $studentData['waktu_tunggu_kerja'],
                ]
            );

            // Jika form dan pertanyaan ditemukan, buat response & response answers
            if ($form && count($questions) > 0) {
                // Hapus response lama agar tidak duplikat jika seeder dijalankan ulang
                FormResponse::where('form_id', $form->id)
                    ->where('user_id', $user->id)
                    ->delete();

                $response = FormResponse::create([
                    'id' => Str::uuid(),
                    'form_id' => $form->id,
                    'user_id' => $user->id,
                    'created_at' => now()->subDays(rand(1, 30)), // Buat bervariasi
                ]);

                foreach ($questions as $q) {
                    $answerText = null;

                    // Cocokkan pertanyaan berdasarkan isinya
                    if (str_contains($q->question_text, 'Status Anda')) {
                        $answerText = $studentData['status_alumni_answer'];
                    } elseif (str_contains($q->question_text, 'waktu tunggu')) {
                        $answerText = $studentData['waktu_tunggu_number'];
                    } elseif (str_contains($q->question_text, 'nama perusahaan')) {
                        $answerText = $studentData['nama_perusahaan'];
                    } elseif (str_contains($q->question_text, 'jabatan')) {
                        $answerText = $studentData['jabatan'];
                    } elseif (str_contains($q->question_text, 'skala tempat kerja')) {
                        $answerText = strtolower($studentData['tempat_kerja'] ?? '');
                    } elseif (str_contains($q->question_text, 'pendapatan')) {
                        $answerText = $studentData['gaji'];
                    } elseif (str_contains($q->question_text, 'sesuai dengan program studi')) {
                        $answerText = $studentData['kesesuaian'];
                    } elseif (str_contains($q->question_text, 'kurikulum')) {
                        $answerText = $studentData['saran'];
                    }

                    FormResponseAnswer::create([
                        'id' => Str::uuid(),
                        'response_id' => $response->id,
                        'question_id' => $q->id,
                        'answer_text' => $answerText,
                    ]);
                }
            }
        }
    }
}
