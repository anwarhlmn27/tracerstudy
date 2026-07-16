<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questionnaire_forms', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->enum('target_role', ['alumni', 'atasan']);
            $table->string('angkatan')->nullable()->comment('Null jika form berlaku untuk semua angkatan');
            $table->string('form_group')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('form_questions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('form_id')->constrained('questionnaire_forms')->onDelete('cascade');
            $table->text('question_text');
            $table->text('question_description')->nullable()->comment('Deskripsi/penjelasan tambahan untuk pertanyaan (opsional)');
            $table->string('db_source')->nullable()->comment('Sumber data database untuk tipe select_db');
            $table->string('question_type')->comment('text, textarea, radio, checkbox, select');
            $table->boolean('is_required')->default(true);
            $table->boolean('has_others')->default(false);
            $table->integer('sort_order')->default(0);
            $table->integer('section_id')->default(1)->comment('Section tempat pertanyaan berada');
            $table->string('section_title')->nullable()->comment('Judul section jika merupakan awal dari section baru');
            $table->timestamps();
        });

        Schema::create('form_question_options', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('question_id')->constrained('form_questions')->onDelete('cascade');
            $table->string('option_text');
            $table->integer('sort_order')->default(0);
            $table->integer('go_to_section')->nullable()->comment('ID section tujuan untuk branching (null untuk sekuensial, -1 untuk submit)');
            $table->timestamps();
        });

        Schema::create('form_responses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('form_id')->constrained('questionnaire_forms')->onDelete('cascade');
            $table->foreignUuid('user_id')->nullable()->constrained('users')->nullOnDelete()->comment('User pengisi form, nullable untuk guest submission');
            $table->foreignUuid('evaluated_student_id')->nullable()->constrained('students')->onDelete('cascade')->comment('Diisi jika target_role adalah atasan');
            $table->string('guest_student_id')->nullable()->comment('Track student yang mengisi sebagai guest');
            $table->timestamps();
        });

        Schema::create('form_response_answers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('response_id')->constrained('form_responses')->onDelete('cascade');
            $table->foreignUuid('question_id')->constrained('form_questions')->onDelete('cascade');
            $table->text('answer_text')->nullable(); // Menampung teks jawaban atau ID dari pilihan opsi
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('form_response_answers');
        Schema::dropIfExists('form_responses');
        Schema::dropIfExists('form_question_options');
        Schema::dropIfExists('form_questions');
        Schema::dropIfExists('questionnaire_forms');
    }
};
