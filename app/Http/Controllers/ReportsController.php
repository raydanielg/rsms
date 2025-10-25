<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public function students(Request $request)
    {
        $total = (int) DB::table('students')->count();
        $male = (int) DB::table('students')->where('sex','M')->count();
        $female = (int) DB::table('students')->where('sex','F')->count();
        $byClass = DB::table('students')
            ->select('class_name', DB::raw('COUNT(*) as count'))
            ->groupBy('class_name')
            ->orderBy('class_name')
            ->get();
        return response()->json([
            'total' => $total,
            'male' => $male,
            'female' => $female,
            'by_class' => $byClass,
        ]);
    }

    public function school(Request $request)
    {
        $students = (int) DB::table('students')->count();
        $classes = (int) DB::table('classes')->count();
        $subjects = (int) DB::table('subjects')->count();
        $exams = (int) DB::table('exams')->count();
        $marks = DB::table('exam_marks')->count();
        $subjectsPerClass = DB::table('class_subject')
            ->select('school_class_id', DB::raw('COUNT(*) as subjects'))
            ->groupBy('school_class_id')
            ->get();
        $avgSubjectsPerClass = $subjectsPerClass->count() ? round($subjectsPerClass->avg('subjects'), 1) : 0;
        return response()->json([
            'students' => $students,
            'classes' => $classes,
            'subjects' => $subjects,
            'exams' => $exams,
            'marks' => $marks,
            'avg_subjects_per_class' => $avgSubjectsPerClass,
        ]);
    }

    public function classesCoverage(Request $request)
    {
        $rows = DB::table('classes as c')
            ->leftJoin('class_subject as cs', 'cs.school_class_id', '=', 'c.id')
            ->select('c.id','c.name', DB::raw('COUNT(cs.subject_id) as subjects'))
            ->groupBy('c.id','c.name')
            ->orderBy('c.name')
            ->get();
        return response()->json(['classes' => $rows]);
    }
}
