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

    public function subjectStatistics(): Response
    {
        return Inertia::render('Emas/Reports/SubjectStatistics', [
            'regions' => [],
            'districts' => [],
            'wards' => [],
            'subjects' => [],
        ]);
    }

    public function schoolRanks(Request $request): Response
    {
        // Get filters from request
        $filters = [
            'region' => $request->input('region'),
            'district' => $request->input('district'),
            'ward' => $request->input('ward'),
            'subject' => $request->input('subject'),
            'year' => $request->input('year', date('Y')),
            'exam_type' => $request->input('exam_type', 'pre-national'),
        ];

        // Get all centers with candidates and results
        $centers = EmasCenter::with(['candidates' => function($query) use ($filters) {
            $query->with(['results' => function($q) use ($filters) {
                $q->with('exam');
                // Filter by subject if provided
                if (!empty($filters['subject'])) {
                    $q->whereHas('exam', function($examQuery) use ($filters) {
                        $examQuery->where('subject', $filters['subject']);
                    });
                }
            }]);
        }])
        ->where('status', 'active')
        ->get();

        // Calculate rankings for each center
        $rankings = $centers->map(function($center) use ($filters) {
            $candidates = $center->candidates;
            $totalCandidates = $candidates->count();
            
            if ($totalCandidates === 0) {
                return null;
            }

            // Initialize grade counts
            $gradeCounts = ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'F' => 0];
            $totalScore = 0;
            $validScores = 0;

            foreach ($candidates as $candidate) {
                foreach ($candidate->results as $result) {
                    if (!empty($filters['subject'])) {
                        // Only count results for the specified subject
                        if ($result->exam && $result->exam->subject === $filters['subject']) {
                            $grade = $result->grade ?? 'F';
                            $gradeCounts[$grade] = ($gradeCounts[$grade] ?? 0) + 1;
                            $totalScore += $result->score ?? 0;
                            $validScores++;
                        }
                    }
                }
            }

            // Calculate statistics
            $acCount = $gradeCounts['A'] + $gradeCounts['B'] + $gradeCounts['C'];
            $adCount = $acCount + $gradeCounts['D'];
            $acPercentage = $validScores > 0 ? round(($acCount / $validScores) * 100, 2) : 0;
            $adPercentage = $validScores > 0 ? round(($adCount / $validScores) * 100, 2) : 0;
            $averageScore = $validScores > 0 ? round($totalScore / $validScores, 2) : 0;
            
            // Calculate GPA (assuming A=5, B=4, C=3, D=2, F=1)
            $gpaPoints = ($gradeCounts['A'] * 5) + ($gradeCounts['B'] * 4) + 
                        ($gradeCounts['C'] * 3) + ($gradeCounts['D'] * 2) + ($gradeCounts['F'] * 1);
            $gpa = $validScores > 0 ? round($gpaPoints / $validScores, 4) : 0;
            
            // Determine overall grade
            $overallGrade = 'F';
            if ($averageScore >= 75) $overallGrade = 'A';
            elseif ($averageScore >= 65) $overallGrade = 'B';
            elseif ($averageScore >= 50) $overallGrade = 'C';
            elseif ($averageScore >= 30) $overallGrade = 'D';
            
            // Competency level
            $competencyLevel = match($overallGrade) {
                'A' => 'Grade A (Excellent)',
                'B' => 'Grade B (Very Good)',
                'C' => 'Grade C (Good)',
                'D' => 'Grade D (Satisfactory)',
                default => 'Grade F (Fail)'
            };

            return [
                'center_id' => $center->id,
                'center_name' => $center->center_name,
                'center_code' => $center->center_code,
                'ownership' => $center->ownership ?? 'GOVERNMENT',
                'total_candidates' => $totalCandidates,
                'registered' => $validScores,
                'average' => $averageScore,
                'grade' => $overallGrade,
                'grade_a' => $gradeCounts['A'],
                'grade_b' => $gradeCounts['B'],
                'grade_c' => $gradeCounts['C'],
                'grade_d' => $gradeCounts['D'],
                'grade_f' => $gradeCounts['F'],
                'total_sat' => $validScores,
                'ac_count' => $acCount,
                'ac_percentage' => $acPercentage,
                'ad_count' => $adCount,
                'ad_percentage' => $adPercentage,
                'gpa' => $gpa,
                'competency_level' => $competencyLevel,
            ];
        })->filter()->values();

        // Sort by average score (descending) and add rank
        $rankings = $rankings->sortByDesc('average')->values();
        $rankings = $rankings->map(function($item, $index) {
            $item['rank'] = $index + 1;
            return $item;
        });

        return Inertia::render('Emas/Reports/SchoolRanks', [
            'rankings' => $rankings,
            'filters' => $filters,
        ]);
    }

    public function wardFilters(): Response
    {
        return Inertia::render('Emas/Reports/WardFilters', [
            'regions' => [],
            'districts' => [],
            'wards' => [],
        ]);
    }

    public function wardRanks(Request $request): Response
    {
        // Get filters from request
        $filters = [
            'region' => $request->input('region'),
            'district' => $request->input('district'),
            'ward' => $request->input('ward'),
            'year' => $request->input('year', date('Y')),
            'exam_type' => $request->input('exam_type', 'pre-national'),
            'view_type' => $request->input('view_type', 'all'),
        ];

        // Get all centers grouped by ward
        $centers = EmasCenter::with(['candidates.results.exam'])
            ->where('status', 'active')
            ->get();

        // Group centers by ward and calculate ward statistics
        $wardData = [];
        
        foreach ($centers as $center) {
            $ward = $center->ward ?? 'Unknown';
            
            if (!isset($wardData[$ward])) {
                $wardData[$ward] = [
                    'ward_name' => $ward,
                    'schools_count' => 0,
                    'total_registered' => 0,
                    'total_sat' => 0,
                    'grade_i' => 0,
                    'grade_ii' => 0,
                    'grade_iii' => 0,
                    'grade_iv' => 0,
                    'grade_0' => 0,
                    'total_score' => 0,
                ];
            }

            $wardData[$ward]['schools_count']++;
            
            // Count candidates and grades
            foreach ($center->candidates as $candidate) {
                $wardData[$ward]['total_registered']++;
                
                // Calculate average division from results
                $totalScore = 0;
                $scoreCount = 0;
                
                foreach ($candidate->results as $result) {
                    $totalScore += $result->score ?? 0;
                    $scoreCount++;
                }
                
                if ($scoreCount > 0) {
                    $avgScore = $totalScore / $scoreCount;
                    $wardData[$ward]['total_sat']++;
                    $wardData[$ward]['total_score'] += $avgScore;
                    
                    // Determine division
                    if ($avgScore >= 75) $wardData[$ward]['grade_i']++;
                    elseif ($avgScore >= 65) $wardData[$ward]['grade_ii']++;
                    elseif ($avgScore >= 50) $wardData[$ward]['grade_iii']++;
                    elseif ($avgScore >= 30) $wardData[$ward]['grade_iv']++;
                    else $wardData[$ward]['grade_0']++;
                }
            }
        }

        // Calculate percentages and rankings
        $rankings = collect($wardData)->map(function($data) {
            $satPercentage = $data['total_registered'] > 0 
                ? round(($data['total_sat'] / $data['total_registered']) * 100, 2) 
                : 0;
                
            $i_iii = $data['grade_i'] + $data['grade_ii'] + $data['grade_iii'];
            $i_iii_percentage = $data['total_sat'] > 0 
                ? round(($i_iii / $data['total_sat']) * 100, 2) 
                : 0;
                
            $i_iv = $i_iii + $data['grade_iv'];
            $i_iv_percentage = $data['total_sat'] > 0 
                ? round(($i_iv / $data['total_sat']) * 100, 2) 
                : 0;
                
            $div_0_percentage = $data['total_sat'] > 0 
                ? round(($data['grade_0'] / $data['total_sat']) * 100, 2) 
                : 0;
                
            $average = $data['total_sat'] > 0 
                ? round($data['total_score'] / $data['total_sat'], 2) 
                : 0;
                
            // Calculate GPA
            $gpaPoints = ($data['grade_i'] * 5) + ($data['grade_ii'] * 4) + 
                        ($data['grade_iii'] * 3) + ($data['grade_iv'] * 2) + ($data['grade_0'] * 1);
            $gpa = $data['total_sat'] > 0 ? round($gpaPoints / $data['total_sat'], 5) : 0;
            
            // Determine overall grade
            $overallGrade = 'F';
            if ($average >= 75) $overallGrade = 'A';
            elseif ($average >= 65) $overallGrade = 'B';
            elseif ($average >= 50) $overallGrade = 'C';
            elseif ($average >= 30) $overallGrade = 'D';
            
            // Competency level
            $competencyLevel = match($overallGrade) {
                'A' => 'Grade A (Excellent)',
                'B' => 'Grade B (Very Good)',
                'C' => 'Grade C (Good)',
                'D' => 'Grade D (Satisfactory)',
                default => 'Grade F (Fail)'
            };

            return [
                'ward_name' => $data['ward_name'],
                'schools_count' => $data['schools_count'],
                'total_registered' => $data['total_registered'],
                'total_sat' => $data['total_sat'],
                'sat_percentage' => $satPercentage,
                'grade_i' => $data['grade_i'],
                'grade_ii' => $data['grade_ii'],
                'grade_iii' => $data['grade_iii'],
                'i_iii' => $i_iii,
                'i_iii_percentage' => $i_iii_percentage,
                'grade_iv' => $data['grade_iv'],
                'i_iv' => $i_iv,
                'i_iv_percentage' => $i_iv_percentage,
                'grade_0' => $data['grade_0'],
                'div_0_percentage' => $div_0_percentage,
                'average' => $average,
                'grade' => $overallGrade,
                'gpa' => $gpa,
                'competency_level' => $competencyLevel,
            ];
        })->values();

        // Sort by average score and add rank
        $rankings = $rankings->sortByDesc('average')->values();
        $rankings = $rankings->map(function($item, $index) {
            $item['rank'] = $index + 1;
            return $item;
        });

        // Calculate summary totals
        $summary = [
            'total_wards' => $rankings->count(),
            'total_schools' => $rankings->sum('schools_count'),
            'total_registered' => $rankings->sum('total_registered'),
            'total_sat' => $rankings->sum('total_sat'),
            'sat_percentage' => $rankings->sum('total_registered') > 0 
                ? round(($rankings->sum('total_sat') / $rankings->sum('total_registered')) * 100, 2) 
                : 0,
            'grade_i' => $rankings->sum('grade_i'),
            'grade_ii' => $rankings->sum('grade_ii'),
            'grade_iii' => $rankings->sum('grade_iii'),
            'i_iii' => $rankings->sum('i_iii'),
            'i_iii_percentage' => $rankings->sum('total_sat') > 0 
                ? round(($rankings->sum('i_iii') / $rankings->sum('total_sat')) * 100, 2) 
                : 0,
            'grade_iv' => $rankings->sum('grade_iv'),
            'i_iv' => $rankings->sum('i_iv'),
            'i_iv_percentage' => $rankings->sum('total_sat') > 0 
                ? round(($rankings->sum('i_iv') / $rankings->sum('total_sat')) * 100, 2) 
                : 0,
            'grade_0' => $rankings->sum('grade_0'),
            'div_0_percentage' => $rankings->sum('total_sat') > 0 
                ? round(($rankings->sum('grade_0') / $rankings->sum('total_sat')) * 100, 2) 
                : 0,
            'average' => $rankings->avg('average'),
            'grade' => 'C',
            'gpa' => $rankings->avg('gpa'),
            'competency_level' => 'Grade B (Very Good)',
        ];

        return Inertia::render('Emas/Reports/WardRanks', [
            'rankings' => $rankings,
            'summary' => $summary,
            'filters' => $filters,
        ]);
    }

    public function topStudentsFilters(): Response
    {
        return Inertia::render('Emas/Reports/TopStudentsFilters');
    }

    public function topStudents(Request $request): Response
    {
        $filters = [
            'region' => $request->input('region'),
            'district' => $request->input('district'),
            'report_type' => $request->input('report_type', 'best'),
            'school_type' => $request->input('school_type', 'all'),
            'gender' => $request->input('gender', 'all'),
            'limit' => $request->input('limit', 10),
            'year' => $request->input('year', date('Y')),
            'exam_type' => $request->input('exam_type', 'pre-national'),
        ];

        // Build query for candidates with results
        $query = EmasCandidate::with(['center', 'results.exam']);

        // Filter by school type
        if ($filters['school_type'] !== 'all') {
            $query->whereHas('center', function($q) use ($filters) {
                $q->where('ownership', strtoupper($filters['school_type']));
            });
        }

        // Filter by gender
        if ($filters['gender'] !== 'all') {
            $query->where('gender', strtoupper($filters['gender'][0])); // M or F
        }

        $candidates = $query->get();

        // Calculate average and prepare student data
        $students = $candidates->map(function($candidate) {
            $results = $candidate->results;
            $totalScore = 0;
            $subjectCount = 0;
            $subjects = [];

            foreach ($results as $result) {
                if ($result->exam) {
                    $subject = $result->exam->subject ?? 'Unknown';
                    $score = $result->score ?? 0;
                    $subjects[$subject] = $score;
                    $totalScore += $score;
                    $subjectCount++;
                }
            }

            $average = $subjectCount > 0 ? round($totalScore / $subjectCount, 2) : 0;

            // Determine grade
            $grade = 'F';
            if ($average >= 75) $grade = 'A';
            elseif ($average >= 65) $grade = 'B';
            elseif ($average >= 50) $grade = 'C';
            elseif ($average >= 30) $grade = 'D';

            return [
                'candidate_id' => $candidate->id,
                'candidate_number' => $candidate->candidate_number ?? 'N/A',
                'full_name' => $candidate->full_name,
                'gender' => $candidate->gender,
                'school_name' => $candidate->center->center_name ?? 'Unknown School',
                'average' => $average,
                'grade' => $grade,
                'subjects' => $subjects,
                'subject_count' => $subjectCount,
                'agct' => min(count($subjects), 7), // AGCT is usually number of subjects (max 7 for core)
                'avg' => min(count($subjects), 7), // Same as AGCT
            ];
        });

        // Filter out students with no results
        $students = $students->filter(function($student) {
            return $student['subject_count'] > 0;
        });

        // Sort based on report type
        if ($filters['report_type'] === 'best') {
            $students = $students->sortByDesc('average')->take($filters['limit']);
        } else {
            $students = $students->sortBy('average')->take($filters['limit']);
        }

        $students = $students->values();

        // Generate title
        $title = 'ILEMELA MC ';
        
        if ($filters['report_type'] === 'best') {
            $title .= 'TOP TEN BEST ';
        } else {
            $title .= 'TOP TEN WEAK ';
        }

        if ($filters['gender'] === 'male') {
            $title .= 'MALE STUDENTS ';
        } elseif ($filters['gender'] === 'female') {
            $title .= 'FEMALE STUDENTS ';
        } else {
            $title .= 'STUDENTS ';
        }

        if ($filters['school_type'] === 'government') {
            $title .= 'FOR GOVERNMENT SCHOOLS';
        } elseif ($filters['school_type'] === 'private') {
            $title .= 'FOR PRIVATE SCHOOLS';
        } else {
            $title .= 'OVERALL COUNCILWISE';
        }

        return Inertia::render('Emas/Reports/TopStudents', [
            'students' => $students,
            'filters' => $filters,
            'title' => $title,
        ]);
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
