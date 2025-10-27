<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emas_centers', function (Blueprint $table) {
            $table->id();
            $table->string('center_name');
            $table->string('center_code')->unique();
            $table->text('address');
            $table->string('district');
            $table->string('region');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->integer('capacity');
            $table->string('coordinator_name')->nullable();
            $table->string('coordinator_phone')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emas_centers');
    }
};
