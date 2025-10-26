<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            $students = DB::table('students')
                ->orderBy('id')
                ->get(['id', 'reg_no', 'exam_no']);
            $counters = [];
            foreach ($students as $student) {
                $prefix = null;
                if (!empty($student->exam_no) && preg_match('/^([A-Za-z0-9]+)-/', $student->exam_no, $m)) {
                    $prefix = strtoupper($m[1]);
                } elseif (!empty($student->reg_no) && preg_match('/^([A-Za-z0-9]+)-/', $student->reg_no, $m)) {
                    $prefix = strtoupper($m[1]);
                }
                if (!$prefix) {
                    $prefix = 'S0000';
                }
                $counters[$prefix] = ($counters[$prefix] ?? 0) + 1;
                $suffix = str_pad((string) $counters[$prefix], 4, '0', STR_PAD_LEFT);
                $newReg = $prefix . '-' . $suffix;
                if ($newReg !== $student->reg_no) {
                    DB::table('students')->where('id', $student->id)->update(['reg_no' => $newReg]);
                }
            }
        });
    }

    public function down(): void
    {
        // Cannot reliably restore previous registration numbers.
    }
};
