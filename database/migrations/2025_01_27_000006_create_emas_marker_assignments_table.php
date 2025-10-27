<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emas_marker_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('marker_id')->constrained('emas_markers')->onDelete('cascade');
            $table->foreignId('exam_id')->constrained('emas_exams')->onDelete('cascade');
            $table->integer('papers_assigned')->default(0);
            $table->integer('papers_marked')->default(0);
            $table->integer('papers_pending')->default(0);
            $table->enum('status', ['assigned', 'in_progress', 'completed'])->default('assigned');
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['marker_id', 'exam_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emas_marker_assignments');
    }
};
