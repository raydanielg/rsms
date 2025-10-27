<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emas_markers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('marker_code')->unique();
            $table->json('subjects'); // Array of subjects they can mark
            $table->string('district')->nullable(); // Specific district or null for all
            $table->string('region'); // Region they belong to
            $table->enum('scope', ['district', 'region'])->default('district'); // Marking scope
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->integer('exams_marked')->default(0);
            $table->integer('papers_marked')->default(0);
            $table->text('qualifications')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emas_markers');
    }
};
