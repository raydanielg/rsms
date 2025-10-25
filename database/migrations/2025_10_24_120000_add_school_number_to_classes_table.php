<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            if (!Schema::hasColumn('classes', 'school_number')) {
                $table->string('school_number')->nullable()->after('id');
            }
        });

        Schema::table('classes', function (Blueprint $table) {
            // Drop existing unique on name if it exists
            try {
                $table->dropUnique('classes_name_unique');
            } catch (\Throwable $e) {
                // ignore if index does not exist
            }

            // Add composite unique to allow same class names per school
            $table->unique(['school_number', 'name'], 'classes_school_number_name_unique');
        });
    }

    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            // Drop composite unique
            try { $table->dropUnique('classes_school_number_name_unique'); } catch (\Throwable $e) {}
            // Restore unique on name
            try { $table->unique('name'); } catch (\Throwable $e) {}
        });

        Schema::table('classes', function (Blueprint $table) {
            if (Schema::hasColumn('classes', 'school_number')) {
                $table->dropColumn('school_number');
            }
        });
    }
};
