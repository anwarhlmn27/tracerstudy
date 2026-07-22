<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use App\Models\Prodi;
use App\Models\Univ;
use App\Models\Fakultas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Shuchkin\SimpleXLSX;
use Shuchkin\SimpleXLSXGen;

class AlumniController extends Controller
{
    /**
     * Display a listing of students (alumni data).
     */
    public function index(Request $request)
    {
        $univId     = $request->input('univ_id');
        $fakultasId = $request->input('fakultas_id');
        $prodiId    = $request->input('prodi_id');
        $angkatan   = $request->input('angkatan');

        $query = Student::with(['user', 'prodi.fakultas.univ']);

        if ($prodiId) {
            $query->where('prodi_id', $prodiId);
        } elseif ($fakultasId) {
            $query->whereHas('prodi', function ($q) use ($fakultasId) {
                $q->where('fakultas_id', $fakultasId);
            });
        } elseif ($univId) {
            $query->whereHas('prodi.fakultas', function ($q) use ($univId) {
                $q->where('id_univs', $univId);
            });
        }

        if ($angkatan) {
            $query->where('angkatan', $angkatan);
        }

        $students = $query->orderBy('created_at', 'desc')->get();

        $univs = Univ::orderBy('nama_univ')->get(['id', 'nama_univ']);

        $fakultasList = $univId
            ? Fakultas::where('id_univs', $univId)->orderBy('nama_fakultas')->get(['id', 'nama_fakultas'])
            : collect();

        $prodiList = $fakultasId
            ? Prodi::where('fakultas_id', $fakultasId)->orderBy('nama_prodi')->get(['id', 'nama_prodi'])
            : ($univId
                ? Prodi::whereHas('fakultas', fn($q) => $q->where('id_univs', $univId))->orderBy('nama_prodi')->get(['id', 'nama_prodi'])
                : Prodi::orderBy('nama_prodi')->get(['id', 'nama_prodi']));

        $angkatans = Student::whereNotNull('angkatan')
            ->distinct()
            ->orderBy('angkatan', 'desc')
            ->pluck('angkatan');

        $prodis = Prodi::orderBy('nama_prodi')->get();

        $activeUniv     = $univId     ? Univ::find($univId)     : null;
        $activeFakultas = $fakultasId ? Fakultas::find($fakultasId) : null;
        $activeProdi    = $prodiId    ? Prodi::find($prodiId)   : null;

        return view('alumni', compact(
            'students',
            'prodis',
            'univs',
            'fakultasList',
            'prodiList',
            'angkatans',
            'univId',
            'fakultasId',
            'prodiId',
            'angkatan',
            'activeUniv',
            'activeFakultas',
            'activeProdi'
        ));
    }

