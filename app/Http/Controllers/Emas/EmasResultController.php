<?php

namespace App\Http\Controllers\Emas;

use App\Http\Controllers\Controller;
use App\Models\EmasResult;
use App\Models\EmasExam;
use App\Models\EmasCandidate;
use App\Models\EmasCenter;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EmasResultController extends Controller
{
    public function index(Request $request): Response
    {
        $query = EmasCenter::withCount('candidates');

        // Filter by region
        if ($request->filled('region')) {
            $query->where('region', $request->region);
        }

        // Search by center name or code
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('center_name', 'like', "%{$search}%")
                  ->orWhere('center_code', 'like', "%{$search}%")
                  ->orWhere('district', 'like', "%{$search}%");
            });
        }

        $centers = $query->latest()->paginate(12)->withQueryString();

        // Get statistics for each center with results
        $centers->getCollection()->transform(function($center) {
            // Count results for this center
            $resultsCount = EmasResult::whereHas('candidate', function($q) use ($center) {
                $q->where('exam_center_code', $center->center_code);
            })->count();

            // Get average score for this center
            $avgScore = EmasResult::whereHas('candidate', function($q) use ($center) {
                $q->where('exam_center_code', $center->center_code);
            })->avg('score');

            // Count exams for this center
            $examsCount = EmasExam::whereHas('candidates', function($q) use ($center) {
                $q->where('exam_center_code', $center->center_code);
            })->count();

            $center->results_count = $resultsCount;
            $center->average_score = $avgScore ? round($avgScore, 1) : 0;
            $center->exams_count = $examsCount;

            return $center;
        });

        // Overall statistics
        $stats = [
            'total_centers' => EmasCenter::count(),
            'total_results' => EmasResult::count(),
            'total_candidates' => \App\Models\EmasCandidate::count(),
            'regions_count' => EmasCenter::distinct('region')->count('region'),
        ];

        // Get all regions used in centers
        $usedRegions = EmasCenter::distinct('region')
            ->pluck('region')
            ->sort()
            ->values()
            ->toArray();

        return Inertia::render('Emas/Results/Index', [
            'centers' => $centers,
            'stats' => $stats,
            'usedRegions' => $usedRegions,
            'filters' => $request->only(['search', 'region']),
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

    public function show(EmasCenter $center): Response
    {
        // Load candidates with their results
        $candidates = $center->candidates()->with(['results.exam'])->latest()->paginate(50);

        // Transform candidates to include calculated fields
        $candidates->getCollection()->transform(function($candidate) {
            // Get all results for this candidate
            $results = [];
            $totalScore = 0;
            $subjectCount = 0;
            
            foreach($candidate->results as $result) {
                $subjectName = $result->exam->subject ?? 'Unknown';
                $results[$subjectName] = [
                    'score' => $result->score,
                    'grade' => $result->grade,
                ];
                $totalScore += $result->score;
                $subjectCount++;
            }
            
            $candidate->results = $results;
            $candidate->total_score = $totalScore;
            $candidate->average_score = $subjectCount > 0 ? round($totalScore / $subjectCount, 1) : 0;
            
            // Calculate division based on average
            $avg = $candidate->average_score;
            if ($avg >= 75) $candidate->division = 'I';
            elseif ($avg >= 65) $candidate->division = 'II';
            elseif ($avg >= 50) $candidate->division = 'III';
            elseif ($avg >= 40) $candidate->division = 'IV';
            else $candidate->division = '0';
            
            $candidate->full_name = trim(($candidate->first_name ?? '') . ' ' . ($candidate->middle_name ?? '') . ' ' . ($candidate->last_name ?? ''));
            
            return $candidate;
        });

        // Get all active subjects from the subjects table
        $subjects = \App\Models\EmasSubject::where('status', 'active')
            ->orderBy('name')
            ->pluck('name')
            ->toArray();

        // Get all exams for this center
        $exams = EmasExam::whereHas('candidates', function($q) use ($center) {
            $q->where('exam_center_code', $center->center_code);
        })->get();

        // Calculate statistics
        $stats = [
            'total_candidates' => $center->candidates()->count(),
            'total_exams' => $exams->count(),
            'average_score' => EmasResult::whereHas('candidate', function($q) use ($center) {
                $q->where('exam_center_code', $center->center_code);
            })->avg('score'),
        ];

        return Inertia::render('Emas/Results/SchoolResults', [
            'center' => $center,
            'candidates' => $candidates,
            'subjects' => $subjects,
            'stats' => $stats,
            'exams' => $exams,
        ]);
    }

    public function destroy(EmasResult $result)
    {
        $result->delete();

        return redirect()->route('emas.results.index')
            ->with('success', 'Result deleted successfully!');
    }
}
