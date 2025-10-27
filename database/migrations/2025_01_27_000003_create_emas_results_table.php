<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emas_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('emas_exams')->onDelete('cascade');
            $table->foreignId('candidate_id')->constrained('emas_candidates')->onDelete('cascade');
            $table->integer('score');
            $table->string('grade', 2);
            $table->enum('remarks', ['Excellent', 'Very Good', 'Good', 'Satisfactory', 'Fail'])->nullable();
            $table->text('comments')->nullable();
            $table->foreignId('uploaded_by')->constrained('emas_users')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['exam_id', 'candidate_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emas_results');
    }
};
