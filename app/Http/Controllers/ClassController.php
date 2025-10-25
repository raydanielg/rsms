<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class ClassController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $schoolNumber = optional($request->user())->school_number;
        $query = SchoolClass::query()
            ->with('subjects:id,name,code')
            ->when($schoolNumber, fn($w)=>$w->where('school_number', $schoolNumber));
        if ($q !== '') {
            $query->where('name', 'like', "%$q%");
        }
        $classes = $query->orderBy('name')->paginate(10)->withQueryString();
        $allSubjects = Subject::orderBy('name')->get(['id','name','code']);
        return Inertia::render('Classes/Index', [
            'classes' => $classes,
            'filters' => [ 'q' => $q ],
            'subjects' => $allSubjects,
        ]);
    }

    public function search(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $schoolNumber = optional($request->user())->school_number;
        $items = SchoolClass::query()
            ->when($schoolNumber, function($w) use ($schoolNumber) {
                $w->whereExists(function($sq) use ($schoolNumber) {
                    $sq->select(DB::raw(1))
                        ->from('students as s')
                        ->whereColumn('s.class_name', 'classes.name')
                        ->where('s.exam_no', 'like', $schoolNumber.'-%');
                });
            })
            ->when($q !== '', function($w) use ($q) {
                $w->where('name', 'like', "%$q%");
            })
            ->orderBy('name')
            ->limit(8)
            ->get(['id','name']);
        return response()->json($items);
    }

    public function showJson(Request $request)
    {
        $id = (int) $request->query('id', 0);
        $name = trim((string) $request->query('name', ''));
        $schoolNumber = optional($request->user())->school_number;
        $base = SchoolClass::query()
            ->with('subjects:id,name,code')
            ->when($schoolNumber, function($w) use ($schoolNumber) {
                $w->whereExists(function($sq) use ($schoolNumber) {
                    $sq->select(DB::raw(1))
                        ->from('students as s')
                        ->whereColumn('s.class_name', 'classes.name')
                        ->where('s.exam_no', 'like', $schoolNumber.'-%');
                });
            });
        if ($id) {
            $cls = $base->where('id', $id)->firstOrFail();
        } else {
            $q = strtolower($name);
            $cls = $base->whereRaw('LOWER(name)=?', [$q])->firstOrFail();
        }
        return response()->json($cls);
    }

    public function store(Request $request)
    {
        $schoolNumber = optional($request->user())->school_number;
        $data = $request->validate([
            'name' => ['required','string','max:255','unique:classes,name,NULL,id,school_number,'.$schoolNumber],
            'subject_ids' => ['array'],
            'subject_ids.*' => ['integer','exists:subjects,id'],
        ]);
        DB::transaction(function () use ($data, $schoolNumber) {
            $cls = SchoolClass::create(['name' => $data['name'], 'school_number' => $schoolNumber]);
            if (!empty($data['subject_ids'])) {
                $cls->subjects()->sync($data['subject_ids']);
            }
        });
        return Redirect::back()->with('success', 'Class created');
    }

    public function update(Request $request, int $id)
    {
        $cls = SchoolClass::findOrFail($id);
        $data = $request->validate([
            'name' => ['required','string','max:255','unique:classes,name,'.$cls->id.',id,school_number,'.$cls->school_number],
            'subject_ids' => ['array'],
            'subject_ids.*' => ['integer','exists:subjects,id'],
        ]);
        DB::transaction(function () use ($cls, $data) {
            $cls->update(['name' => $data['name']]);
            $cls->subjects()->sync($data['subject_ids'] ?? []);
        });
        return Redirect::back()->with('success', 'Class updated');
    }

    public function destroy(Request $request, int $id)
    {
        $cls = SchoolClass::findOrFail($id);
        $cls->delete();
        return Redirect::back()->with('success', 'Class deleted');
    }

    public function show(int $id)
    {
        $schoolNumber = optional(request()->user())->school_number;
        $clsQuery = SchoolClass::query()
            ->with('subjects:id,name,code')
            ->when($schoolNumber, function($w) use ($schoolNumber) {
                $w->whereExists(function($sq) use ($schoolNumber) {
                    $sq->select(DB::raw(1))
                        ->from('students as s')
                        ->whereColumn('s.class_name', 'classes.name')
                        ->where('s.exam_no', 'like', $schoolNumber.'-%');
                });
            });
        $cls = $clsQuery->findOrFail($id);
        $students = \App\Models\Student::where('class_name', $cls->name)
            ->orderBy('name')->get(['id','reg_no','name','sex','class_name']);
        return Inertia::render('Classes/Show', [
            'klass' => [
                'id' => $cls->id,
                'name' => $cls->name,
                'subjects' => $cls->subjects->map->only(['id','name','code']),
            ],
            'students' => $students,
        ]);
    }

    public function subjects(int $id)
    {
        $schoolNumber = optional(request()->user())->school_number;
        $clsQuery = SchoolClass::query()
            ->with('subjects:id,name,code')
            ->when($schoolNumber, function($w) use ($schoolNumber) {
                $w->whereExists(function($sq) use ($schoolNumber) {
                    $sq->select(DB::raw(1))
                        ->from('students as s')
                        ->whereColumn('s.class_name', 'classes.name')
                        ->where('s.exam_no', 'like', $schoolNumber.'-%');
                });
            });
        $cls = $clsQuery->findOrFail($id);
        return response()->json([ 'id' => $cls->id, 'name' => $cls->name, 'subjects' => $cls->subjects ]);
    }
}
