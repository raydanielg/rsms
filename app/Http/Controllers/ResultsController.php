<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\SchoolClass;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class ResultsController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $type = $request->query('type');
        $year = $request->query('year');
        $term = $request->query('term');

        $query = Exam::query()->withCount('classes');
        if ($q !== '') {
            $query->where('name', 'like', "%$q%");
        }
        if ($type) { $query->where('type', $type); }
        if ($year) { $query->where('year', (int)$year); }
        if ($term) { $query->where('term', (int)$term); }

        $exams = $query->orderByDesc('year')->orderByDesc('term')->orderByDesc('id')->paginate(10)->withQueryString();
        $schoolNumber = optional($request->user())->school_number;
        $classes = SchoolClass::query()
            ->when($schoolNumber, function($w) use ($schoolNumber) {
                $w->whereExists(function($sq) use ($schoolNumber) {
                    $sq->select(DB::raw(1))
                        ->from('students as s')
                        ->whereColumn('s.class_name', 'classes.name')
                        ->where('s.exam_no', 'like', $schoolNumber.'-%');
                });
            })
            ->orderBy('name')->get(['id','name']);

        return Inertia::render('Results/Index', [
            'exams' => $exams,
            'filters' => [ 'q' => $q, 'type' => $type, 'year' => $year, 'term' => $term ],
            'classes' => $classes,
            'types' => ['midterm','terminal','mock','trial','custom'],
            'current_year' => (int)date('Y'),
        ]);
    }

    public function storeExam(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'type' => ['required','in:midterm,terminal,mock,trial,custom'],
            'year' => ['required','integer','min:2000','max:2100'],
            'term' => ['required','integer','min:1','max:3'],
            'class_ids' => ['array'],
            'class_ids.*' => ['integer','exists:classes,id'],
        ]);

        DB::transaction(function () use ($data, $request) {
            $exam = Exam::create([
                'name' => $data['name'],
                'type' => $data['type'],
                'year' => (int)$data['year'],
                'term' => (int)$data['term'],
                'created_by' => optional($request->user())->id,
            ]);
            $exam->classes()->sync($data['class_ids'] ?? []);
        });

        return Redirect::back()->with('success', 'Exam created');
    }

    public function show(int $id)
    {
        $exam = Exam::with('classes:id,name')->findOrFail($id);
        $schoolNumber = optional(request()->user())->school_number;
        $allClasses = SchoolClass::query()
            ->when($schoolNumber, function($w) use ($schoolNumber) {
                $w->whereExists(function($sq) use ($schoolNumber) {
                    $sq->select(DB::raw(1))
                        ->from('students as s')
                        ->whereColumn('s.class_name', 'classes.name')
                        ->where('s.exam_no', 'like', $schoolNumber.'-%');
                });
            })
            ->orderBy('name')->get(['id','name']);
        // gather students for assigned classes (by name match)
        $students = [];
        $classNames = $exam->classes->pluck('name')->all();
        if (!empty($classNames)) {
            $students = \App\Models\Student::query()
                ->whereIn('class_name', $classNames)
                ->orderBy('class_name')->orderBy('name')
                ->get(['id','reg_no','name','class_name','sex']);
        }
        // Scope exam's own classes for chips
        $examClassIds = $exam->classes->pluck('id');
        $scopedExamClasses = SchoolClass::query()
            ->whereIn('id', $examClassIds)
            ->when($schoolNumber, function($w) use ($schoolNumber) {
                $w->whereExists(function($sq) use ($schoolNumber) {
                    $sq->select(DB::raw(1))
                        ->from('students as s')
                        ->whereColumn('s.class_name', 'classes.name')
                        ->where('s.exam_no', 'like', $schoolNumber.'-%');
                });
            })
            ->orderBy('name')->get(['id','name']);

        return Inertia::render('Results/Show', [
            'exam' => [
                'id' => $exam->id,
                'name' => $exam->name,
                'type' => $exam->type,
                'year' => $exam->year,
                'term' => $exam->term,
                'published_at' => $exam->published_at,
                'classes' => $scopedExamClasses->map->only(['id','name']),
            ],
            'classes' => $allClasses,
            'types' => ['midterm','terminal','mock','trial','custom'],
            'students' => $students,
        ]);
    }

    public function showJson(Request $request)
    {
        $id = (int) $request->query('id', 0);
        $exam = Exam::with('classes:id')->findOrFail($id);
        return response()->json([
            'id' => $exam->id,
            'name' => $exam->name,
            'type' => $exam->type,
            'year' => $exam->year,
            'term' => $exam->term,
            'class_ids' => $exam->classes->pluck('id')->all(),
        ]);
    }

    public function update(Request $request, int $id)
    {
        $exam = Exam::findOrFail($id);
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'type' => ['required','in:midterm,terminal,mock,trial,custom'],
            'year' => ['required','integer','min:2000','max:2100'],
            'term' => ['required','integer','min:1','max:3'],
            'class_ids' => ['array'],
            'class_ids.*' => ['integer','exists:classes,id'],
        ]);
        DB::transaction(function() use ($exam, $data) {
            $exam->update([
                'name' => $data['name'],
                'type' => $data['type'],
                'year' => (int)$data['year'],
                'term' => (int)$data['term'],
            ]);
            $exam->classes()->sync($data['class_ids'] ?? []);
        });
        return Redirect::back()->with('success', 'Exam updated');
    }

    public function destroy(int $id)
    {
        $exam = Exam::findOrFail($id);
        $exam->delete();
        return Redirect::route('results.index')->with('success', 'Exam deleted');
    }

        public function getMarks(Request $request, int $examId)
    {
        try {
        $classId = (int) $request->query('class_id', 0);
        $className = $request->query('class_name');
        $exam = Exam::findOrFail($examId);
        $schoolNumber = optional($request->user())->school_number;

        $cls = null;
        if ($classId) {
            $cls = SchoolClass::query()
                ->where('id', $classId)
                ->when($schoolNumber, fn($q) => $q->where('school_number', $schoolNumber))
                ->first();
            // Fallback for legacy classes missing school_number
            if (!$cls) {
                $cls = SchoolClass::find($classId);
            }
        } elseif ($className) {
            $cls = SchoolClass::query()
                ->where('name', $className)
                ->when($schoolNumber, fn($q) => $q->where('school_number', $schoolNumber))
                ->first();
            if (!$cls) {
                $cls = SchoolClass::where('name', $className)->first();
            }
        }
        if (!$cls) return response()->json(['subjects' => [], 'students' => [], 'marks' => (object)[]]);

        // Get all unique subjects assigned to the students in this class
        $subjectIds = DB::table('class_subject')
            ->join('classes', 'classes.id', '=', 'class_subject.school_class_id')
            ->where('classes.name', $cls->name)
            ->pluck('subject_id');

        $subjectsList = Subject::whereIn('id', $subjectIds)
            ->when($schoolNumber, fn($q) => $q->where('school_number', $schoolNumber))
            ->get(['id','name','code']);

        // Custom subject order
        $customOrder = ['CIVICS', 'HISTORY', 'GEOGRAPHY', 'KISWAHILI', 'ENGLISH LANGUAGE', 'ENGLISH', 'PHYSICS', 'CHEMISTRY', 'BIOLOGY', 'BASIC MATHEMATICS', 'BASIC MATH', 'MATHEMATICS'];
        $weights = array_flip(array_map('strtoupper', $customOrder));
        $subjects = collect($subjectsList)->sortBy(function($s) use ($weights) {
            $name = strtoupper(trim((string)($s->name ?? '')));
            return $weights[$name] ?? 1000;
        })->values();

        $assignedSubjectsCount = $subjects->count();
        $fallbackSubjects = false;
        // students in resolved class, scoped by school_number if available
        $studentsQuery = \App\Models\Student::query()
            ->where('class_name', $cls->name)
            ->when($schoolNumber, function($w) use ($schoolNumber) {
                $w->where('exam_no', 'like', $schoolNumber.'-%');
            })
            ->orderBy('name');
        $students = $studentsQuery->get(['id','reg_no','name','class_name','sex']);
        // marks map (store class_name for historical congruence)
        $marks = \DB::table('exam_marks')
            ->where('exam_id', $exam->id)
            ->where('class_name', $cls->name)
            ->get(['student_id','subject_id','marks'])
            ->map(fn($r) => [ 'k' => $r->student_id.'_'.$r->subject_id, 'v' => $r->marks ])
            ->pluck('v','k');
        return response()->json([
            'subjects' => $subjects,
            'students' => $students,
            'marks' => $marks,
                        'fallback_subjects' => $fallbackSubjects,
            'effective_class_id' => $cls ? $cls->id : null,
                                    'assigned_subjects_count' => $assignedSubjectsCount,
        ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Failed to get marks matrix: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'A server error occurred while loading data. Please check the logs.'], 500);
        }
    }

    public function saveMarks(Request $request, int $examId)
    {
        $data = $request->validate([
            'class_name' => ['required','string'],
            'entries' => ['array'],
            'entries.*.student_id' => ['required','integer','exists:students,id'],
            'entries.*.subject_id' => ['required','integer','exists:subjects,id'],
            'entries.*.marks' => ['nullable','numeric','min:0','max:100'],
        ]);
        $exam = Exam::findOrFail($examId);
        $schoolNumber = optional($request->user())->school_number;
        $userId = optional($request->user())->id;
        $year = (int) $exam->year;
        $now = now();
        foreach ($data['entries'] ?? [] as $e) {
            $m = $e['marks'];
            $grade = null;
            if ($m !== null) {
                $mNum = (float) $m;
                if ($mNum >= 75) $grade = 'A';
                elseif ($mNum >= 65) $grade = 'B';
                elseif ($mNum >= 45) $grade = 'C';
                elseif ($mNum >= 30) $grade = 'D';
                else $grade = 'F';
            }
            \DB::table('exam_marks')->updateOrInsert(
                ['exam_id' => $exam->id, 'student_id' => $e['student_id'], 'subject_id' => $e['subject_id']],
                [
                    'class_name' => $data['class_name'],
                    'marks' => $e['marks'],
                    'grade' => $grade,
                    'school_number' => $schoolNumber,
                    'year' => $year,
                    'created_by' => $userId,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }
        return response()->json(['ok' => true]);
    }

    public function marking(int $examId)
    {
        $exam = Exam::with('classes:id,name')->findOrFail($examId);
        $schoolNumber = optional(request()->user())->school_number;
        $allClasses = SchoolClass::query()
            ->when($schoolNumber, function($w) use ($schoolNumber) {
                $w->whereExists(function($sq) use ($schoolNumber) {
                    $sq->select(DB::raw(1))
                        ->from('students as s')
                        ->whereColumn('s.class_name', 'classes.name')
                        ->where('s.exam_no', 'like', $schoolNumber.'-%');
                });
            })
            ->orderBy('name')->get(['id','name']);
        // preload students for chips count (same as show)
        $students = [];
        $classNames = $exam->classes->pluck('name')->all();
        if (!empty($classNames)) {
            $schoolNumber = optional(request()->user())->school_number;
            $students = \App\Models\Student::query()
                ->whereIn('class_name', $classNames)
                ->when($schoolNumber, function($w) use ($schoolNumber) {
                    $w->where('exam_no', 'like', $schoolNumber.'-%');
                })
                ->orderBy('class_name')->orderBy('name')
                ->get(['id','reg_no','name','class_name','sex']);
        }
        // Scope chips to classes from this school
        $examClassIds = $exam->classes->pluck('id');
        $scopedExamClasses = SchoolClass::query()
            ->whereIn('id', $examClassIds)
            ->when($schoolNumber, function($w) use ($schoolNumber) {
                $w->whereExists(function($sq) use ($schoolNumber) {
                    $sq->select(DB::raw(1))
                        ->from('students as s')
                        ->whereColumn('s.class_name', 'classes.name')
                        ->where('s.exam_no', 'like', $schoolNumber.'-%');
                });
            })
            ->orderBy('name')->get(['id','name']);

        return Inertia::render('Marking/Index', [
            'exam' => [
                'id' => $exam->id,
                'name' => $exam->name,
                'type' => $exam->type,
                'year' => $exam->year,
                'term' => $exam->term,
                'classes' => $scopedExamClasses->map->only(['id','name']),
            ],
            'classes' => $allClasses,
            'students' => $students,
        ]);
    }
}
