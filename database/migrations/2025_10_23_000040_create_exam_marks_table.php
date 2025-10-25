<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('exam_marks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->string('class_name');
            $table->decimal('marks', 5, 2)->nullable();
            $table->string('grade', 5)->nullable();
            $table->timestamps();
            $table->unique(['exam_id','student_id','subject_id']);
            $table->index(['exam_id','class_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_marks');
    }
};
