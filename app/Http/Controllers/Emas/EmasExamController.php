<?php

namespace App\Http\Controllers\Emas;

use App\Http\Controllers\Controller;
use App\Models\EmasExam;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EmasExamController extends Controller
{
    public function index(): Response
    {
        $exams = EmasExam::latest()->paginate(10);

        return Inertia::render('Emas/Exams/Index', [
            'exams' => $exams,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Emas/Exams/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'exam_name' => 'required|string|max:255',
            'exam_code' => 'required|string|unique:emas_exams,exam_code',
            'subject' => 'required|string',
            'exam_type' => 'required|in:written,practical,oral,project',
            'level' => 'required|in:primary,secondary,advanced',
            'class_form' => 'required|string',
            'exam_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'duration' => 'required|integer|min:1',
            'total_marks' => 'required|integer|min:1',
            'pass_marks' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
        ]);

        EmasExam::create($validated);

        return redirect()->route('emas.exams.index')
            ->with('success', 'Exam created successfully!');
    }

    public function show(EmasExam $exam): Response
    {
        $exam->load(['results.candidate']);

        return Inertia::render('Emas/Exams/Show', [
            'exam' => $exam,
        ]);
    }

    public function edit(EmasExam $exam): Response
    {
        return Inertia::render('Emas/Exams/Edit', [
            'exam' => $exam,
        ]);
    }

    public function update(Request $request, EmasExam $exam)
    {
        $validated = $request->validate([
            'exam_name' => 'required|string|max:255',
            'exam_code' => 'required|string|unique:emas_exams,exam_code,' . $exam->id,
            'subject' => 'required|string',
            'exam_type' => 'required|in:written,practical,oral,project',
            'level' => 'required|in:primary,secondary,advanced',
            'class_form' => 'required|string',
            'exam_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'duration' => 'required|integer|min:1',
            'total_marks' => 'required|integer|min:1',
            'pass_marks' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'status' => 'required|in:scheduled,ongoing,completed,cancelled',
        ]);

        $exam->update($validated);

        return redirect()->route('emas.exams.index')
            ->with('success', 'Exam updated successfully!');
    }

    public function destroy(EmasExam $exam)
    {
        $exam->delete();

        return redirect()->route('emas.exams.index')
            ->with('success', 'Exam deleted successfully!');
    }
}
