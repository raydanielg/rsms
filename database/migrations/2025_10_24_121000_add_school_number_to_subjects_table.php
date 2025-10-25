<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            if (!Schema::hasColumn('subjects', 'school_number')) {
                $table->string('school_number')->nullable()->after('id');
            }
            // Drop single unique on code if exists then add composite
            try { $table->dropUnique('subjects_code_unique'); } catch (\Throwable $e) {}
            $table->unique(['school_number','code'], 'subjects_school_number_code_unique');
        });
    }

    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            try { $table->dropUnique('subjects_school_number_code_unique'); } catch (\Throwable $e) {}
            try { $table->unique('code'); } catch (\Throwable $e) {}
            if (Schema::hasColumn('subjects', 'school_number')) {
                $table->dropColumn('school_number');
            }
        });
    }
};
