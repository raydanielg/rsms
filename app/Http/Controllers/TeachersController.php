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
        $t = Teacher::query()
            ->when($schoolNumber, fn($w)=>$w->where('school_number', $schoolNumber))
            ->with(['classes:id,name', 'subjects:id,code,name'])
            ->findOrFail($id);
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
