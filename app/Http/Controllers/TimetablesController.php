<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Timetable;

class TimetablesController extends Controller
{
    public function index(Request $request)
    {
        $items = Timetable::query()
            ->orderByDesc('created_at')
            ->limit(50)
            ->get(['id','name','type','created_at'])
            ->map(function ($t) {
                return [
                    'id' => $t->id,
                    'name' => $t->name,
                    'category' => ucfirst($t->type ?? 'Other'),
                    'created_at' => $t->created_at?->toDateTimeString(),
                ];
            });

        return Inertia::render('Timetables/Index', [
            'items' => $items,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|string|in:class,exam,supervision,teacher',
            'name' => 'required|string|max:255',
            'effective_date' => 'required|date',
            'scope_type' => 'nullable|string|in:class,teacher,school',
            'scope_id' => 'nullable|integer',
            'scope_name' => 'nullable|string|max:255',
        ]);

        $t = new Timetable();
        $t->type = $data['type'];
        $t->name = $data['name'];
        $t->effective_date = $data['effective_date'];
        $t->scope_type = $data['scope_type'] ?? null;
        $t->scope_id = $data['scope_id'] ?? null;
        $t->scope_name = $data['scope_name'] ?? null;
        $t->status = 'draft';
        $t->created_by = $request->user()->id;
        $t->save();

        $route = $t->type === 'class' ? 'timetables.class.index' : 'timetables.index';
        return redirect()->route($route)->with('success', 'Timetable created');
    }

    public function classIndex(Request $request)
    {
        $items = Timetable::query()
            ->where('type', 'class')
            ->orderByDesc('created_at')
            ->limit(50)
            ->get(['id','name','type','created_at'])
            ->map(function ($t) {
                return [
                    'id' => $t->id,
                    'name' => $t->name,
                    'category' => 'Class',
                    'created_at' => $t->created_at?->toDateTimeString(),
                ];
            });

        return Inertia::render('Timetables/Class', [
            'items' => $items,
        ]);
    }
}
