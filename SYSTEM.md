# Tracers - System & Project Flow Documentation

## 1. Overview
Tracers adalah aplikasi berbasis web yang dibangun menggunakan **Laravel 12** dan **Tailwind CSS v4** (via Vite). Sistem ini dirancang untuk memfasilitasi "Tracer Study" atau pelacakan alumni, yang memungkinkan institusi/perguruan tinggi untuk mengumpulkan data dari alumni dan pihak terkait (seperti atasan alumni) melalui kuesioner yang dinamis.

## 2. Arsitektur & Teknologi
- **Backend:** Laravel 12 (PHP ^8.2)
- **Frontend:** Blade Templates dengan Tailwind CSS v4, dikompilasi menggunakan Vite.
- **Database:** Relasional (dikonfigurasi via `.env`, mendukung MySQL/PostgreSQL/SQLite).
- **Ekspor Data:** Menggunakan package `shuchkin/simplexlsxgen` untuk mengekspor laporan ke format Excel.

## 3. Role Pengguna & Hak Akses
Sistem memiliki beberapa peran pengguna (`role`) yang diatur pada model `User`:
1. **Admin:** Memiliki akses penuh ke seluruh sistem, termasuk dashboard, manajemen data alumni/mahasiswa, master program studi (prodi), laporan, dan secara eksklusif dapat mengelola **Master Form** (kuesioner dinamis).
2. **Dosen:** Memiliki akses ke dashboard, melihat data alumni, master prodi, kuesioner, dan laporan. (Akses terbatas untuk membaca atau mengubah data non-sistem).
3. **Alumni (Student):** Target utama pengisian form tracer study. Saat login, sistem akan mengecek kelengkapan profil/form mereka.
4. **Atasan:** Pihak pemberi kerja alumni yang juga menjadi target kuesioner untuk memberikan penilaian terhadap kinerja alumni di dunia kerja.

## 4. Alur Kerja (Project Flow)

### A. Autentikasi & Routing
- Entry point aplikasi dimulai dari `AuthController` (`/` untuk form login, `/logout` untuk keluar).
- Terdapat **middleware** berbasis *role* dan pengecekan kelengkapan profil (`incomplete_profile`) untuk mengarahkan rute pengguna.
- Jika Alumni atau Atasan belum mengisi form wajib, mereka secara otomatis akan di-redirect ke halaman pengisian form (`/form`).

### B. Dynamic Questionnaire (Master Form)
Sistem ini menggunakan struktur form yang dinamis (bukan hardcoded di HTML):
- **Admin** masuk ke menu **Master Form** (`MasterFormController`).
- Admin membuat **QuestionnaireForm** baru dan menentukan `target_role` (siapa yang akan mengisi, misal: khusus alumni atau khusus atasan).
- Di dalam form, admin menambahkan **FormQuestion** (pertanyaan).
- Jika tipe pertanyaan adalah pilihan (radio/checkbox/dropdown), admin dapat menambahkan **FormQuestionOption**.

### C. Pengisian Tracer Study oleh Alumni/Atasan
1. **Login:** Alumni/Atasan masuk ke sistem.
2. **Validasi:** Middleware mendeteksi apakah mereka sudah melengkapi profil/menjawab kuesioner aktif.
3. **Pengisian Form:** User diarahkan ke form yang di-generate otomatis dari data Master Form.
4. **Penyimpanan:** Jawaban disimpan ke dalam tabel `FormResponse` dan dipecah detailnya ke `FormResponseAnswer`. Data pelengkap (seperti informasi perusahaan tempat alumni bekerja, masa tunggu kerja) diperbarui di model `Student`.

### D. Pengelolaan Data Utama (Master Data)
- **Data Prodi:** Dikelola oleh Admin/Dosen melalui `ProdiController`. Berelasi dengan entitas `Student`.
- **Data Alumni:** Dikelola melalui `AlumniController`. Berisi informasi akademik (NIM, Angkatan) dan status karir alumni.

### E. Laporan & Ekspor (Reporting)
- **Dashboard:** Menampilkan metrik utama secara real-time seperti Total Alumni, Response Rate (tingkat partisipasi pengisian form), jumlah form aktif, dan respons terbaru dari alumni.
- **Report & Questionnaires:** `ReportController` dan `QuestionnaireController` menyajikan rangkuman hasil dari jawaban kuesioner.
- **Ekspor Data:** Terdapat fitur ekspor (`/questionnaires/export`) yang menghasilkan file Excel (format .xlsx) untuk kebutuhan pelaporan institusi menggunakan `simplexlsxgen`.

## 5. Struktur Database Utama (Core Models)
Sistem menggunakan UUID (`HasUuids`) untuk primary key pada kebanyakan modelnya.
- `User`: Akun autentikasi pengguna.
- `Student`: Profil spesifik mahasiswa/alumni (berelasi `hasOne` dari `User` dan `belongsTo` `Prodi`).
- `Prodi`: Master data Program Studi.
- `QuestionnaireForm`: Form kuesioner utama.
- `FormQuestion`: Pertanyaan-pertanyaan di dalam form.
- `FormQuestionOption`: Opsi jawaban untuk tipe soal pilihan ganda.
- `FormResponse`: Header/Transaksi respons pengisian form oleh user.
- `FormResponseAnswer`: Detail jawaban spesifik untuk setiap `FormQuestion`.

## 6. Entry Points File Kode
Jika ingin melakukan modifikasi pada sistem, berikut adalah file-file utamanya:
- **Routes:** `routes/web.php`
- **Controllers:** Berada di `app/Http/Controllers/` (khususnya `AlumniController`, `MasterFormController`, `TracerResponseController`).
- **Models:** Berada di `app/Models/` (khususnya `Student`, `QuestionnaireForm`, `FormResponse`).
- **Views (Frontend):** Berada di `resources/views/` (Terdapat folder per fitur seperti `master-form/`, `alumni/`, `prodi/`, dan `dashboard.blade.php`).
