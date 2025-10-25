<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->date('dob')->nullable()->after('class_name');
            $table->string('photo_path')->nullable()->after('dob');
            $table->string('guardian_name')->nullable()->after('photo_path');
            $table->string('guardian_phone')->nullable()->after('guardian_name');
            $table->string('guardian_relation')->nullable()->after('guardian_phone');
            $table->string('exam_no')->unique()->nullable()->after('guardian_relation');
            $table->year('academic_year')->nullable()->after('exam_no');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['dob','photo_path','guardian_name','guardian_phone','guardian_relation','exam_no','academic_year']);
        });
    }
};
