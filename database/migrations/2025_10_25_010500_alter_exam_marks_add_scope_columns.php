<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('exam_marks', function (Blueprint $table) {
            $table->string('school_number')->nullable()->after('class_name');
            $table->unsignedSmallInteger('year')->nullable()->after('school_number');
            $table->foreignId('created_by')->nullable()->after('year')->constrained('users')->nullOnDelete();
            $table->index(['exam_id','school_number']);
            $table->index(['exam_id','year']);
        });
    }

    public function down(): void
    {
        Schema::table('exam_marks', function (Blueprint $table) {
            $table->dropIndex(['exam_marks_exam_id_school_number_index']);
            $table->dropIndex(['exam_marks_exam_id_year_index']);
            $table->dropConstrainedForeignId('created_by');
            $table->dropColumn(['school_number','year']);
        });
    }
};
