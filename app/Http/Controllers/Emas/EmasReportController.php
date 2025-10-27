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

class EmasReportController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Emas/Reports/Index');
    }

    public function generate(Request $request)
    {
        $validated = $request->validate([
            'report_type' => 'required|in:performance,candidate,center,subject,comparative',
            'exam_id' => 'nullable|exists:emas_exams,id',
            'period' => 'required|in:weekly,monthly,quarterly,yearly,custom',
            'start_date' => 'required_if:period,custom|date',
            'end_date' => 'required_if:period,custom|date',
        ]);

        $reportData = $this->generateReportData($validated);

        return response()->json([
            'success' => true,
            'data' => $reportData,
        ]);
    }

    private function generateReportData(array $params): array
    {
        $reportType = $params['report_type'];

        return match($reportType) {
            'performance' => $this->generatePerformanceReport($params),
            'candidate' => $this->generateCandidateReport($params),
            'center' => $this->generateCenterReport($params),
            'subject' => $this->generateSubjectReport($params),
            'comparative' => $this->generateComparativeReport($params),
            default => [],
        };
    }

    private function generatePerformanceReport(array $params): array
    {
        $query = EmasResult::with(['exam', 'candidate']);

        if (isset($params['exam_id'])) {
            $query->where('exam_id', $params['exam_id']);
        }

        $results = $query->get();

        $totalCandidates = $results->count();
        $passedCandidates = $results->whereIn('grade', ['A', 'B', 'C', 'D'])->count();
        $passRate = $totalCandidates > 0 ? ($passedCandidates / $totalCandidates) * 100 : 0;

        $gradeDistribution = [
            'A' => $results->where('grade', 'A')->count(),
            'B' => $results->where('grade', 'B')->count(),
            'C' => $results->where('grade', 'C')->count(),
            'D' => $results->where('grade', 'D')->count(),
            'F' => $results->where('grade', 'F')->count(),
        ];

        $averageScore = $results->avg('score');

        return [
            'total_candidates' => $totalCandidates,
            'passed_candidates' => $passedCandidates,
            'pass_rate' => round($passRate, 2),
            'grade_distribution' => $gradeDistribution,
            'average_score' => round($averageScore, 2),
        ];
    }

    private function generateCandidateReport(array $params): array
    {
        $candidates = EmasCandidate::withCount('results')->get();

        return [
            'total_candidates' => $candidates->count(),
            'by_level' => [
                'primary' => $candidates->where('level', 'primary')->count(),
                'secondary' => $candidates->where('level', 'secondary')->count(),
                'advanced' => $candidates->where('level', 'advanced')->count(),
            ],
            'by_gender' => [
                'male' => $candidates->where('gender', 'male')->count(),
                'female' => $candidates->where('gender', 'female')->count(),
            ],
            'by_status' => [
                'active' => $candidates->where('status', 'active')->count(),
                'pending' => $candidates->where('status', 'pending')->count(),
                'suspended' => $candidates->where('status', 'suspended')->count(),
            ],
        ];
    }

    private function generateCenterReport(array $params): array
    {
        $centers = EmasCenter::withCount('candidates')->get();

        return [
            'total_centers' => $centers->count(),
            'active_centers' => $centers->where('status', 'active')->count(),
            'total_capacity' => $centers->sum('capacity'),
            'total_candidates' => $centers->sum('candidates_count'),
            'by_region' => $centers->groupBy('region')->map(fn($group) => $group->count())->toArray(),
        ];
    }

    private function generateSubjectReport(array $params): array
    {
        $exams = EmasExam::with('results')->get();

        $subjectPerformance = $exams->groupBy('subject')->map(function($subjectExams) {
            $allResults = $subjectExams->flatMap->results;
            $totalCandidates = $allResults->count();
            $passedCandidates = $allResults->whereIn('grade', ['A', 'B', 'C', 'D'])->count();

            return [
                'total_exams' => $subjectExams->count(),
                'total_candidates' => $totalCandidates,
                'pass_rate' => $totalCandidates > 0 ? round(($passedCandidates / $totalCandidates) * 100, 2) : 0,
                'average_score' => round($allResults->avg('score'), 2),
            ];
        });

        return $subjectPerformance->toArray();
    }

    private function generateComparativeReport(array $params): array
    {
        // Comparative analysis between different periods, centers, or levels
        return [
            'message' => 'Comparative report generation in progress',
        ];
    }
}
