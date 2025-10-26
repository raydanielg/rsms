<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Subject;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $schoolNumber = optional($request->user())->school_number;
        $year = (int) ($request->query('year') ?: date('Y'));

        // Counts scoped to school
        $studentsCount = Student::query()
            ->when($schoolNumber, fn($q) => $q->where('exam_no', 'like', $schoolNumber.'-%'))
            ->count();
        $teachersCount = Teacher::query()
            ->when($schoolNumber, fn($q) => $q->where('school_number', $schoolNumber))
            ->count();
        $subjectsCount = Subject::query()
            ->when($schoolNumber, fn($q) => $q->where('school_number', $schoolNumber))
            ->count();
        $classesCount = SchoolClass::query()
            ->when($schoolNumber, fn($q) => $q->where('school_number', $schoolNumber))
            ->count();
        // Parents proxy: distinct guardians by phone for this school
        $parentsCount = Student::query()
            ->when($schoolNumber, fn($q) => $q->where('exam_no', 'like', $schoolNumber.'-%'))
            ->whereNotNull('guardian_phone')
            ->distinct('guardian_phone')
            ->count('guardian_phone');

        // Years list from exams
        $years = Exam::query()
            ->select('year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        // Results summary for selected year and current school
        $studentIds = Student::query()
            ->when($schoolNumber, fn($q) => $q->where('exam_no', 'like', $schoolNumber.'-%'))
            ->pluck('id');
        $totalMarks = 0; $passMarks = 0;
        $teacherStats = [];
        $subjectStats = [];
        $genderStats = ['M' => ['total' => 0, 'pass' => 0], 'F' => ['total' => 0, 'pass' => 0]];
        $classGender = [];
        if ($studentIds->isNotEmpty()) {
            $totalMarks = DB::table('exam_marks as em')
                ->join('exams as e', 'e.id', '=', 'em.exam_id')
                ->where('e.year', $year)
                ->whereIn('em.student_id', $studentIds)
                ->count();
            $passMarks = DB::table('exam_marks as em')
                ->join('exams as e', 'e.id', '=', 'em.exam_id')
                ->where('e.year', $year)
                ->whereIn('em.student_id', $studentIds)
                ->where('em.marks', '>=', 40)
                ->count();

            // Teacher analytics: aggregate marks for teacher's subjects
            $teacherRows = DB::table('exam_marks as em')
                ->join('exams as e', 'e.id', '=', 'em.exam_id')
                ->join('students as s', 's.id', '=', 'em.student_id')
                ->join('subject_teacher as st', 'st.subject_id', '=', 'em.subject_id')
                ->join('teachers as t', 't.id', '=', 'st.teacher_id')
                ->where('e.year', $year)
                ->when($schoolNumber, function ($q) use ($schoolNumber, $studentIds) {
                    $q->where('t.school_number', $schoolNumber)
                      ->whereIn('em.student_id', $studentIds);
                })
                ->select('t.id','t.name','em.marks')
                ->get();
            foreach ($teacherRows as $row) {
                $bucket = &$teacherStats[$row->id];
                if (!$bucket) {
                    $bucket = ['id' => $row->id, 'name' => $row->name, 'total' => 0, 'pass' => 0, 'sum' => 0];
                }
                $bucket['total']++;
                $bucket['sum'] += (float)$row->marks;
                if ($row->marks >= 40) $bucket['pass']++;
            }
            $teacherStats = collect($teacherStats)
                ->map(fn($t) => [
                    'id' => $t['id'],
                    'name' => $t['name'],
                    'pass_rate' => $t['total'] ? round(($t['pass'] / $t['total']) * 100, 1) : 0,
                    'avg' => $t['total'] ? round($t['sum'] / $t['total'], 1) : 0,
                ])
                ->sortByDesc('pass_rate')
                ->values()
                ->take(8)
                ->all();

            // Subject analytics
            $subjectRows = DB::table('exam_marks as em')
                ->join('exams as e', 'e.id', '=', 'em.exam_id')
                ->join('subjects as sub', 'sub.id', '=', 'em.subject_id')
                ->join('students as s', 's.id', '=', 'em.student_id')
                ->where('e.year', $year)
                ->whereIn('em.student_id', $studentIds)
                ->when($schoolNumber, fn($q) => $q->where('sub.school_number', $schoolNumber))
                ->select('sub.id','sub.name','em.marks')
                ->get();
            foreach ($subjectRows as $row) {
                $bucket = &$subjectStats[$row->id];
                if (!$bucket) {
                    $bucket = ['id' => $row->id, 'name' => $row->name, 'total' => 0, 'pass' => 0, 'sum' => 0];
                }
                $bucket['total']++;
                $bucket['sum'] += (float)$row->marks;
                if ($row->marks >= 40) $bucket['pass']++;
            }
            $subjectStats = collect($subjectStats)
                ->map(fn($s) => [
                    'id' => $s['id'],
                    'name' => $s['name'],
                    'pass_rate' => $s['total'] ? round(($s['pass'] / $s['total']) * 100, 1) : 0,
                    'avg' => $s['total'] ? round($s['sum'] / $s['total'], 1) : 0,
                ])
                ->sortByDesc('avg')
                ->values()
                ->take(8)
                ->all();

            // Gender analytics
            $genderRows = DB::table('exam_marks as em')
                ->join('exams as e', 'e.id', '=', 'em.exam_id')
                ->join('students as s', 's.id', '=', 'em.student_id')
                ->where('e.year', $year)
                ->whereIn('em.student_id', $studentIds)
                ->select('s.sex','em.marks')
                ->get();
            foreach ($genderRows as $row) {
                $sex = strtoupper($row->sex ?? '');
                if (!isset($genderStats[$sex])) {
                    $genderStats[$sex] = ['total' => 0, 'pass' => 0];
                }
                $genderStats[$sex]['total']++;
                if ($row->marks >= 40) $genderStats[$sex]['pass']++;
            }

            // Overall class gender counts (top 4 classes by total students)
            $classGenderRows = Student::query()
                ->when($schoolNumber, fn($q) => $q->where('exam_no', 'like', $schoolNumber.'-%'))
                ->select('class_name', 'sex', DB::raw('COUNT(*) as total'))
                ->groupBy('class_name', 'sex')
                ->get();
            $classBuckets = [];
            foreach ($classGenderRows as $row) {
                $class = $row->class_name ?: 'Unknown';
                $sex = strtoupper($row->sex ?: 'U');
                if (!isset($classBuckets[$class])) {
                    $classBuckets[$class] = ['class' => $class, 'M' => 0, 'F' => 0, 'total' => 0];
                }
                if (in_array($sex, ['M','F'])) {
                    $classBuckets[$class][$sex] += (int)$row->total;
                    $classBuckets[$class]['total'] += (int)$row->total;
                }
            }
            $classGender = collect($classBuckets)
                ->sortByDesc('total')
                ->values()
                ->take(4)
                ->map(fn($item) => [
                    'class' => $item['class'],
                    'male' => $item['M'],
                    'female' => $item['F'],
                ])
                ->all();

        }
        $passPct = $totalMarks ? round(($passMarks / max(1,$totalMarks))*100) : 0;
        $failPct = 100 - $passPct;
        $genderChart = collect($genderStats)
            ->map(function ($val, $sex) {
                $total = max(1, $val['total']);
                return [
                    'sex' => $sex,
                    'pass_rate' => round(($val['pass'] / $total) * 100, 1),
                ];
            })
            ->values();

        // Recent activity
        $recentStudents = Student::query()
            ->when($schoolNumber, fn($q) => $q->where('exam_no', 'like', $schoolNumber.'-%'))
            ->orderByDesc('id')->limit(3)->get(['id','name','reg_no']);
        $recentTeachers = Teacher::query()
            ->when($schoolNumber, fn($q) => $q->where('school_number', $schoolNumber))
            ->orderByDesc('id')->limit(2)->get(['id','name','check_no']);
        $activities = [];
        foreach ($recentStudents as $s) { $activities[] = [ 'type' => 'student', 'text' => 'New student: '.$s->name, 'meta' => $s->reg_no ]; }
        foreach ($recentTeachers as $t) { $activities[] = [ 'type' => 'teacher', 'text' => 'New teacher: '.$t->name, 'meta' => $t->check_no ]; }

        return Inertia::render('Dashboard', [
            'selected_year' => $year,
            'years' => $years,
            'stats' => [
                'students' => $studentsCount,
                'teachers' => $teachersCount,
                'subjects' => $subjectsCount,
                'classes' => $classesCount,
                'parents' => $parentsCount,
            ],
            'results' => [ 'pass' => $passPct, 'fail' => $failPct, 'total_marks' => $totalMarks ],
            'charts' => [
                'teachers' => $teacherStats,
                'subjects' => $subjectStats,
                'gender' => $genderChart,
                'class_gender' => $classGender,
            ],
            'activities' => $activities,
        ]);
    }
}
