<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('timetable_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('timetable_id');
            $table->unsignedTinyInteger('day_of_week'); // 0-6
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->unsignedBigInteger('class_id')->nullable();
            $table->unsignedBigInteger('teacher_id')->nullable();
            $table->string('room', 100)->nullable();
            $table->string('note', 255)->nullable();
            $table->timestamps();

            $table->index(['timetable_id','day_of_week']);
            $table->unique(['timetable_id','day_of_week','start_time','end_time','class_id','teacher_id'], 'tt_items_unique_slot');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('timetable_items');
    }
};
