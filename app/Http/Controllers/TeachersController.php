<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\SchoolClass;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class TeachersController extends Controller
{
    public function index(Request $request)
    {
        $schoolNumber = optional($request->user())->school_number;
        $q = trim((string) $request->query('q', ''));

        $teachers = Teacher::query()
            ->when($schoolNumber, fn($w)=>$w->where('school_number', $schoolNumber))
            ->when($q !== '', function($w) use ($q) {
                $w->where(function($ww) use ($q){
                    $ww->where('name', 'like', "%$q%")
                       ->orWhere('check_no', 'like', "%$q%")
                       ->orWhere('phone', 'like', "%$q%");
                });
            })
            ->with(['classes:id,name', 'subjects:id,code,name'])
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Teachers/Index', [
            'teachers' => $teachers,
            'filters' => ['q' => $q],
        ]);
    }

    public function exam(Request $request, int $id, int $examId)
    {
        $schoolNumber = optional($request->user())->school_number;
        $t = Teacher::query()
            ->when($schoolNumber, fn($w)=>$w->where('school_number', $schoolNumber))
            ->with(['classes:id,name', 'subjects:id,code,name'])
            ->findOrFail($id);

        $exam = \App\Models\Exam::findOrFail($examId);

        return Inertia::render('Teachers/Exam', [
            'teacher' => [
                'id' => $t->id,
                'name' => $t->name,
                'check_no' => $t->check_no,
                'classes' => $t->classes->map->only(['id','name']),
                'subjects' => $t->subjects->map->only(['id','name','code']),
            ],
            'exam' => [
                'id' => $exam->id,
                'name' => $exam->name,
                'type' => $exam->type,
                'year' => $exam->year,
                'term' => $exam->term,
            ],
        ]);
    }

    public function showJson(Request $request)
    {
        $id = (int) $request->query('id', 0);
        $schoolNumber = optional($request->user())->school_number;
        $t = Teacher::query()
            ->when($schoolNumber, fn($w)=>$w->where('school_number', $schoolNumber))
            ->with(['classes:id,name', 'subjects:id,code,name'])
            ->findOrFail($id);
        return response()->json($t);
    }

    public function show(Request $request, int $id)
    {
        $schoolNumber = optional($request->user())->school_number;
        $q = trim((string) $request->query('q', ''));
        $subjectId = (int) $request->query('subject_id', 0);

        $t = Teacher::query()
            ->when($schoolNumber, fn($w)=>$w->where('school_number', $schoolNumber))
            ->with(['classes:id,name', 'subjects:id,code,name'])
            ->findOrFail($id);

        // Build class subjects map for teacher's classes
        $classNames = $t->classes->pluck('name')->filter()->values();
        $classSubjects = [];
        if ($classNames->isNotEmpty()) {
            $clsList = \App\Models\SchoolClass::query()
                ->whereIn('name', $classNames)
                ->when($schoolNumber, fn($w)=>$w->where('school_number', $schoolNumber))
                ->with('subjects:id,name,code')
                ->get(['id','name']);
            foreach ($clsList as $c) {
                $classSubjects[$c->name] = $c->subjects->map(fn($s)=>['id'=>$s->id,'name'=>$s->name,'code'=>$s->code])->all();
            }
        }

        $teacherSubjectIds = $t->subjects->pluck('id')->all();

        // Fetch students in those classes
        $studentsQuery = \App\Models\Student::query()
            ->when($classNames->isNotEmpty(), fn($w)=>$w->whereIn('class_name', $classNames))
            ->when($q !== '', function($w) use ($q){
                $w->where(function($ww) use ($q){
                    $ww->where('name','like',"%$q%")
                       ->orWhere('reg_no','like',"%$q%")
                       ->orWhere('class_name','like',"%$q%");
                });
            })
            ->orderBy('name');

        $students = $studentsQuery->get(['id','reg_no','name','class_name']);

        // Attach subjects intersection per student and optional filter by subjectId
        $studentsOut = [];
        foreach ($students as $s) {
            $clsName = $s->class_name ?? '';
            $clsSubjects = collect($classSubjects[$clsName] ?? []);
            $subs = $clsSubjects->filter(fn($sub) => in_array($sub['id'], $teacherSubjectIds))->values()->all();
            if ($subjectId && !in_array($subjectId, array_column($subs, 'id'))) {
                continue; // filter out students not in this subject
            }
            $studentsOut[] = [
                'id' => $s->id,
                'reg_no' => $s->reg_no,
                'name' => $s->name,
                'class_name' => $clsName,
                'subjects' => array_values($subs),
            ];
        }

        // Exams for the teacher's classes
        $classIds = $t->classes->pluck('id')->filter()->values();
        $exams = [];
        if ($classIds->isNotEmpty()) {
            $exams = \App\Models\Exam::query()
                ->whereHas('classes', fn($w) => $w->whereIn('classes.id', $classIds))
                ->withCount('classes')
                ->orderByDesc('year')->orderByDesc('term')->orderByDesc('id')
                ->get(['id','name','type','year','term']);
        }

        return Inertia::render('Teachers/Show', [
            'teacher' => [
                'id' => $t->id,
                'name' => $t->name,
                'check_no' => $t->check_no,
                'phone' => $t->phone,
                'email' => $t->email,
                'sex' => $t->sex,
                'classes' => $t->classes->map->only(['id','name']),
                'subjects' => $t->subjects->map->only(['id','code','name']),
            ],
            'students' => $studentsOut,
            'exams' => $exams,
            'filters' => [ 'q' => $q, 'subject_id' => $subjectId ?: null ],
        ]);
    }

    public function store(Request $request)
    {
        $schoolNumber = optional($request->user())->school_number;
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'check_no' => ['required','string','max:50'],
            'phone' => ['required','string','max:30'],
            'email' => ['nullable','email','max:255'],
            'sex' => ['required','in:M,F'],
            'class_ids' => ['required','array','min:1'],
            'class_ids.*' => ['integer','exists:classes,id'],
            'subject_ids' => ['required','array','min:1'],
            'subject_ids.*' => ['integer','exists:subjects,id'],
        ]);

        DB::transaction(function() use ($data, $schoolNumber) {
            $t = Teacher::create([
                'school_number' => $schoolNumber,
                'name' => $data['name'],
                'check_no' => $data['check_no'],
                'phone' => $data['phone'],
                'email' => $data['email'] ?? null,
                'sex' => $data['sex'],
            ]);
            $t->classes()->sync($data['class_ids'] ?? []);
            $t->subjects()->sync($data['subject_ids'] ?? []);
        });

        return Redirect::back()->with('success', 'Teacher added');
    }

    public function destroy(Request $request, int $id)
    {
        $schoolNumber = optional($request->user())->school_number;
        $t = Teacher::query()->when($schoolNumber, fn($w)=>$w->where('school_number', $schoolNumber))->findOrFail($id);
        $t->classes()->detach();
        $t->subjects()->detach();
        $t->delete();
        return Redirect::back()->with('success', 'Teacher deleted');
    }
}
