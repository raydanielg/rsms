<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Clean up duplicates before adding the unique index
        // Keep the smallest id per (school_number, name)
        $dupes = DB::table('classes as c')
            ->select('c.school_number', 'c.name', DB::raw('MIN(c.id) as keep_id'), DB::raw('COUNT(*) as cnt'))
            ->groupBy('c.school_number', 'c.name')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($dupes as $d) {
            DB::table('classes')
                ->where('school_number', $d->school_number)
                ->where('name', $d->name)
                ->where('id', '!=', $d->keep_id)
                ->delete();
        }

        try {
            Schema::table('classes', function (Blueprint $table) {
                $table->unique(['school_number', 'name'], 'classes_school_number_name_unique');
            });
        } catch (\Throwable $e) {
            // Ignore if index already exists or driver cannot alter; the cleanup above still helps
        }
    }

    public function down(): void
    {
        try {
            Schema::table('classes', function (Blueprint $table) {
                $table->dropUnique('classes_school_number_name_unique');
            });
        } catch (\Throwable $e) {
            // Ignore if index missing or driver limitation
        }
    }
};