    /**
     * Store a newly created student in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_student'    => ['required', 'string', 'max:255'],
            'nim'             => ['required', 'string', 'max:20', 'unique:students,nim'],
            'email'           => ['required', 'email', 'max:255', 'unique:users,email'],
            'prodi_id'        => ['required', 'exists:prodis,id'],
            'angkatan'        => ['required', 'integer', 'min:2000', 'max:2099'],
            'status'          => ['required', Rule::in(['aktif', 'lulus', 'cuti', 'drop_out'])],
            'status_alumni'   => ['nullable', 'string', Rule::in(['Bekerja (full time / part time)', 'Belum memungkinkan bekerja', 'Wiraswasta', 'Melanjutkan Pendidikan', 'Tidak kerja tetapi sedang mencari kerja'])],
            'nama_perusahaan' => ['nullable', 'string', 'max:255'],
            'jabatan'         => ['nullable', 'string', 'max:255'],
            'tempat_kerja'    => ['nullable', 'string', Rule::in(['Lokal', 'Nasional', 'Multinasional'])],
            'response_rate'   => ['nullable', 'integer', 'min:0', 'max:100'],
            'waktu_tunggu_kerja' => ['nullable', 'string', 'max:255'],
        ]);

        $user = User::create([
            'id'       => Str::uuid(),
            'name'     => $validated['nama_student'],
            'email'    => $validated['email'],
            'password' => Hash::make('password123'),
            'role'     => 'alumni',
        ]);

        Student::create([
            'id'               => Str::uuid(),
            'user_id'          => $user->id,
            'prodi_id'         => $validated['prodi_id'],
            'nim'              => $validated['nim'],
            'nama_student'     => $validated['nama_student'],
            'angkatan'         => $validated['angkatan'],
            'status'           => $validated['status'],
            'status_alumni'    => $validated['status_alumni'] ?? null,
            'nama_perusahaan'  => $validated['nama_perusahaan'] ?? null,
            'jabatan'          => $validated['jabatan'] ?? null,
            'tempat_kerja'     => $validated['tempat_kerja'] ?? null,
            'response_rate'    => $validated['response_rate'] ?? null,
            'waktu_tunggu_kerja' => $validated['waktu_tunggu_kerja'] ?? null,
        ]);

        return back()->with('success', 'Data alumni berhasil ditambahkan!');
    }

    /**
     * Update the specified student in storage.
     */
    public function update(Request $request, string $id)
    {
        $student = Student::findOrFail($id);

        $validated = $request->validate([
            'nama_student'    => ['required', 'string', 'max:255'],
            'nim'             => ['required', 'string', 'max:20', Rule::unique('students', 'nim')->ignore($student->id)],
            'prodi_id'        => ['required', 'exists:prodis,id'],
            'angkatan'        => ['required', 'integer', 'min:2000', 'max:2099'],
            'status'          => ['required', Rule::in(['aktif', 'lulus', 'cuti', 'drop_out'])],
            'status_alumni'   => ['nullable', 'string', Rule::in(['Bekerja (full time / part time)', 'Belum memungkinkan bekerja', 'Wiraswasta', 'Melanjutkan Pendidikan', 'Tidak kerja tetapi sedang mencari kerja'])],
            'nama_perusahaan' => ['nullable', 'string', 'max:255'],
            'jabatan'         => ['nullable', 'string', 'max:255'],
            'tempat_kerja'    => ['nullable', 'string', Rule::in(['Lokal', 'Nasional', 'Multinasional'])],
            'response_rate'   => ['nullable', 'integer', 'min:0', 'max:100'],
            'waktu_tunggu_kerja' => ['nullable', 'string', 'max:255'],
        ]);

        $student->update([
            'nama_student'     => $validated['nama_student'],
            'nim'              => $validated['nim'],
            'prodi_id'         => $validated['prodi_id'],
            'angkatan'         => $validated['angkatan'],
            'status'           => $validated['status'],
            'status_alumni'    => $validated['status_alumni'] ?? null,
            'nama_perusahaan'  => $validated['nama_perusahaan'] ?? null,
            'jabatan'          => $validated['jabatan'] ?? null,
            'tempat_kerja'     => $validated['tempat_kerja'] ?? null,
            'response_rate'    => $validated['response_rate'] ?? null,
            'waktu_tunggu_kerja' => $validated['waktu_tunggu_kerja'] ?? null,
        ]);

        if ($student->user) {
            $student->user->update(['name' => $validated['nama_student']]);
        }

        return back()->with('success', 'Data alumni berhasil diperbarui!');
    }

    /**
     * Remove the specified student from storage.
     */
    public function destroy(string $id)
    {
        $student = Student::findOrFail($id);

        if ($student->user) {
            $student->user->delete();
        }

        $student->delete();

        return back()->with('success', 'Data alumni berhasil dihapus!');
    }

    // ──────────────────────────────────────────────────────────
    //  IMPORT EXCEL
    // ──────────────────────────────────────────────────────────

    /**
     * Valid values reference
     */
    private array $validStatus      = ['aktif', 'lulus', 'cuti', 'drop_out'];
    private array $validStatusAlumni = [
        'Bekerja (full time / part time)',
        'Wiraswasta',
        'Melanjutkan Pendidikan',
        'Tidak kerja tetapi sedang mencari kerja',
        'Belum memungkinkan bekerja',
    ];
    private array $validTempatKerja = ['Lokal', 'Nasional', 'Multinasional'];

    /**
     * Download the Excel import template (6 kolom inti saja).
     */
    public function downloadTemplate()
    {
        // Header row
        $headers = [
            'NIM *',
            'Nama Lengkap *',
            'Email *',
            'Kode Prodi *',
            'Angkatan *',
            'Status * (aktif/lulus/cuti/drop_out)',
        ];

        // Contoh baris data
        $example = [
            '2021001',
            'Budi Santoso',
            'budi@example.com',
            'TI001',
            '2021',
            'lulus',
        ];

        // Referensi kode prodi di sheet yang sama
        $prodis   = Prodi::with('fakultas.univ')->orderBy('kode_prodi')->get();
        $prodiRef = [['', '', '', '', '', ''], ['=== REFERENSI KODE PRODI (jangan diedit) ==='], ['Kode Prodi', 'Nama Prodi', 'Fakultas', 'Universitas']];
    
        $rows = [$headers, $example];

        $xlsx = SimpleXLSXGen::fromArray($rows);
        $xlsx->downloadAs('template_import_alumni.xlsx');
        exit;
    }

