<?php

namespace App\Http\Controllers\Emas;

use App\Http\Controllers\Controller;
use App\Models\EmasCandidate;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EmasCandidateController extends Controller
{
    public function index(): Response
    {
        $candidates = EmasCandidate::latest()->paginate(10);

        return Inertia::render('Emas/Candidates/Index', [
            'candidates' => $candidates,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Emas/Candidates/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female',
            'registration_number' => 'required|string|unique:emas_candidates,registration_number',
            'exam_center_code' => 'required|string',
            'level' => 'required|in:primary,secondary,advanced',
            'class_form' => 'required|string',
            'phone' => 'required|string',
            'email' => 'nullable|email',
            'guardian_name' => 'required|string',
            'guardian_phone' => 'required|string',
            'address' => 'required|string',
            'district' => 'required|string',
            'region' => 'required|string',
            'special_needs' => 'nullable|string',
        ]);

        EmasCandidate::create($validated);

        return redirect()->route('emas.candidates.index')
            ->with('success', 'Candidate registered successfully!');
    }

    public function show(EmasCandidate $candidate): Response
    {
        $candidate->load(['results.exam', 'center']);

        return Inertia::render('Emas/Candidates/Show', [
            'candidate' => $candidate,
        ]);
    }

    public function edit(EmasCandidate $candidate): Response
    {
        return Inertia::render('Emas/Candidates/Edit', [
            'candidate' => $candidate,
        ]);
    }

    public function update(Request $request, EmasCandidate $candidate)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female',
            'registration_number' => 'required|string|unique:emas_candidates,registration_number,' . $candidate->id,
            'exam_center_code' => 'required|string',
            'level' => 'required|in:primary,secondary,advanced',
            'class_form' => 'required|string',
            'phone' => 'required|string',
            'email' => 'nullable|email',
            'guardian_name' => 'required|string',
            'guardian_phone' => 'required|string',
            'address' => 'required|string',
            'district' => 'required|string',
            'region' => 'required|string',
            'special_needs' => 'nullable|string',
            'status' => 'required|in:active,pending,suspended',
        ]);

        $candidate->update($validated);

        return redirect()->route('emas.candidates.index')
            ->with('success', 'Candidate updated successfully!');
    }

    public function destroy(EmasCandidate $candidate)
    {
        $candidate->delete();

        return redirect()->route('emas.candidates.index')
            ->with('success', 'Candidate deleted successfully!');
    }
}
