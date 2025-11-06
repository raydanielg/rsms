<?php

namespace App\Http\Controllers\Emas;

use App\Http\Controllers\Controller;
use App\Models\EmasSubject;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EmasSubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = EmasSubject::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $subjects = $query->latest()->paginate(15);

        return Inertia::render('Emas/Subjects/Index', [
            'subjects' => $subjects,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Emas/Subjects/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:emas_subjects,code',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        EmasSubject::create($validated);

        return redirect()->route('emas.subjects.index')
            ->with('success', 'Subject created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(EmasSubject $subject): Response
    {
        return Inertia::render('Emas/Subjects/Show', [
            'subject' => $subject,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EmasSubject $subject): Response
    {
        return Inertia::render('Emas/Subjects/Edit', [
            'subject' => $subject,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EmasSubject $subject)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:emas_subjects,code,' . $subject->id,
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $subject->update($validated);

        return redirect()->route('emas.subjects.index')
            ->with('success', 'Subject updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EmasSubject $subject)
    {
        $subject->delete();

        return redirect()->route('emas.subjects.index')
            ->with('success', 'Subject deleted successfully!');
    }
}
