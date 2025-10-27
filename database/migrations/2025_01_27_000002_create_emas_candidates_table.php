<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emas_candidates', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->date('date_of_birth');
            $table->enum('gender', ['male', 'female']);
            $table->string('registration_number')->unique();
            $table->string('exam_center_code');
            $table->enum('level', ['primary', 'secondary', 'advanced']);
            $table->string('class_form');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('guardian_name');
            $table->string('guardian_phone');
            $table->text('address');
            $table->string('district');
            $table->string('region');
            $table->string('special_needs')->nullable();
            $table->string('photo')->nullable();
            $table->enum('status', ['active', 'pending', 'suspended'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emas_candidates');
    }
};
