<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            // Per-school isolation using school_number for now (string). Later, migrate to school_id FK.
            $table->string('school_number')->index();
            $table->string('name');
            $table->string('check_no', 50)->index();
            $table->string('phone', 30);
            $table->string('email')->nullable();
            $table->enum('sex', ['M','F']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
