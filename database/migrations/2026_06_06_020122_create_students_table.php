<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
    Schema::create('students', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
        $table->foreignUuid('prodi_id')->constrained('prodis')->onDelete('cascade');
        $table->string('nim')->unique();
        $table->string('nama_student');
        $table->integer('angkatan');
        $table->enum('status', ['aktif', 'lulus', 'cuti', 'drop_out'])->default('aktif');
        $table->string('status_alumni')->nullable()->comment('Bekerja (full time / part time), Belum memungkinkan bekerja, Wiraswasta, Melanjutkan Pendidikan, Tidak kerja tetapi sedang mencari kerja');
        $table->string('nama_perusahaan')->nullable();
        $table->string('jabatan')->nullable();
        $table->string('tempat_kerja')->nullable()->comment('Lokal, Nasional, Multinasional');
        $table->integer('response_rate')->nullable();
        $table->string('waktu_tunggu_kerja')->nullable();
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
