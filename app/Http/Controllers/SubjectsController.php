<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class SubjectsController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $schoolNumber = optional($request->user())->school_number;
        $query = Subject::query()
            ->with('classes:id,name')
            ->when($schoolNumber, fn($w) => $w->where('school_number', $schoolNumber));
        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%$q%")
                  ->orWhere('code', 'like', "%$q%");
            });
        }
        $subjects = $query->orderBy('name')->paginate(10)->withQueryString();
        // classes options scoped to current school
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
        return Inertia::render('Subjects/Index', [
            'subjects' => $subjects,
            'filters' => ['q' => $q],
            'classes' => $classes,
        ]);
    }

    public function search(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $schoolNumber = optional($request->user())->school_number;
        $items = Subject::query()
            ->when($schoolNumber, fn($w) => $w->where('school_number', $schoolNumber))
            ->when($q !== '', function($w) use ($q) {
                $w->where(function ($ww) use ($q) {
                    $ww->where('name', 'like', "%$q%")
                       ->orWhere('code', 'like', "%$q%");
                });
            })
            ->orderBy('name')
            ->limit(8)
            ->get(['id','name','code']);
        return response()->json($items);
    }

    public function showJson(Request $request)
    {
        $id = (int) $request->query('id', 0);
        $schoolNumber = optional($request->user())->school_number;
        $subj = Subject::query()
            ->with('classes:id,name')
            ->when($schoolNumber, fn($w) => $w->where('school_number', $schoolNumber))
            ->findOrFail($id);
        return response()->json($subj);
    }

    public function show(int $id)
    {
        $schoolNumber = optional(request()->user())->school_number;
        $subject = Subject::query()
            ->with('classes:id,name')
            ->when($schoolNumber, fn($w) => $w->where('school_number', $schoolNumber))
            ->findOrFail($id);
        // Compute student counts per assigned class (by class_name match)
        $perClass = [];
        $total = 0;
        foreach ($subject->classes as $cls) {
            $count = \App\Models\Student::where('class_name', $cls->name)->count();
            $perClass[] = [ 'id' => $cls->id, 'name' => $cls->name, 'students' => $count ];
            $total += $count;
        }
        return Inertia::render('Subjects/Show', [
            'subject' => [
                'id' => $subject->id,
                'name' => $subject->name,
                'code' => $subject->code,
            ],
            'class_stats' => $perClass,
            'total_students' => $total,
        ]);
    }

    public function store(Request $request)
    {
        $schoolNumber = optional($request->user())->school_number;
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'code' => ['required','string','max:20','unique:subjects,code,NULL,id,school_number,'.$schoolNumber],
            'class_ids' => ['array'],
            'class_ids.*' => ['integer','exists:classes,id'],
        ]);
        $subj = Subject::create(['name' => $data['name'], 'code' => $data['code'], 'school_number' => $schoolNumber]);
        if (!empty($data['class_ids'])) {
            $subj->classes()->sync($data['class_ids']);
        }
        return Redirect::back()->with('success', 'Subject created');
    }

    public function update(Request $request, int $id)
    {
        $subj = Subject::findOrFail($id);
        $schoolNumber = $subj->school_number ?: optional($request->user())->school_number;
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'code' => ['required','string','max:20','unique:subjects,code,'.$subj->id.',id,school_number,'.$schoolNumber],
            'class_ids' => ['array'],
            'class_ids.*' => ['integer','exists:classes,id'],
        ]);
        $subj->update(['name' => $data['name'], 'code' => $data['code']]);
        $subj->classes()->sync($data['class_ids'] ?? []);
        return Redirect::back()->with('success', 'Subject updated');
    }

    public function destroy(Request $request, int $id)
    {
        $subj = Subject::findOrFail($id);
        $subj->delete();
        return Redirect::back()->with('success', 'Subject deleted');
    }
}
