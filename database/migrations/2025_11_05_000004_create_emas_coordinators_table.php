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
        Schema::create('emas_coordinators', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('username')->unique();
            $table->string('password');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('id_number')->nullable();
            $table->text('address')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamp('last_login')->nullable();
            $table->timestamps();

            $table->index('status');
        });

        // Pivot table for many-to-many relationship
        Schema::create('center_coordinator', function (Blueprint $table) {
            $table->id();
            $table->foreignId('center_id')->constrained('emas_centers')->onDelete('cascade');
            $table->foreignId('coordinator_id')->constrained('emas_coordinators')->onDelete('cascade');
            $table->enum('role', ['primary', 'secondary'])->default('primary');
            $table->timestamps();

            $table->unique(['center_id', 'coordinator_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('center_coordinator');
        Schema::dropIfExists('emas_coordinators');
    }
};
