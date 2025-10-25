<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['midterm','terminal','mock','trial','custom']);
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('term')->default(1); // 1-3
            $table->timestamp('published_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['year','term','type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
