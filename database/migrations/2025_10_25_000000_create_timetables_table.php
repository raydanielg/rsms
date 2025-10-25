<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('timetables', function (Blueprint $table) {
            $table->id();
            $table->string('type', 20); // class, exam, supervision, teacher
            $table->string('name');
            $table->date('effective_date')->nullable();
            $table->string('scope_type', 20)->nullable(); // class, teacher, school
            $table->unsignedBigInteger('scope_id')->nullable();
            $table->string('scope_name')->nullable();
            $table->unsignedBigInteger('exam_id')->nullable();
            $table->string('status', 20)->default('draft');
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['type','status']);
            $table->index(['name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('timetables');
    }
};
