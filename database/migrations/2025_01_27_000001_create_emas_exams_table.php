<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emas_exams', function (Blueprint $table) {
            $table->id();
            $table->string('exam_name');
            $table->string('exam_code')->unique();
            $table->string('subject');
            $table->enum('exam_type', ['written', 'practical', 'oral', 'project'])->default('written');
            $table->enum('level', ['primary', 'secondary', 'advanced']);
            $table->string('class_form');
            $table->date('exam_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('duration'); // in minutes
            $table->integer('total_marks');
            $table->integer('pass_marks');
            $table->text('description')->nullable();
            $table->text('instructions')->nullable();
            $table->enum('status', ['scheduled', 'ongoing', 'completed', 'cancelled'])->default('scheduled');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emas_exams');
    }
};
