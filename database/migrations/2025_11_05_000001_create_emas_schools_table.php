<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('emas_schools', function (Blueprint $table) {
            $table->id();
            $table->string('centre_number')->unique();
            $table->string('centre_name');
            $table->string('region');
            $table->string('council');
            $table->string('ward')->nullable();
            $table->enum('ownership', ['GOVERNMENT', 'PRIVATE'])->default('GOVERNMENT');
            $table->integer('female_students')->default(0);
            $table->integer('male_students')->default(0);
            $table->integer('total_students')->default(0);
            $table->string('contact_person')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->index(['region', 'council']);
            $table->index('ownership');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emas_schools');
    }
};
