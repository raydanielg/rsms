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
        Schema::table('emas_candidates', function (Blueprint $table) {
            // Make fields nullable that aren't always required
            $table->string('last_name')->nullable()->change();
            $table->date('date_of_birth')->nullable()->change();
            $table->string('phone')->nullable()->change();
            $table->string('guardian_name')->nullable()->change();
            $table->string('guardian_phone')->nullable()->change();
            $table->text('address')->nullable()->change();
            $table->string('district')->nullable()->change();
            $table->string('region')->nullable()->change();
            $table->string('class_form')->nullable()->change();
            $table->string('level')->nullable()->change();
            
            // Change gender enum to accept M/F
            $table->enum('gender', ['M', 'F', 'male', 'female'])->change();
            
            // Change status to accept 'inactive' as well
            $table->enum('status', ['active', 'inactive', 'pending', 'suspended'])->default('active')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('emas_candidates', function (Blueprint $table) {
            // Revert changes
            $table->string('last_name')->nullable(false)->change();
            $table->date('date_of_birth')->nullable(false)->change();
            $table->string('phone')->nullable(false)->change();
            $table->string('guardian_name')->nullable(false)->change();
            $table->string('guardian_phone')->nullable(false)->change();
            $table->text('address')->nullable(false)->change();
            $table->string('district')->nullable(false)->change();
            $table->string('region')->nullable(false)->change();
            $table->string('class_form')->nullable(false)->change();
            $table->enum('level', ['primary', 'secondary', 'advanced'])->change();
            $table->enum('gender', ['male', 'female'])->change();
            $table->enum('status', ['active', 'pending', 'suspended'])->default('active')->change();
        });
    }
};
