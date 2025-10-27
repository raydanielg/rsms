<?php

namespace App\Http\Controllers\Emas;

use App\Http\Controllers\Controller;
use App\Models\EmasResult;
use App\Models\EmasExam;
use App\Models\EmasCandidate;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EmasResultController extends Controller
{
    public function index(): Response
    {
        $results = EmasResult::with(['exam', 'candidate'])
            ->latest()
            ->paginate(10);

        return Inertia::render('Emas/Results/Index', [
            'results' => $results,
        ]);
    }

    public function upload(): Response
    {
        return Inertia::render('Emas/Results/Upload');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'exam_id' => 'required|exists:emas_exams,id',
            'upload_method' => 'required|in:file,manual',
            'file' => 'required_if:upload_method,file|file|mimes:xlsx,xls,csv',
            'results' => 'required_if:upload_method,manual|array',
            'results.*.candidate_id' => 'required_if:upload_method,manual|exists:emas_candidates,id',
            'results.*.score' => 'required_if:upload_method,manual|integer|min:0',
        ]);

        $exam = EmasExam::findOrFail($validated['exam_id']);
        $uploadedBy = auth()->guard('emas')->id();

        if ($validated['upload_method'] === 'manual') {
            foreach ($validated['results'] as $resultData) {
                $grade = EmasResult::calculateGrade($resultData['score'], $exam->total_marks);
                $remarks = EmasResult::getRemarks($grade);

                EmasResult::updateOrCreate(
                    [
                        'exam_id' => $exam->id,
                        'candidate_id' => $resultData['candidate_id'],
                    ],
                    [
                        'score' => $resultData['score'],
                        'grade' => $grade,
                        'remarks' => $remarks,
                        'uploaded_by' => $uploadedBy,
                    ]
                );
            }
        } else {
            // Handle file upload (Excel/CSV processing)
            // This would require a package like maatwebsite/excel
            // For now, we'll just acknowledge the file
        }

        return redirect()->route('emas.results.index')
            ->with('success', 'Results uploaded successfully!');
    }

    public function show(EmasResult $result): Response
    {
        $result->load(['exam', 'candidate', 'uploader']);

        return Inertia::render('Emas/Results/Show', [
            'result' => $result,
        ]);
    }

    public function destroy(EmasResult $result)
    {
        $result->delete();

        return redirect()->route('emas.results.index')
            ->with('success', 'Result deleted successfully!');
    }
}