    /**
     * Import alumni data from Excel file.
     * Kolom: NIM | Nama | Email | Kode Prodi | Angkatan | Status
     * Baris pertama (header) dilewati otomatis.
     */
    public function import(Request $request)
    {
        // Prevent execution timeout on large imports (e.g. 300+ records)
        @set_time_limit(300);
        @ini_set('memory_limit', '512M');

        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls', 'max:5120'],
        ], [
            'file.required' => 'Pilih file Excel terlebih dahulu.',
            'file.mimes'    => 'File harus berformat .xlsx atau .xls.',
            'file.max'      => 'Ukuran file maksimal 5 MB.',
        ]);

        $path = $request->file('file')->getRealPath();
        $xlsx = SimpleXLSX::parse($path);

        if (!$xlsx) {
            return back()->with('error', 'File Excel tidak dapat dibaca. Pastikan format file benar.');
        }

        $rows = $xlsx->rows();

        if (count($rows) < 2) {
            return back()->with('error', 'File Excel kosong atau hanya berisi header.');
        }

        $imported = 0;
        $skipped  = 0;
        $errors   = [];

        // Build prodi lookup: kode_prodi → prodi id
        $prodiMap = Prodi::pluck('id', 'kode_prodi')->toArray();

        // Pre-fetch existing NIMs & Emails for fast in-memory duplicate checking (O(1) lookup)
        $existingNims   = Student::pluck('nim')->mapWithKeys(fn($n) => [(string) $n => true])->toArray();
        $existingEmails = User::pluck('email')->mapWithKeys(fn($e) => [strtolower($e) => true])->toArray();

        // Pre-hash default password ONCE to eliminate Bcrypt CPU bottleneck (300 bcrypt calls = ~45s)
        $defaultPasswordHash = Hash::make('password123');

        \Illuminate\Support\Facades\DB::transaction(function () use (
            $rows, $prodiMap, &$existingNims, &$existingEmails, $defaultPasswordHash,
            &$imported, &$skipped, &$errors
        ) {
            foreach ($rows as $rowIndex => $row) {
                // Baris 0 = header → lewati
                if ($rowIndex === 0) continue;

                // Lewati baris kosong atau baris referensi (===)
                $rowStr = implode('', array_map('trim', $row));
                if (empty($rowStr) || str_starts_with(trim($row[0] ?? ''), '===')) {
                    continue;
                }

                $lineNum = $rowIndex + 1;

                // ── Pemetaan kolom (hanya 6 kolom inti) ──
                $nim          = trim((string)($row[0] ?? ''));
                $namaStudent  = trim($row[1] ?? '');
                $email        = strtolower(trim($row[2] ?? ''));
                $kodeProdi    = trim($row[3] ?? '');
                $angkatan     = trim($row[4] ?? '');
                $status       = strtolower(trim($row[5] ?? ''));

                // ── Validasi per-baris ──
                $rowErrors = [];

                if (empty($nim))
                    $rowErrors[] = 'NIM wajib diisi';
                if (empty($namaStudent))
                    $rowErrors[] = 'Nama wajib diisi';
                if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL))
                    $rowErrors[] = 'Email tidak valid';
                if (!array_key_exists($kodeProdi, $prodiMap))
                    $rowErrors[] = "Kode Prodi '$kodeProdi' tidak ditemukan";
                if (!is_numeric($angkatan) || $angkatan < 2000 || $angkatan > 2099)
                    $rowErrors[] = 'Angkatan tidak valid (2000–2099)';
                if (!in_array($status, $this->validStatus))
                    $rowErrors[] = "Status '$status' tidak valid (aktif/lulus/cuti/drop_out)";

                if (!empty($rowErrors)) {
                    $errors[] = "Baris $lineNum: " . implode(', ', $rowErrors);
                    $skipped++;
                    continue;
                }

                // ── Cek duplikat (in-memory fast check) ──
                if (isset($existingNims[$nim])) {
                    $errors[] = "Baris $lineNum: NIM '$nim' sudah terdaftar, dilewati.";
                    $skipped++;
                    continue;
                }
                if (isset($existingEmails[$email])) {
                    $errors[] = "Baris $lineNum: Email '$email' sudah terdaftar, dilewati.";
                    $skipped++;
                    continue;
                }

                // Catat di memory agar duplikat dalam file yang sama juga terdeteksi
                $existingNims[$nim]     = true;
                $existingEmails[$email] = true;

                // ── Simpan: User + Student ──
                $user = User::create([
                    'name'     => $namaStudent,
                    'email'    => $email,
                    'password' => $defaultPasswordHash,
                    'role'     => 'alumni',
                ]);

                Student::create([
                    'user_id'      => $user->id,
                    'prodi_id'     => $prodiMap[$kodeProdi],
                    'nim'          => $nim,
                    'nama_student' => $namaStudent,
                    'angkatan'     => (int) $angkatan,
                    'status'       => $status,
                ]);

                $imported++;
            }
        });

        $msg        = "$imported data alumni berhasil diimpor.";
        $sessionKey = 'success';
        if ($skipped > 0) {
            $msg       .= " $skipped baris dilewati.";
            $sessionKey = 'import_warning';
        }

        return back()
            ->with($sessionKey, $msg)
            ->with('import_errors', $errors);
    }
}
