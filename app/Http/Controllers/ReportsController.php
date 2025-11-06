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

    public function teachers(Request $request)
    {
        $total = (int) DB::table('teachers')->count();
        $male = (int) DB::table('teachers')->where('sex', 'M')->count();
        $female = (int) DB::table('teachers')->where('sex', 'F')->count();
        
        // Teachers by subjects taught
        $bySubject = DB::table('subject_teacher as st')
            ->join('teachers as t', 't.id', '=', 'st.teacher_id')
            ->join('subjects as s', 's.id', '=', 'st.subject_id')
            ->select('s.name as subject', DB::raw('COUNT(DISTINCT t.id) as count'))
            ->groupBy('s.name')
            ->orderBy('count', 'desc')
            ->get();

        // Teachers by class assigned
        $byClass = DB::table('class_teacher as ct')
            ->join('teachers as t', 't.id', '=', 'ct.teacher_id')
            ->join('classes as c', 'c.id', '=', 'ct.class_id')
            ->select('c.name as class_name', 't.name as teacher_name', 't.phone')
            ->orderBy('c.name')
            ->get();

        return response()->json([
            'total' => $total,
            'male' => $male,
            'female' => $female,
            'by_subject' => $bySubject,
            'by_class' => $byClass,
        ]);
    }

    public function topStudents(Request $request)
    {
        $examId = $request->input('exam_id');
        $className = $request->input('class_name');
        $subjectId = $request->input('subject_id');
        $limit = (int) $request->input('limit', 10);

        $query = DB::table('exam_marks as em')
            ->join('students as s', 's.id', '=', 'em.student_id')
            ->select('s.id', 's.name', 's.class_name', 
                    DB::raw('AVG(em.marks) as avg_marks'),
                    DB::raw('COUNT(em.id) as subjects_count'))
            ->groupBy('s.id', 's.name', 's.class_name');

        if ($examId) {
            $query->where('em.exam_id', $examId);
        }
        if ($className) {
            $query->where('em.class_name', $className);
        }
        if ($subjectId) {
            $query->where('em.subject_id', $subjectId);
        }

        $topStudents = $query->orderBy('avg_marks', 'desc')
            ->limit($limit)
            ->get();

        return response()->json(['students' => $topStudents]);
    }

    public function subjectRankings(Request $request)
    {
        $examId = $request->input('exam_id');
        $className = $request->input('class_name');

        $query = DB::table('exam_marks as em')
            ->join('subjects as s', 's.id', '=', 'em.subject_id')
            ->select('s.id', 's.name',
                    DB::raw('AVG(em.marks) as avg_marks'),
                    DB::raw('MAX(em.marks) as max_marks'),
                    DB::raw('MIN(em.marks) as min_marks'),
                    DB::raw('COUNT(em.id) as students_count'))
            ->groupBy('s.id', 's.name');

        if ($examId) {
            $query->where('em.exam_id', $examId);
        }
        if ($className) {
            $query->where('em.class_name', $className);
        }

        $rankings = $query->orderBy('avg_marks', 'desc')->get();

        return response()->json(['subjects' => $rankings]);
    }

    public function school(Request $request)
    {
        $students = (int) DB::table('students')->count();
        $classes = (int) DB::table('classes')->count();
        $subjects = (int) DB::table('subjects')->count();
        $exams = (int) DB::table('exams')->count();
        $teachers = (int) DB::table('teachers')->count();
        $marks = DB::table('exam_marks')->count();
        $subjectsPerClass = DB::table('class_subject')
            ->select('school_class_id', DB::raw('COUNT(*) as subjects'))
            ->groupBy('school_class_id')
            ->get();
        $avgSubjectsPerClass = $subjectsPerClass->count() ? round($subjectsPerClass->avg('subjects'), 1) : 0;

        // Overall performance
        $avgPerformance = DB::table('exam_marks')
            ->avg('marks');

        // Pass rate (assuming 50% is pass)
        $totalMarks = DB::table('exam_marks')->whereNotNull('marks')->count();
        $passedMarks = DB::table('exam_marks')->where('marks', '>=', 50)->count();
        $passRate = $totalMarks > 0 ? round(($passedMarks / $totalMarks) * 100, 2) : 0;

        return response()->json([
            'students' => $students,
            'classes' => $classes,
            'subjects' => $subjects,
            'exams' => $exams,
            'teachers' => $teachers,
            'marks' => $marks,
            'avg_subjects_per_class' => $avgSubjectsPerClass,
            'avg_performance' => round($avgPerformance, 2),
            'pass_rate' => $passRate,
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

    public function filters(Request $request)
    {
        $exams = DB::table('exams')
            ->select('id', 'name')
            ->orderBy('created_at', 'desc')
            ->get();

        $classes = DB::table('classes')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        $subjects = DB::table('subjects')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return response()->json([
            'exams' => $exams,
            'classes' => $classes,
            'subjects' => $subjects,
        ]);
    }
}
