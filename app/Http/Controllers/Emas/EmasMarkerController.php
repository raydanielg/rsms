<?php

namespace App\Http\Controllers\Emas;

use App\Http\Controllers\Controller;
use App\Models\EmasMarker;
use App\Models\EmasMarkerAssignment;
use App\Models\EmasExam;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EmasMarkerController extends Controller
{
    public function index(): Response
    {
        $markers = EmasMarker::latest()->paginate(10);

        return Inertia::render('Emas/Markers/Index', [
            'markers' => $markers,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Emas/Markers/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:emas_markers,email',
            'phone' => 'required|string',
            'marker_code' => 'required|string|unique:emas_markers,marker_code',
            'subjects' => 'required|array|min:1',
            'subjects.*' => 'required|string',
            'district' => 'nullable|string',
            'region' => 'required|string',
            'scope' => 'required|in:district,region',
            'qualifications' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        EmasMarker::create($validated);

        return redirect()->route('emas.markers.index')
            ->with('success', 'Marker added successfully!');
    }

    public function show(EmasMarker $marker): Response
    {
        $marker->load(['assignments.exam']);

        return Inertia::render('Emas/Markers/Show', [
            'marker' => $marker,
        ]);
    }

    public function edit(EmasMarker $marker): Response
    {
        return Inertia::render('Emas/Markers/Edit', [
            'marker' => $marker,
        ]);
    }

    public function update(Request $request, EmasMarker $marker)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:emas_markers,email,' . $marker->id,
            'phone' => 'required|string',
            'marker_code' => 'required|string|unique:emas_markers,marker_code,' . $marker->id,
            'subjects' => 'required|array|min:1',
            'subjects.*' => 'required|string',
            'district' => 'nullable|string',
            'region' => 'required|string',
            'scope' => 'required|in:district,region',
            'status' => 'required|in:active,inactive,suspended',
            'qualifications' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $marker->update($validated);

        return redirect()->route('emas.markers.index')
            ->with('success', 'Marker updated successfully!');
    }

    public function destroy(EmasMarker $marker)
    {
        $marker->delete();

        return redirect()->route('emas.markers.index')
            ->with('success', 'Marker deleted successfully!');
    }

    public function assign(Request $request)
    {
        $validated = $request->validate([
            'marker_id' => 'required|exists:emas_markers,id',
            'exam_id' => 'required|exists:emas_exams,id',
            'papers_assigned' => 'required|integer|min:1',
        ]);

        $assignment = EmasMarkerAssignment::updateOrCreate(
            [
                'marker_id' => $validated['marker_id'],
                'exam_id' => $validated['exam_id'],
            ],
            [
                'papers_assigned' => $validated['papers_assigned'],
                'papers_pending' => $validated['papers_assigned'],
                'assigned_at' => now(),
            ]
        );

        return redirect()->back()
            ->with('success', 'Exam assigned to marker successfully!');
    }

    public function assignments(): Response
    {
        $assignments = EmasMarkerAssignment::with(['marker', 'exam'])
            ->latest()
            ->paginate(15);

        return Inertia::render('Emas/Markers/Assignments', [
            'assignments' => $assignments,
        ]);
    }
}
